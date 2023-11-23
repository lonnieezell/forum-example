<?php

namespace Tests\Controllers;

use App\Entities\User;
use App\Models\Factories\UserFactory;
use Config\Forum;
use Exception;
use Tests\Support\Database\Seeds\TestDataSeeder;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class ReportControllerTest extends TestCase
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
    public function testReportGetGuest()
    {
        $response = $this->withHeaders([
            'HX-Request' => 'true',
        ])->get(route_to('thread-report', 1));

        $response->assertSee('You do not have permission to report threads');
    }

    /**
     * @throws Exception
     */
    public function testReportPostGuest()
    {
        $response = $this->withHeaders([
            csrf_header() => csrf_hash(),
            'HX-Request'  => 'true',
        ])->post(route_to('thread-report', 1), [
            'comment' => 'testing',
        ]);

        $response->assertSee('You do not have permission to report threads');
    }

    /**
     * @throws Exception
     */
    public function testReportGet()
    {
        $response = $this->withHeaders([
            'HX-Request' => 'true',
        ])->actingAs($this->user)->get(route_to('thread-report', 1));

        $response->assertSee('Report Thread');
    }

    /**
     * @throws Exception
     */
    public function testReportPost()
    {
        $response = $this->withHeaders([
            csrf_header() => csrf_hash(),
            'HX-Request'  => 'true',
        ])->actingAs($this->user)->post(route_to('thread-report', 1), [
            'comment' => 'testing',
        ]);

        $response->assertSee('Your report has been submitted. Thank you.');
    }

    /**
     * @throws Exception
     */
    public function testReportPostEmptyComment()
    {
        $response = $this->withHeaders([
            csrf_header() => csrf_hash(),
            'HX-Request'  => 'true',
        ])->actingAs($this->user)->post(route_to('thread-report', 1), [
        ]);

        $response->assertSee('The comment field is required.');
    }

    /**
     * @throws Exception
     */
    public function testReportPostTooManyReports()
    {
        config(Forum::class)->maxReportsPerDey = 1;
        service('throttler')->check(md5('report-' . $this->user->id), config(Forum::class)->maxReportsPerDey, DAY);

        $response = $this->withHeaders([
            csrf_header() => csrf_hash(),
            'HX-Request'  => 'true',
        ])->actingAs($this->user)->post(route_to('thread-report', 1), [
            'comment' => 'testing',
        ]);

        $response->assertSee('You have reached the maximum number of reports for today.');
    }
}
