<?php

namespace Tests\Controllers;

use Exception;
use Tests\Support\Database\Seeds\TestDataSeeder;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class MemberControllerTest extends TestCase
{
    protected $seed = TestDataSeeder::class;

    /**
     * @throws Exception
     */
    public function testShowMembers()
    {
        $response = $this->get('members');

        $response->assertOK();
        $response->assertSeeElement('#member-search');
        $response->assertDontSee('Sorry, there are no users to display');
        $response->assertSee('testuser1');
        $response->assertSee('testuser2');
        $response->assertSee('testuser3');
    }

    /**
     * @throws Exception
     */
    public function testShowMembersRoleAdmin()
    {
        $params = [
            'search' => [
                'role' => 'admin',
            ],
        ];
        $response = $this->get('members', $params);

        $response->assertOK();
        $response->assertSeeElement('#member-search');
        $response->assertDontSee('Sorry, there are no users to display');
        $response->assertDontSee('testuser1');
        $response->assertDontSee('testuser3');
        $response->assertSee('testuser2');
    }

    /**
     * @throws Exception
     */
    public function testShowMembersRoleBeta()
    {
        $params = [
            'search' => [
                'role' => 'beta',
            ],
        ];
        $response = $this->get('members', $params);

        $response->assertOK();
        $response->assertSeeElement('#member-search');
        $response->assertSee('Sorry, there are no users to display');
    }

    /**
     * @throws Exception
     */
    public function testShowMembersTypeNew()
    {
        $params = [
            'search' => [
                'type' => 'new',
            ],
        ];
        $response = $this->get('members', $params);

        $response->assertOK();
        $response->assertSeeElement('#member-search');
        $response->assertDontSee('Sorry, there are no users to display');
        $response->assertSee('testuser3');
    }
}
