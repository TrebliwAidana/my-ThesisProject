<?php

/**
 * Role Configuration
 * 
 * Defines role-based mappings and hierarchies for member management
 */

return [
    /**
     * Role Hierarchy Mappings
     * Maps each role to the roles they can create/manage
     */
    'hierarchy' => [
        'SysAdmin' => ['all'],
        'SA'       => ['all'],
        'CA'       => ['OA', 'OO', 'OM'],
        'OA'       => ['OM'],
        'OO'       => ['OM'],
    ],

    /**
     * Position Mappings by Role
     * Defines which positions are valid for each role abbreviation
     */
    'positions' => [
        'System Administrator' => [],
        'Supreme Admin'        => ['President', 'Vice President', 'Secretary', 'Treasurer'],
        'Supreme Officer'      => ['Secretary', 'Treasurer'],
        'Org Admin'            => ['President', 'Vice President', 'Secretary', 'Treasurer'],
        'Org Officer'          => ['Secretary', 'Treasurer'],
        'Club Adviser'         => [],
        'Org Member'           => [],
    ],

    /**
     * Year Level Requirements by Role
     * Defines which roles require year_level to be set
     * If empty array, year_level should be null
     */
    'requires_year_level' => [
        'System Administrator' => [],
        'Supreme Admin'        => ['Grade 11', 'Grade 12'],
        'Supreme Officer'      => ['Grade 11', 'Grade 12'],
        'Org Admin'            => ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
        'Org Officer'          => ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
        'Club Adviser'         => [],
        'Org Member'           => ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'],
    ],

    /**
     * Abbreviations Mapping
     * For quick lookup of abbreviations
     */
    'abbreviations' => [
        'SysAdmin'             => 'System Administrator',
        'SA'                   => 'Supreme Admin',
        'SO'                   => 'Supreme Officer',
        'CA'                   => 'Org Admin',
        'OA'                   => 'Org Admin',
        'OO'                   => 'Org Officer',
        'OM'                   => 'Org Member',
        'CA2'                  => 'Club Adviser',
    ],
];
