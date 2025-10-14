<?php
return [
    'modules' => [
        //'products' => ['view', 'create', 'edit', 'delete'] //  (like 'products.create')
        /* 'products' => [
            'view',
            'create',
            'edit',
            'delete',
        ],
        'orders' => [
            'view',
            'create',
            'edit',
            'status_update',
            'send_email',
        ],
        'settings' => [
            'manage',
        ],
        'users' => [
            'manage',
        ], */
        'tickets' => [
            'create',        // tickets.create, tickets.store
            'sync_single',   // tickets.sync-single
            'sync_all',      // tickets.sync-all
            //'view_private',  // কাস্টম পারমিশন: শুধু নিজের টিকিট দেখার জন্য
        ],
        'users' => [
            'manage',       // users.manage (সকল User/Role Management এর জন্য গ্লোবাল গেট)
            'assign',       // users.assign (User কে Role assign করার জন্য)
        ],

        'roles' => [
            'manage',       // roles.manage (সকল Role Management এর জন্য গ্লোবাল গেট, যদি লাগে)
            'assign',       // roles.assign (Role কে Permission assign করার জন্য)
        ],
    ]
];
