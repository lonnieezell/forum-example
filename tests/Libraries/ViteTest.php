<?php

use App\Libraries\Vite;
use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
final class ViteTest extends CIUnitTestCase
{
    public function testInstance(): void
    {
        $vite = new Vite();

        $this->assertInstanceOf(Vite::class, $vite);
    }

    public function testInstanceWithManifest(): void
    {
        $manifest = json_decode(file_get_contents(SUPPORTPATH . 'Libraries/Vite/assets/.vite/manifest.json'), true);

        $vite = new Vite($manifest);

        $this->assertInstanceOf(Vite::class, $vite);
    }

    public function testWrongExtensions(): void
    {
        $this->expectExceptionMessage('Unknown file type: ext');

        $vite = new Vite();
        $vite->link('wrong.ext');
    }

    public function testChangeWebAssetsDir(): void
    {
        $_ENV['VITE_SERVE'] = 'false';
        $manifest = json_decode(file_get_contents(SUPPORTPATH . 'Libraries/Vite/assets/.vite/manifest.json'), true);
        $vite = new Vite($manifest);

        $link = $vite->style('themes/default/css/app.scss');
        $linkNew = $vite->withWebAssetsDir('design')->link('themes/default/css/app.scss');

        $this->assertStringContainsString('https://example.com/assets/app-BolJ4eB_.css', $link);
        $this->assertStringContainsString('https://example.com/design/app-BolJ4eB_.css', $linkNew);
    }

    #[DataProvider('pathWithManifestProvider')]
    public function testGetLinkWithManifest(string $path, string $expected, string $devMode): void
    {
        $_ENV['VITE_SERVE'] = $devMode;
        $_ENV['VITE_HOST']  = 'localhost';
        $_ENV['VITE_PORT']  = '3000';

        $manifest = json_decode(file_get_contents(SUPPORTPATH . 'Libraries/Vite/assets/.vite/manifest.json'), true);
        $vite     = new Vite($manifest);

        $this->assertSame($expected, trim($vite->link($path)));
    }

    #[DataProvider('pathWithoutManifestProvider')]
    public function testGetLinkWithoutManifest(string $path, string $expected, string $devMode): void
    {
        $_ENV['VITE_SERVE'] = $devMode;
        $_ENV['VITE_HOST']  = 'localhost';
        $_ENV['VITE_PORT']  = '3000';

        $vite = new Vite();

        $this->assertSame($expected, trim($vite->link($path)));
    }

    public static function pathWithManifestProvider()
    {
        yield from [
            // Development.
            [
                'themes/default/css/app.scss',
                '<link rel="stylesheet" href="http://localhost:3000/themes/default/css/app.scss">',
                'true',
            ],
            [
                'themes/admin/css/admin.scss',
                '<link rel="stylesheet" href="http://localhost:3000/themes/admin/css/admin.scss">',
                'true',
            ],
            [
                'undefined.css',
                '',
                'true',
            ],
            [
                'undefined.js',
                '',
                'true',
            ],
            [
                'themes/default/js/app.js',
                '<script type="module" src="http://localhost:3000/themes/default/js/app.js"></script>',
                'true',
            ],
            [
                'themes/admin/js/admin.js',
                '<script type="module" src="http://localhost:3000/themes/admin/js/admin.js"></script>',
                'true',
            ],

            // Production.
            [
                'themes/default/css/app.scss',
                '<link rel="stylesheet" href="https://example.com/assets/app-BolJ4eB_.css">',
                'false',
            ],
            [
                'themes/admin/css/admin.scss',
                '<link rel="stylesheet" href="https://example.com/assets/admin-BwnLi0AT.css">',
                'false',
            ],
            [
                'undefined.css',
                '',
                'false',
            ],
            [
                'undefined.js',
                '',
                'false',
            ],
            [
                'themes/default/js/app.js',
                '<script type="module" src="https://example.com/assets/module.esm-CBS4kwPT.js"></script>
<link rel="stylesheet" href="https://example.com/assets/app-DrGlaGvg.css">
<script type="module" src="https://example.com/assets/app-GTsfWFNk.js"></script>',
                'false',
            ],
            [
                'themes/admin/js/admin.js',
                '<script type="module" src="https://example.com/assets/module.esm-CBS4kwPT.js"></script>
<script type="module" src="https://example.com/assets/admin-iTQpL3fh.js"></script>',
                'false',
            ],
        ];
    }

