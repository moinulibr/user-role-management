<?php

return [
    // ড্যাশবোর্ড
    [
        'title' => 'Business Dashboard',
        'route' => 'dashboard', // রুট নাম
        'icon' => 'mdi mdi-briefcase-account-outline',
        'permission' => 'dashboard.view', // এই পারমিশন ছাড়া দেখা যাবে না
        'is_active' => true, // ডিফল্ট হিসেবে active থাকতে পারে
    ],
    [
        'title' => 'Analytics Dashboard',
        'route' => 'analytics', // রুট নাম
        'icon' => 'mdi mdi-chart-line',
        'permission' => 'dashboard.view', // Analytics-ও ড্যাশবোর্ড পারমিশন দ্বারা সুরক্ষিত
    ],

    // ইউজার ও রোল ম্যানেজমেন্ট (সাবমেনু সহ একটি গ্রুপ)
    [
        'title' => 'Users',
        'icon' => 'mdi mdi-account-group',
        'permission' => 'users.manage', // এই সেকশন দেখতে ন্যূনতম এই পারমিশন লাগবে
        'submenu' => [
            [
                'title' => 'User Lists',
                'route' => 'admin.users.index',
                'permission' => 'users.manage',
            ],
            [
                'title' => 'User Create',
                'route' => 'admin.users.create',
                'permission' => 'users.create',
            ],
           /*  [
                'title' => 'User Role Assign',
                'route' => 'admin.users.assign',
                'permission' => 'users.assign',
            ], */
        ],
    ],

    // অন্যান্য মেনু আইটেম
    [
        'title' => 'Tickets',
        'route' => 'tickets.index',
        'icon' => 'mdi mdi-truck-delivery',
        'permission' => 'tickets.view',
    ],
    [
        'title' => 'Users',
        'route' => 'admin.users.index',
        'icon' => 'mdi mdi-settings',
        'permission' => 'users.manage',
    ],
    [
        'title' => 'Roles',
        'route' => 'admin.roles.index',
        'icon' => 'mdi mdi-settings',
        'permission' => 'roles.manage',
    ],
    [
        'title' => 'Settings',
        'route' => 'admin.settings.index',
        'icon' => 'mdi mdi-settings',
        'permission' => 'settings.manage',
    ],
];
