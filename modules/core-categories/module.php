<?php

return [
    'name'          => 'core-categories',
    'label'         => 'Categories',
    'version'       => '1.0.0',
    'description'   => 'Core category management functionality',
    'author'        => 'Forum Team',
    'type'          => 'core',
    'required'      => true,

    'provider'      => 'Vox\Categories\CategoryProvider',
    'namespace'     => 'Vox\Categories',

    'min_php'       => '8.2',
    'min_framework' => '4.5',
    'dependencies'  => [],
];
