<?php

namespace Tests\Support;

use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

abstract class TestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    use AuthenticationTesting;

    protected $namespace;

    protected function setUp(): void
    {
        // Load helpers that should be autoloaded
        helper(['auth', 'setting']);
        
        $this->resetServices();
        Services::routes()->loadRoutes();

        parent::setUp();
    }
}