    public static function pathWithoutManifestProvider()
    {
        yield from [
            // Development.
            [
                'themes/default/css/app.scss',
                '<link rel="stylesheet" href="http://localhost:3000/themes/default/css/app.scss">',
                'true',
            ],
            [
                'themes/admin/css/admin.scss',
                '<link rel="stylesheet" href="http://localhost:3000/themes/admin/css/admin.scss">',
                'true',
            ],
            [
                'undefined.css',
                '',
                'true',
            ],
            [
                'undefined.js',
                '',
                'true',
            ],
            [
                'themes/default/js/app.js',
                '<script type="module" src="http://localhost:3000/themes/default/js/app.js"></script>',
                'true',
            ],
            [
                'themes/admin/js/admin.js',
                '<script type="module" src="http://localhost:3000/themes/admin/js/admin.js"></script>',
                'true',
            ],

            // Production.
            // If there is no manifest, then there are no scripts and styles.
            [
                'themes/default/css/app.scss',
                '',
                'false',
            ],
            [
                'themes/admin/css/admin.scss',
                '',
                'false',
            ],
            [
                'undefined.css',
                '',
                'false',
            ],
            [
                'undefined.js',
                '',
                'false',
            ],
            [
                'themes/default/js/app.js',
                '',
                'false',
            ],
            [
                'themes/admin/js/admin.js',
                '',
                'false',
            ],
            [
                'themes/default/js/app.js',
                '',
                'false',
            ],
            [
                'themes/admin/js/admin.js',
                '',
                'false',
            ],
        ];
    }

    #[DataProvider('pathsWithManifestProvider')]
    public function testGetLinksWithManifest(array $paths, string $expected, string $devMode): void
    {
        $_ENV['VITE_SERVE'] = $devMode;
        $_ENV['VITE_HOST']  = 'localhost';
        $_ENV['VITE_PORT']  = '3000';

        $manifest = json_decode(file_get_contents(SUPPORTPATH . 'Libraries/Vite/assets/.vite/manifest.json'), true);
        $vite     = new Vite($manifest);

        $this->assertSame($expected, trim($vite->links($paths)));
    }

    public static function pathsWithManifestProvider()
    {
        yield from [
            // Development.
            [
                ['themes/default/css/app.scss'],
                '<script type="module" src="http://localhost:3000/@vite/client"></script>
<link rel="stylesheet" href="http://localhost:3000/themes/default/css/app.scss">',
                'true',
            ],
            [
                ['themes/admin/css/admin.scss'],
                '<script type="module" src="http://localhost:3000/@vite/client"></script>
<link rel="stylesheet" href="http://localhost:3000/themes/admin/css/admin.scss">',
                'true',
            ],
            [
                ['undefined.css'],
                '',
                'true',
            ],
            [
                ['undefined.js'],
                '',
                'true',
            ],
            [
                ['themes/default/js/app.js'],
                '<script type="module" src="http://localhost:3000/@vite/client"></script>
<script type="module" src="http://localhost:3000/themes/default/js/app.js"></script>',
                'true',
            ],
            [
                ['themes/admin/js/admin.js'],
                '<script type="module" src="http://localhost:3000/@vite/client"></script>
<script type="module" src="http://localhost:3000/themes/admin/js/admin.js"></script>',
                'true',
            ],
            [
                [
                    'themes/default/js/app.js',
                    'themes/admin/js/admin.js',
                    'undefined.js',
                    'themes/default/js/app.js',
                    'themes/admin/js/admin.js',
                    'themes/default/css/app.scss',
                    'themes/admin/css/admin.scss',
                ],
                '<script type="module" src="http://localhost:3000/@vite/client"></script>
<script type="module" src="http://localhost:3000/themes/default/js/app.js"></script>
<script type="module" src="http://localhost:3000/themes/admin/js/admin.js"></script>
<link rel="stylesheet" href="http://localhost:3000/themes/default/css/app.scss">
<link rel="stylesheet" href="http://localhost:3000/themes/admin/css/admin.scss">',
                'true',
            ],

            // Production.
            [
                ['themes/default/css/app.scss'],
                '<link rel="stylesheet" href="https://example.com/assets/app-BolJ4eB_.css">',
                'false',
            ],
            [
                ['themes/admin/css/admin.scss'],
                '<link rel="stylesheet" href="https://example.com/assets/admin-BwnLi0AT.css">',
                'false',
            ],
            [
                ['undefined.css'],
                '',
                'false',
            ],
            [
                ['undefined.js'],
                '',
                'false',
            ],
            [
                ['themes/default/js/app.js'],
                '<script type="module" src="https://example.com/assets/module.esm-CBS4kwPT.js"></script>
<link rel="stylesheet" href="https://example.com/assets/app-DrGlaGvg.css">
<script type="module" src="https://example.com/assets/app-GTsfWFNk.js"></script>',
                'false',
            ],
            [
                ['themes/admin/js/admin.js'],
                '<script type="module" src="https://example.com/assets/module.esm-CBS4kwPT.js"></script>
<script type="module" src="https://example.com/assets/admin-iTQpL3fh.js"></script>',
                'false',
            ],
            [
                [
                    'themes/default/js/app.js',
                    'themes/admin/js/admin.js',
                    'undefined.js',
                    'themes/default/js/app.js',
                    'themes/admin/js/admin.js',
                    'themes/default/css/app.scss',
                    'themes/admin/css/admin.scss',
                ],
                '<script type="module" src="https://example.com/assets/module.esm-CBS4kwPT.js"></script>
<link rel="stylesheet" href="https://example.com/assets/app-DrGlaGvg.css">
<script type="module" src="https://example.com/assets/app-GTsfWFNk.js"></script>
<script type="module" src="https://example.com/assets/admin-iTQpL3fh.js"></script>
<link rel="stylesheet" href="https://example.com/assets/app-BolJ4eB_.css">
<link rel="stylesheet" href="https://example.com/assets/admin-BwnLi0AT.css">',
                'false',
            ],
        ];
    }

