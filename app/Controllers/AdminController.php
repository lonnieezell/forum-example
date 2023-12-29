<?php

namespace App\Controllers;

/**
 * A base controller for the admin area.
 * All admin controllers should extend this class.
 */
class AdminController extends BaseController
{
    protected string $theme = 'admin';
}
