<?php

return [
    'name'          => 'core-discussions',
    'label'         => 'Discussions',
    'version'       => '1.0.0',
    'description'   => 'Core discussion management functionality',
    'author'        => 'Forum Team',
    'type'          => 'core',
    'required'      => true,

    'provider'      => 'Koru\Discussions\CategoryProvider',
    'namespace'     => 'Koru\Discussions',

    'min_php'       => '8.2',
    'min_framework' => '4.5',
    'dependencies'  => ['core-users', 'core-categories'],
];
