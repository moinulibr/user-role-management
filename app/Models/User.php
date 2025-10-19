<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasCustomPermissions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasCustomPermissions;

    protected $with = ['roles'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'provider_id',
        'provider',
        'password',
        'profile_picture'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * profile_picture অ্যাট্রিবিউটের জন্য Accessor ডিফাইন করা।
     *
     * এটি ডেটাবেস থেকে যখনই profile_picture অ্যাক্সেস করা হবে, তখনই তা 
     * সম্পূর্ণ URL-এ রূপান্তর করে দেবে।
     * * Laravel 9+ এর জন্য:
     */
    protected function profilePicture(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                // ১. যদি কোনো পাথ সেভ করা না থাকে বা ফাইলটি Google/Facebook থেকে আসে
                if (!$value || Str::startsWith($value, ['http', 'https'])) {
                    // URL হলে সরাসরি URL রিটার্ন করবে
                    return $value;
                }

                // ২. আপেক্ষিক পাথকে সম্পূর্ণ পাবলিক URL-এ রূপান্তর
                $url = Storage::disk('public')->url($value);

                // *** It's removing to duble slass ***
                // নিশ্চিত করা হচ্ছে যে http://example.com//storage এর মতো ডাবল স্ল্যাশ যেন না থাকে।
                $cleanedUrl = Str::replaceFirst('//storage', '/storage', $url);

                return $cleanedUrl;
            },
            // No need to Mutator
        );
    }
}
