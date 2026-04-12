<?php

// config/permissions.php
return [

    'roles' => [
        'Adviser' => [
            'manage-members',
            'manage-documents',
            'manage-budgets',
            'manage-users',
            'manage-settings',
            'manage-roles',
            'manage-permissions',
            'view-reports',
        ],
        'Officer' => [
            'manage-members',
            'manage-documents',
            'view-budgets',
            'create-budgets',
            'review-budgets',
            'view-reports',
        ],
        'Auditor' => [
            'view-members',
            'view-documents',
            'view-budgets',
            'view-reports',
        ],
        'Member' => [
            'view-members',
            'view-organization-info',
        ],
    ],

    // Sidebar Menu Config
    'menu' => [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'permission' => null,
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
            'label' => 'Adviser',
            'route' => null,
            'permission' => 'manage-users',
            'submenu' => [
                ['label' => 'User Management', 'route' => 'admin.users.index', 'permission' => 'manage-users'],
                ['label' => 'Roles', 'route' => 'admin.roles.index', 'permission' => 'manage-roles'],
                ['label' => 'Permissions', 'route' => 'admin.permissions.index', 'permission' => 'manage-permissions'],
                ['label' => 'Settings', 'route' => 'settings.index', 'permission' => 'manage-settings'],
                ['label' => 'Audit Logs', 'route' => 'audit.logs', 'permission' => 'view-reports'],
            ],
        ],
    ],
    'visibility' => [
        'System Administrator' => ['*'],
        'Supreme Admin'        => ['*'],
        'Club Adviser'         => ['view', 'create', 'edit'],
        'Org Admin'            => ['view', 'create', 'edit', 'delete', 'approve'],
        'Org Officer'          => ['view', 'create', 'edit'],
        'Org Member'           => ['view'],
    ],
    
];