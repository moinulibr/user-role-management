<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploader
{
    protected string $disk = 'public';
    protected string $rootFolder = 'uploads';

    /**
     * ডিস্ক সেট করুন (যেমন 's3' বা 'public').
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * একক ফাইল আপলোড করুন।
     *
     * @param UploadedFile $file আপলোড হওয়া ফাইল ইনস্ট্যান্স
     * @param string $folder সাব-ফোল্ডারের নাম (যেমন 'users' বা 'products')
     * @return string|null সেভ করা ফাইলের আপেক্ষিক পাথ (storage/uploads/users/example.jpg)
     */
    public function upload(UploadedFile $file, string $folder): ?string
    {
        // ফাইল নেম ক্লিন এবং ইউনিক করা
        $originalExtension = $file->getClientOriginalExtension();
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fileName = $baseName . '-' . time() . '.' . $originalExtension;

        // টার্গেট পাথ তৈরি (uploads/users)
        $path = $this->rootFolder . '/' . trim($folder, '/');

        try {
            // ফাইল স্টোর করা: এটি একটি রিলেটিভ পাথ রিটার্ন করে
            $filePath = Storage::disk($this->disk)->putFileAs($path, $file, $fileName);

            return $filePath;
        } catch (\Exception $e) {
            \Log::error("File Upload Failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * একটি ফাইল ডিলিট করুন।
     */
    public function delete(?string $filePath): bool
    {
        if (!$filePath) {
            return false;
        }

        if (Storage::disk($this->disk)->exists($filePath)) {
            return Storage::disk($this->disk)->delete($filePath);
        }

        return false;
    }
}
