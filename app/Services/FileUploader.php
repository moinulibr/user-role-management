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
     * 🚀 প্রধান আপলোড মেথড: UploadedFile বা Base64 স্ট্রিং উভয়ই হ্যান্ডেল করে।
     * * @param mixed $fileData UploadedFile ইনস্ট্যান্স, অথবা Base64 স্ট্রিং।
     * @param string $folder সাব-ফোল্ডারের নাম (যেমন 'users' বা 'products')
     * @return string|null সেভ করা ফাইলের আপেক্ষিক পাথ
     */
    public function upload($fileData, string $folder): ?string
    {
        if ($fileData instanceof UploadedFile) {
            // যদি এটি একটি সাধারণ ফাইল আপলোড হয়
            return $this->uploadFromFile($fileData, $folder);
        } elseif (is_string($fileData) && Str::startsWith($fileData, 'data:')) {
            // যদি এটি একটি Base64 ডেটা স্ট্রিং হয়
            return $this->uploadFromBase64($fileData, $folder);
        }

        // অন্য কোনো ইনপুট হলে (যেমন null বা ভুল ফরম্যাট)
        return null;
    }


    // --- প্রাইভেট আপলোড মেথড ---

    /**
     * প্রাইভেট মেথড: UploadedFile ইনস্ট্যান্স সেভ করে।
     */
    private function uploadFromFile(UploadedFile $file, string $folder): ?string
    {
        $originalExtension = $file->getClientOriginalExtension();
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fileName = $baseName . '-' . time() . '.' . $originalExtension;

        $path = $this->rootFolder . '/' . trim($folder, '/');

        try {
            $filePath = Storage::disk($this->disk)->putFileAs($path, $file, $fileName);
            return $filePath;
        } catch (\Exception $e) {
            \Log::error("File Upload Failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * প্রাইভেট মেথড: Base64 ডেটা সেভ করে।
     */
    private function uploadFromBase64(string $base64Data, string $folder): ?string
    {
        // Base64 স্ট্রিং থেকে ফাইল টাইপ বের করা
        if (!preg_match('/^data:(\w+)\/(\w+);base64,/', $base64Data, $type)) {
            return null;
        }

        $fileData = substr($base64Data, strpos($base64Data, ',') + 1);
        $fileType = $type[2] ?? 'png';
        $fileBinary = base64_decode($fileData);

        $fileName = Str::random(40) . '-' . time() . '.' . $fileType;
        $path = $this->rootFolder . '/' . trim($folder, '/') . '/' . $fileName;

        try {
            // বাইনারি ডেটা সরাসরি ডিস্কে সেভ করা
            Storage::disk($this->disk)->put($path, $fileBinary);
            return $path;
        } catch (\Exception $e) {
            \Log::error("Base64 Upload Failed: " . $e->getMessage());
            return null;
        }
    }

    // --- ডিলিট মেথড (অপরিবর্তিত) ---

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
