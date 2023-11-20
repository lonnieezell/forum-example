<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://github.com/codeigniter4/shield/blob/develop/docs/quickstart.md#change-available-groups for more info
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Complete control of the site.',
        ],
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Day to day administrators of the site.',
        ],
        'moderators' => [
            'title'       => 'Moderators',
            'description' => 'Day to day moderators of the site.',
        ],
        'developer' => [
            'title'       => 'Developer',
            'description' => 'Site programmers.',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'General users of the site. Often customers.',
        ],
        'beta' => [
            'title'       => 'Beta User',
            'description' => 'Has access to beta-level features.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system. Each system is defined
     * where the key is the
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [
        'admin.access'             => 'Can access the sites admin area',
        'admin.settings'           => 'Can access the main site settings',
        'users.manage-admins'      => 'Can manage other admins',
        'users.create'             => 'Can create new non-admin users',
        'users.edit'               => 'Can edit existing non-admin users',
        'users.delete'             => 'Can delete existing non-admin users',
        'users.changePassword'     => 'Can change a user\'s password',
        'users.deleteAvatar'       => 'Can delete a user\'s avatar',
        'users.twoFactorAuthEmail' => 'Can change a users\'s 2FA settings',
        'beta.access'              => 'Can access beta-level features',
        'categories.edit'          => 'Can edit categories',
        'categories.delete'        => 'Can delete categories',
        'categories.create'        => 'Can create categories',
        'categories.moderate'      => 'Can moderate categories',
        'threads.edit'             => 'Can edit threads',
        'threads.delete'           => 'Can delete threads',
        'threads.create'           => 'Can create threads',
        'threads.report'           => 'Can report threads',
        'threads.moderate'         => 'Can moderate threads',
        'posts.edit'               => 'Can edit posts',
        'posts.delete'             => 'Can delete posts',
        'posts.create'             => 'Can create posts',
        'posts.report'             => 'Can report posts',
        'posts.moderate'           => 'Can moderate posts',
        'images.upload'            => 'Can upload images',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'users.*',
            'beta.*',
            'categories.*',
            'posts.*',
            'images.*',
        ],
        'admin' => [
            'admin.access',
            'users.create',
            'users.edit',
            'users.delete',
            'users.changePassword',
            'users.deleteAvatar',
            'beta.access',
            'categories.*',
            'posts.*',
            'images.*',
        ],
        'developer' => [
            'admin.access',
            'admin.settings',
            'users.create',
            'users.edit',
            'beta.access',
            'images.upload',
        ],
        'user' => [
            'threads.create',
            'threads.report',
            'posts.create',
            'posts.report',
            'images.upload',

            // temp
            'threads.moderate',
            'posts.moderate',
        ],
        'beta' => [
            'beta.access',
        ],
    ];
}
