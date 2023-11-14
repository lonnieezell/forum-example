<?php

namespace Tests\Helpers;

use CodeIgniter\Test\CIUnitTestCase;

final class NumberHelperTest extends CIUnitTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        helper('number');
    }

    public function testStringToBytes()
    {
        $this->assertSame(1, string_to_bytes('1'));
        $this->assertSame(1, string_to_bytes('1B'));
        $this->assertSame(1, string_to_bytes('1b'));
        $this->assertSame(1, string_to_bytes('1B '));
        $this->assertSame(1, string_to_bytes('1 b'));
        $this->assertSame(1, string_to_bytes('1 b '));
        $this->assertSame(1, string_to_bytes('1 Byte'));
        $this->assertSame(1, string_to_bytes('1 B'));
        $this->assertSame(1, string_to_bytes('1 B '));
        $this->assertSame(1, string_to_bytes('1 b'));
        $this->assertSame(1, string_to_bytes('1 b '));
        $this->assertSame(1, string_to_bytes('1'));
        $this->assertSame(1, string_to_bytes('1 '));
        $this->assertSame(1 * 1024 * 1024, string_to_bytes('1M'));
        $this->assertSame(1 * 1024 * 1024 * 1024, string_to_bytes('1G'));
        $this->assertSame((int)(1.5 * 1024), string_to_bytes('1.5K'));
    }
}
