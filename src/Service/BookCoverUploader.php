<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class BookCoverUploader
{
    private string $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file, string $slug): string
    {
        $timestamp = time();
        $extension = $file->guessExtension();
        $filename = $slug . '-' . $timestamp . '.' . $extension;
        $file->move($this->targetDirectory, $filename);
        return $filename;
    }

    public function remove(string $filename): void
    {
        $path = $this->targetDirectory . '/' . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }
}

