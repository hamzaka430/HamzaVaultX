<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    public static $wrap = false;

    /**
     * The total file size holder.
     *
     * @var int
     */
    public $totalSize = 0;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $size = $this->getTotalSize($this);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'path' => $this->path,
            'parent_id' => $this->parent_id,
            'is_folder' => $this->is_folder,
            'mime' => $this->mime,
            'size' => $this->formatBytes($size),
            'owner' => $this->owner,
            'is_favourite' => (bool) $this->starred,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_at' => $this->deleted_at,
        ];
    }

    /**
     * Format bytes to human readable size.
     *
     * @param  int  $bytes
     * @param  int  $precision
     * @return string
     */
    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }

    /**
     * Get the total size in bytes for the given file or folder.
     *
     * @param  \App\Models\File  $content
     * @return int
     */
    public function getTotalSize($content)
    {
        if ($content->is_folder) {
            foreach ($content->children as $child) {
                if ($child->is_folder) {
                    $this->getTotalSize($child);
                } else {
                    $this->totalSize += (int) ($child->size ?? 0);
                }
            }
        } else {
            $this->totalSize += (int) ($content->size ?? 0);
        }

        return $this->totalSize;
    }
}
