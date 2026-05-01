<?php

return [

    /**
     * Role Hierarchy Mappings
     * Only real permission roles kept
     */
    'hierarchy' => [
        'SysAdmin' => ['all'],
        'SA'       => ['all'],

        // Club Adviser can manage basic roles
        'CA' => ['TR', 'AU', 'G'],
    ],

    /**
     * Position Mappings by Role
     */
    'positions' => [
        'System Administrator' => [],
        'Club Adviser'         => [],
        'Treasurer'            => [],
        'Auditor'              => [],
        'Guest'                => [],
    ],

    /**
     * Year Level Requirements
     */
    'requires_year_level' => [
        'System Administrator' => [],
        'Club Adviser'         => [],

        // Student roles
        'Treasurer' => ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
        'Auditor'   => ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],

        // Guest typically no year level
        'Guest' => [],
    ],

    /**
     * Abbreviations Mapping
     */
    'abbreviations' => [
        'SysAdmin' => 'System Administrator',
        'SA'       => 'System Administrator',

        'CA' => 'Club Adviser',

        'TR' => 'Treasurer',
        'AU' => 'Auditor',
        'G'  => 'Guest',
    ],
];