    #[DataProvider('pathsWithoutManifestProvider')]
    public function testGetLinksWithoutManifest(array $paths, string $expected, string $devMode): void
    {
        $_ENV['VITE_SERVE'] = $devMode;
        $_ENV['VITE_HOST']  = 'localhost';
        $_ENV['VITE_PORT']  = '3000';

        $vite = new Vite();

        $this->assertSame($expected, trim($vite->links($paths)));
    }

    public static function pathsWithoutManifestProvider()
    {
        yield from [
            // Development.
            [
                ['themes/default/css/app.scss'],
                '<script type="module" src="http://localhost:3000/@vite/client"></script>
<link rel="stylesheet" href="http://localhost:3000/themes/default/css/app.scss">',
                'true',
            ],
            [
                ['themes/admin/css/admin.scss'],
                '<script type="module" src="http://localhost:3000/@vite/client"></script>
<link rel="stylesheet" href="http://localhost:3000/themes/admin/css/admin.scss">',
                'true',
            ],
            [
                ['undefined.css'],
                '',
                'true',
            ],
            [
                ['undefined.js'],
                '',
                'true',
            ],
            [
                ['themes/default/js/app.js'],
                '<script type="module" src="http://localhost:3000/@vite/client"></script>
<script type="module" src="http://localhost:3000/themes/default/js/app.js"></script>',
                'true',
            ],
            [
                ['themes/admin/js/admin.js'],
                '<script type="module" src="http://localhost:3000/@vite/client"></script>
<script type="module" src="http://localhost:3000/themes/admin/js/admin.js"></script>',
                'true',
            ],

            // Production.
            // If there is no manifest, then there are no scripts and styles.
            [
                ['themes/default/css/app.scss'],
                '',
                'false',
            ],
            [
                ['themes/admin/css/admin.scss'],
                '',
                'false',
            ],
            [
                ['undefined.css'],
                '',
                'false',
            ],
            [
                ['undefined.js'],
                '',
                'false',
            ],
            [
                ['themes/default/js/app.js'],
                '',
                'false',
            ],
            [
                ['themes/admin/js/admin.js'],
                '',
                'false',
            ],
            [
                ['themes/default/js/app.js'],
                '',
                'false',
            ],
            [
                ['themes/admin/js/admin.js'],
                '',
                'false',
            ],
        ];
    }
}
