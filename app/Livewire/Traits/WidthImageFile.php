<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Facades\Storage;

trait WidthImageFile
{
    public $imageFile = null;

    public function updatedImageFile()
    {
        $this->verifyTemporaryUrl();

        $this->validate([
            'imageFile' => 'image|max:1024',
        ]);
    }

    private function verifyTemporaryUrl()
    {
        try {
            $this->imageFile->temporaryUrl();
        } catch (\RuntimeException $exception) {
            $this->reset('imageFile');
        }
    }

    private function deleteFile($disk, $file)
    {
        if ($file && Storage::disk($disk)->exists($file)) {
            Storage::disk($disk)->delete($file);
        }
    }
}