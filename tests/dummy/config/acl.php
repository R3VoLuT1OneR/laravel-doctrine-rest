<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */
    'roles'         => [
        'entity' => \Tests\App\Entities\Role::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | Available drivers: config|doctrine
    | When set to config, add the permission names to list
    |
    */
    'permissions'   => [
        'driver' => 'config',
        'entity' => LaravelDoctrine\ACL\Permissions\Permission::class,
        'list'   => [

        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Organisations
    |--------------------------------------------------------------------------
    */
    'organisations' => [
        'entity' => App\Organisation::class,
    ],
];
