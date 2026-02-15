<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileActionsRequest;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Donwload the file(s) or folder(s) from My Files section.
     *
     * @return array|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function fromMyFiles(FileActionsRequest $request)
    {
        $payload = $request->validated();
        $parent = $request->parent;

        $all = $payload['all'] ?? false;
        $ids = $payload['ids'] ?? [];

        if (! $all && empty($ids)) {
            return ['message' => 'Please select at least one file or one folder to download.'];
        }

        if ($all) {
            return $this->downloadAsZip($parent->children, $parent->name.'.zip');
        }

        return $this->handleDownload($ids, $parent->name);
    }

    /**
     * Donwload the file(s) or folder(s) from shared with me page.
     *
     * @return array|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function sharedWithMe(FileActionsRequest $request)
    {
        $payload = $request->validated();

        $all = $payload['all'] ?? false;
        $ids = $payload['ids'] ?? [];

        if (! $all && empty($ids)) {
            return ['message' => 'Please select at least one file or one folder to download.'];
        }

        $zipFileName = 'shared-with-me';
        if ($all) {
            $files = File::getSharedWithMe()->get();

            return $this->downloadAsZip($files, $zipFileName.'.zip');
        }

        return $this->handleDownload($ids, $zipFileName);
    }

    /**
     * Donwload the file(s) or folder(s) from shared by me page.
     *
     * @return array|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function sharedByMe(FileActionsRequest $request)
    {
        $payload = $request->validated();

        $all = $payload['all'] ?? false;
        $ids = $payload['ids'] ?? [];

        if (! $all && empty($ids)) {
            return ['message' => 'Please select at least one file or one folder to download.'];
        }

        $zipFileName = 'shared-by-me';
        if ($all) {
            $files = File::getSharedByMe()->get();

            return $this->downloadAsZip($files, $zipFileName.'.zip');
        }

        return $this->handleDownload($ids, $zipFileName);
    }

    /**
     * Handle the download for given file IDs.
     *
     * @param  array  $ids
     * @param  string  $zipName
     * @return array|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function handleDownload($ids, $zipName)
    {
        if (count($ids) === 1) {
            $file = File::find($ids[0]);
            if ($file->is_folder) {
                if ($file->children->count() === 0) {
                    return ['message' => 'The folder is empty.'];
                }

                return $this->downloadAsZip($file->children, $file->name.'.zip');
            }

            // Single file — stream directly from storage (works with S3 and local)
            return $this->streamSingleFile($file);
        }

        $files = File::whereIn('id', $ids)->get();

        return $this->downloadAsZip($files, $zipName.'.zip');
    }

    /**
     * Stream a single file directly from the storage disk.
     *
     * @param  \App\Models\File  $file
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function streamSingleFile(File $file)
    {
        // Handle note files
        if ($file->type === 'note') {
            $filename = $file->name . '.txt';
            $content = $file->note_content ?? '';

            return response()->streamDownload(function () use ($content) {
                echo $content;
            }, $filename, [
                'Content-Type' => 'text/plain',
            ]);
        }

        return Storage::disk('r2')->download($file->storage_path, $file->name);
    }

    /**
     * Download multiple files/folders as a zip archive.
     * Uses a temp file so it works on both local and cloud (S3) storage.
     *
     * @param  \Illuminate\Support\Collection  $files
     * @param  string  $zipFileName
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function downloadAsZip($files, $zipFileName)
    {
        $tempZipPath = tempnam(sys_get_temp_dir(), 'zip_').'.zip';

        $zip = new \ZipArchive();
        if ($zip->open($tempZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $this->addFilesToZip($zip, $files);
        }
        $zip->close();

        return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Add the given files into the provided zip archive.
     * Streams file content from Storage (works with S3 and local).
     *
     * @param  \ZipArchive  $zip
     * @param  \Illuminate\Support\Collection  $files
     * @param  string  $ancestors
     * @return void
     */
    private function addFilesToZip($zip, $files, $ancestors = '')
    {
        $tempFiles = [];
        
        foreach ($files as $file) {
            if ($file->is_folder) {
                $this->addFilesToZip($zip, $file->children, $ancestors.$file->name.'/');
            } else {
                // Handle notes differently
                if ($file->type === 'note') {
                    $zip->addFromString($ancestors.$file->name.'.txt', $file->note_content ?? '');
                } else {
                    // Stream from any storage driver into a local temp file for zipping
                    $tempFile = tempnam(sys_get_temp_dir(), 'dl_');
                    file_put_contents($tempFile, Storage::disk('r2')->get($file->storage_path));
                    $zip->addFile($tempFile, $ancestors.$file->name);
                    $tempFiles[] = $tempFile;
                }
            }
        }
        
        // Clean up temp files after they're added to zip
        register_shutdown_function(function () use ($tempFiles) {
            foreach ($tempFiles as $tempFile) {
                if (file_exists($tempFile)) {
                    @unlink($tempFile);
                }
            }
        });
    }
}
