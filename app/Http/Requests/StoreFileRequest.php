<?php

namespace App\Http\Requests;

class StoreFileRequest extends ParentIdBaseRequest
{
    /**
     * Prepare the data for validation
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $paths = array_filter($this->relative_paths ?? [], fn ($f) => $f != null);

        $this->merge([
            'file_paths' => $paths,
            'folder_name' => $this->detectFolderName($paths),
        ]);
    }

    /**
     * Do something with the data that passed the validation test.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $data = $this->validated();

        $this->replace([
            'file_tree' => $this->buildFileTree($this->file_paths, $data['files']),
        ]);
    }

    /**
     * Detect the folder name for the given path.
     *
     * @param  array  $paths
     * @return string
     */
    public function detectFolderName($paths)
    {
        if (! $paths) {
            return null;
        }

        $parts = explode('/', $paths[0]);

        return $parts[0];
    }

    /**
     * Build the file tree for the given paths and files.
     *
     * @param  array  $filePaths
     * @param  array  $files
     * @return array
     */
    private function buildFileTree($filePaths, $files)
    {
        $filePaths = array_slice($filePaths, 0, count($files));
        $filePaths = array_filter($filePaths, fn ($f) => $f != null);

        $tree = [];

        foreach ($filePaths as $index => $filePath) {
            $parts = explode('/', $filePath);

            $currentNode = &$tree;
            foreach ($parts as $i => $part) {
                if (! isset($currentNode[$part])) {
                    $currentNode[$part] = [];
                }

                if ($i === count($parts) - 1) {
                    $currentNode[$part] = $files[$index];
                } else {
                    $currentNode = &$currentNode[$part];
                }

            }
        }

        return $tree;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'files.*' => [
                'required',
                'file',
                'max:51200',
                function ($attribute, $value, $fail) {
                    if ($value && str_starts_with($value->getMimeType(), 'video/')) {
                        $fail('Video files are not allowed.');
                    }
                },
            ],
            'folder_name' => [
                'nullable',
                'string',
            ],
        ]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'files.*.max' => 'The file size cannot exceed 50MB.',
            'files.*.file' => 'The file is invalid or could not be uploaded.',
        ];
    }
}
