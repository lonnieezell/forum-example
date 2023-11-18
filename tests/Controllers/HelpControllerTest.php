<?php

namespace Tests\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class HelpControllerTest extends TestCase
{
    public function testIndex()
    {
        $response = $this->get('help');

        $response->assertOK();
        $response->assertSee('Help Section', 'h1');
    }

    public function testIndexSearch()
    {
        $response = $this
            ->withHeaders([
                csrf_header() => csrf_hash(),
            ])
            ->post('help', [
                'search' => 'sample',
            ]);

        $response->assertOK();
        $response->assertSee('Search Results', 'h3');
    }

    public function testIndexSearchEmpty()
    {
        $response = $this
            ->withHeaders([
                'REFERER'     => url_to('pages'),
                'HX-Request'  => 'true',
                csrf_header() => csrf_hash(),
            ])
            ->post('help', [
                'search' => '',
            ]);

        $response->assertStatus(200);
    }

    public function testIndexSearchValidationError()
    {
        $response = $this
            ->withHeaders([
                'REFERER'     => url_to('pages'),
                'HX-Request'  => 'true',
                csrf_header() => csrf_hash(),
            ])
            ->post('help', [
                'search' => 'sam',
            ]);

        $response->assertOK();
        $response->assertSee('The search field must be at least 4 characters in length.');
    }

    public function testIndexSearchNoResults()
    {
        $response = $this
            ->withHeaders([
                csrf_header() => csrf_hash(),
            ])
            ->post('help', [
                'search' => 'invalid',
            ]);

        $response->assertOK();
        $response->assertSee('No results, sorry.', 'h3');
    }

    public function testShow()
    {
        $response = $this->get('help/sample/sample-file');

        $response->assertOK();
        $response->assertSee('Sample File', 'h1');
        $response->assertSee('Help Section', 'h2');
    }

    public function testShowWithError()
    {
        $this->expectException(PageNotFoundException::class);
        $this->expectExceptionMessage('Page Not Found');

        $this->get('help/invalid');
    }
}
