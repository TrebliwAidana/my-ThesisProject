<?php

// config/permissions.php
return [

    'roles' => [
        'admin' => [
            'manage-members',
            'manage-documents',
            'manage-budgets',
            'manage-users',
            'manage-settings',
        ],
        'officer' => [
            'manage-members',
            'manage-documents',
        ],
        'auditor' => [
            'view-documents',
            'view-budgets',
        ],
        'member' => [
            'view-documents',
        ],
    ],

    // 🔥 NEW: Sidebar Menu Config
    'menu' => [

        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'permission' => null, // everyone can see
        ],

        [
            'label' => 'Members',
            'route' => 'members.index',
            'permission' => 'manage-members',
        ],

        [
            'label' => 'Documents',
            'route' => 'documents.index',
            'permission' => 'manage-documents',
        ],

        [
            'label' => 'Budgets',
            'route' => 'budgets.index',
            'permission' => 'manage-budgets',
        ],

        [
            'label' => 'Users',
            'route' => 'admin.users',
            'permission' => 'manage-users',
        ],

        [
            'label' => 'Settings',
            'route' => 'settings.index',
            'permission' => 'manage-settings',
        ],
    ],
];