<?php

namespace Controllers\Moderation;

use App\Entities\User;
use App\Models\Factories\UserFactory;
use Exception;
use Tests\Support\Database\Seeds\TestDataSeeder;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class ReportsControllerTest extends TestCase
{
    protected $seed = TestDataSeeder::class;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = fake(UserFactory::class, [
            'password' => 'secret123',
        ]);
        $this->user = $user;
        $this->user->addGroup('admin');
    }

    /**
     * @throws Exception
     */
    public function testListGuest()
    {
        $response = $this->withHeaders([
            'HX-Request' => 'true',
        ])->get(route_to('moderate-threads'));

        $response->assertRedirect();
        $response->assertHeader('HX-Location', json_encode(['path' => '/display-error']));
        $response->assertSessionHas('message', 'You are not allowed to moderate threads.');
    }

    /**
     * @throws Exception
     */
    public function testActionGuest()
    {
        $response = $this->withHeaders([
            csrf_header() => csrf_hash(),
            'HX-Request'  => 'true',
        ])->post(route_to('moderate-action', 'thread'));

        $response->assertRedirect();
        $response->assertHeader('HX-Location', json_encode(['path' => '/display-error']));
        $response->assertSessionHas('message', 'You are not allowed to moderate threads.');
    }

    /**
     * @throws Exception
     */
    public function testLogsGuest()
    {
        $response = $this->withHeaders([
            'HX-Request' => 'true',
        ])->get(route_to('moderate-logs'));

        $response->assertRedirect();
        $response->assertHeader('HX-Location', json_encode(['path' => '/display-error']));
        $response->assertSessionHas('message', 'You are not allowed to see moderation logs.');
    }

    /**
     * @throws Exception
     */
    public function testList()
    {
        $response = $this->withHeaders([
            'HX-Request' => 'true',
        ])->actingAs($this->user)->get(route_to('moderate-threads'));

        $response->assertSee('Moderation queue - Threads');
    }

    /**
     * @throws Exception
     */
    public function testActionValidationError()
    {
        $response = $this->withHeaders([
            csrf_header() => csrf_hash(),
            'HX-Request'  => 'true',
        ])->actingAs($this->user)->post(route_to('moderate-action', 'thread'), [
            'action' => 'invalid',
            'items'  => [],
        ]);

        $response->assertSee('The action field must be one of: approve,deny,ignore.');
        $response->assertSee('The items.* field is required.');
    }

    /**
     * @throws Exception
     */
    public function testActionApprove()
    {
        $response = $this->withHeaders([
            csrf_header() => csrf_hash(),
            'HX-Request'  => 'true',
        ])->actingAs($this->user)->post(route_to('moderate-action', 'thread'), [
            'action' => 'approve',
            'items'  => [1],
        ]);

        $response->assertSee('Your action has been successful', 'span');
        $this->dontSeeInDatabase('moderation_reports', [
            'resource_id'   => 1,
            'resource_type' => 'thread',
        ]);
    }

    /**
     * @throws Exception
     */
    public function testActionDeny()
    {
        $response = $this->withHeaders([
            csrf_header() => csrf_hash(),
            'HX-Request'  => 'true',
        ])->actingAs($this->user)->post(route_to('moderate-action', 'thread'), [
            'action' => 'deny',
            'items'  => [1],
        ]);

        $response->assertSee('Your action has been successful', 'span');
        $this->dontSeeInDatabase('moderation_reports', [
            'resource_id'   => 1,
            'resource_type' => 'thread',
        ]);
        $this->dontSeeInDatabase('threads', [
            'id'         => 1,
            'deleted_at' => null,
        ]);
    }

    /**
     * @throws Exception
     */
    public function testActionIgnore()
    {
        $response = $this->withHeaders([
            csrf_header() => csrf_hash(),
            'HX-Request'  => 'true',
        ])->actingAs($this->user)->post(route_to('moderate-action', 'thread'), [
            'action' => 'ignore',
            'items'  => [1],
        ]);

        $response->assertSee('Your action has been successful', 'span');
        $this->seeInDatabase('moderation_reports', [
            'resource_id'   => 1,
            'resource_type' => 'thread',
        ]);
        $this->seeInDatabase('moderation_ignored', [
            'moderation_report_id' => 1,
            'user_id'              => $this->user->id,
        ]);
    }

    /**
     * @throws Exception
     */
    public function testLogs()
    {
        $response = $this->withHeaders([
            'HX-Request' => 'true',
        ])->actingAs($this->user)->get(route_to('moderate-logs'));

        $response->assertSee('Moderation logs');
    }
}
