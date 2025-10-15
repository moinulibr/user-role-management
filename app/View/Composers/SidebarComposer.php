<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SidebarComposer
{
    /**
     * মেনু ডেটা লোড এবং ব্যবহারকারীর পারমিশন অনুযায়ী ফিল্টার করে।
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = Auth::user();

        // ১. যদি ইউজার অথেন্টিকেটেড না থাকে, খালি মেনু পাস করুন
        if (!$user) {
            $view->with('menuItems', []);
            return;
        }

        // config('sidebar') ফাইল থেকে ডেটা লোড করুন
        $menuConfig = config('sidebar');
        $filteredMenu = [];

        foreach ($menuConfig as $item) {
            $mainPermission = $item['permission'] ?? null;
            $canSeeMainItem = true;

            // প্রধান আইটেমের পারমিশন চেক করুন
            if ($mainPermission && !$user->can($mainPermission)) {
                $canSeeMainItem = false;
            }

            // ২. সাবমেনু হ্যান্ডেল করুন
            if (isset($item['submenu'])) {
                $filteredSubmenu = [];

                // সাবমেনুর প্রতিটি আইটেম ফিল্টার করুন
                foreach ($item['submenu'] as $subItem) {
                    $subPermission = $subItem['permission'] ?? null;

                    if (!$subPermission || $user->can($subPermission)) {
                        $filteredSubmenu[] = $subItem;
                    }
                }

                $item['submenu'] = $filteredSubmenu;

                // যদি সাবমেনুতে অন্তত একটি আইটেম থাকে, তবে প্রধান মেনুটি দেখান
                if (!empty($item['submenu'])) {
                    $filteredMenu[] = $item;
                    continue; // পরের আইটেমে যান
                }
            }

            // ৩. যদি সিঙ্গেল আইটেম হয় এবং পারমিশন থাকে, তবে যোগ করুন
            if ($canSeeMainItem && !isset($item['submenu'])) {
                $filteredMenu[] = $item;
            }
        }

        $view->with('menuItems', $filteredMenu);
    }
}
