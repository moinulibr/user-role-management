<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // Fillable properties
    protected $fillable = [
        'key',
        'value',
    ];

    // Key এবং Value ছাড়া অন্য কোনো ফিল্ডের পরিবর্তন যেন না হয়।
    // এখানে আমরা একটি সাহায্যকারী (Helper) ফাংশন যোগ করতে পারি
    // যা সেটিংস্ ভ্যালু সহজে Get বা Set করতে সাহায্য করবে।
    public static function getSetting($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setSetting($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
