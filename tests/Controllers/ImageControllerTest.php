<?php

namespace Tests\Controllers;

use App\Models\Factories\UserFactory;
use CodeIgniter\HTTP\Files\UploadedFile;
use Exception;
use org\bovigo\vfs\vfsStream;
use Tests\Support\Database\Seeds\TestDataSeeder;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class ImageControllerTest extends TestCase
{
    protected $seed = TestDataSeeder::class;
    private string $start;

    protected function setUp(): void
    {
        parent::setUp();
        $_FILES = [];

        // create virtual file system
        $root = vfsStream::setup();
        // copy our support files
        $origin = '_support/Images/';
        vfsStream::copyFromFileSystem(TESTPATH . $origin, $root);

        $this->start = $root->url() . '/';
    }

    /**
     * @throws Exception
     */
    public function testCanGuestUploadAnImage()
    {
        $response = $this
            ->withHeaders([csrf_header() => csrf_hash()])
            ->post('images/upload');

        $response->assertStatus(401);
        $response->assertJSONExact(['error' => 'You are not allowed to upload images.']);
    }

    /**
     * @throws Exception
     */
    public function testCanUserPassValidationWithoutProvidingAFile()
    {
        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this
            ->withHeaders([csrf_header() => csrf_hash()])
            ->actingAs($user)->post('images/upload');

        $response->assertStatus(400);
        $response->assertJSONExact(['error' => 'image is not a valid uploaded file.']);
    }

    /**
     * @throws Exception
     */
    public function testCanUserPassValidationWithUploadError(): void
    {
        $_FILES = [
            'image' => [
                'name'     => 'ci-logo.jpeg',
                'type'     => 'image/jpeg',
                'size'     => '7534',
                'tmp_name' => $this->start . 'ci-logo.jpeg',
                'error'    => 1,
            ],
        ];

        $stub = $this->createStub(UploadedFile::class);
        $stub->method('isValid')
            ->willReturn(true);

        $user = fake(UserFactory::class, [
            'username' => 'testuser',
        ]);
        $user->addGroup('user');
        $response = $this
            ->withHeaders([csrf_header() => csrf_hash()])
            ->actingAs($user)->post('images/upload');

        $response->assertStatus(400);
        $response->assertJSONExact(['error' => 'image is not a valid uploaded file.']);
    }
}
