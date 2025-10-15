<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SidebarComposer
{
    /**
     * View Composer-এর মূল মেথড
     * এটি ভিউ রেন্ডার হওয়ার আগে ডেটা সরবরাহ করে।
     * * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // ১. ব্যবহারকারী লগইন করা আছে কিনা চেক করুন
        $user = Auth::user();
        if (!$user) {
            // যদি কেউ লগইন না করে থাকে, তবে কোনো মেনু দেখানোর দরকার নেই (বা শুধু পাবলিক মেনু দেখান)
            $view->with('items', []);
            return;
        }

        // ২. কনফিগারেশন থেকে মেনু ডেটা লোড করুন
        $menuItems = config('sidebar');
        $filteredItems = [];

        // ৩. মেনু আইটেমগুলো লুপ করুন এবং পারমিশন অনুযায়ী ফিল্টার করুন
        foreach ($menuItems as $item) {
            // সেকশন (Section) আইটেম
            if (isset($item['section'])) {
                $sectionItems = [];
                // সেকশনের ভেতরের আইটেমগুলো ফিল্টার করুন
                foreach ($item['items'] as $subItem) {
                    $permission = $subItem['permission'] ?? null;

                    // যদি কোনো পারমিশন সেট করা না থাকে OR যদি ব্যবহারকারীর সেই পারমিশন থাকে
                    if (!$permission || $user->can($permission)) {
                        $sectionItems[] = $subItem;
                    }
                }

                // যদি সেকশনের ভেতরে একটিও আইটেম থাকে, তবে সেকশন হেডার সহ যোগ করুন
                if (!empty($sectionItems)) {
                    $item['items'] = $sectionItems;
                    $filteredItems[] = $item;
                }
            } else {
                // রেগুলার আইটেম
                $permission = $item['permission'] ?? null;

                // যদি কোনো পারমিশন সেট করা না থাকে OR যদি ব্যবহারকারীর সেই পারমিশন থাকে
                if (!$permission || $user->can($permission)) {
                    $filteredItems[] = $item;
                }
            }
        }

        // ৪. ফিল্টার করা মেনু অ্যারেটি Blade ফাইলের জন্য 'items' নামে পাস করুন
        $view->with('items', $filteredItems);
    }
}
