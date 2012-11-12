<?php

namespace Gaufrette\Functional\Adapter;

use Gaufrette\Filesystem;

abstract class FunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    protected $filesystem;

    public function getAdapterName()
    {
        if (!preg_match('/\\\\(\w+)Test$/', get_class($this), $matches)) {
            throw new \RuntimeException(sprintf(
                'Unable to guess filesystem name from class "%s", '.
                'please override the ->getAdapterName() method.',
                get_class($this)
            ));
        }

        return $matches[1];
    }

    public function setUp()
    {
        $basename = $this->getAdapterName();
        $filename = sprintf(
            '%s/filesystems/%s.php',
            dirname(__DIR__),
            $basename
        );

        if (!file_exists($filename)) {
            return $this->markTestSkipped(<<<EOF
To run the {$basename} filesystem tests, you must:

 1. Copy the file "{$filename}.dist" as "{$filename}"
 2. Modify the copied file to fit your environment
EOF
            );
        }

        $adapter = include $filename;
        $this->filesystem = new Filesystem($adapter);
    }

    public function tearDown()
    {
        if (null === $this->filesystem) {
            return;
        }

        $this->filesystem = null;
    }

    /**
     * @test
     * @group functional
     */
    public function shouldWriteAndRead()
    {
        $this->assertEquals(12, $this->filesystem->write('foo', 'Some content'));

        $this->assertEquals('Some content', $this->filesystem->read('foo'));
    }

    /**
     * @test
     * @group functional
     */
    public function shouldUpdateFileContent()
    {
        $this->filesystem->write('foo', 'Some content');
        $this->filesystem->write('foo', 'Some content updated', true);

        $this->assertEquals('Some content updated', $this->filesystem->read('foo'));
    }

    /**
     * @test
     * @group functional
     */
    public function shouldCheckIfFileExists()
    {
        $this->assertFalse($this->filesystem->has('foo'));

        $this->filesystem->write('foo', 'Some content');

        $this->assertTrue($this->filesystem->has('foo'));
    }

    /**
     * @test
     * @group functional
     */
    public function shouldGetMtime()
    {
        $this->filesystem->write('foo', 'Some content');

        $this->assertGreaterThan(0, $this->filesystem->mtime('foo'));
    }

    /**
     * @test
     * @group functional
     * @expectedException \RuntimeException
     * @expectedMessage Could not get mtime for the "foo" key
     */
    public function shouldFailWhenTryMtimeForKeyWhichDoesNotExist()
    {
        $this->assertFalse($this->filesystem->mtime('foo'));
    }

    /**
     * @test
     * @group functional
     */
    public function shouldRenameFile()
    {
        $this->filesystem->write('foo', 'Some content');
        $this->filesystem->rename('foo', 'boo');

        $this->assertFalse($this->filesystem->has('foo'));
        $this->assertEquals('Some content', $this->filesystem->read('boo'));
        $this->filesystem->delete('boo');
    }

    /**
     * @test
     * @group functional
     */
    public function shouldDeleteFile()
    {
        $this->filesystem->write('foo', 'Some content');

        $this->assertTrue($this->filesystem->has('foo'));

        $this->filesystem->delete('foo');

        $this->assertFalse($this->filesystem->has('foo'));
    }

    /**
     * @test
     * @group functional
     */
    public function shouldFetchKeys()
    {
        $this->assertEquals(array(), $this->filesystem->keys());

        $this->filesystem->write('foo', 'Some content');
        $this->filesystem->write('bar', 'Some content');
        $this->filesystem->write('baz', 'Some content');

        $actualKeys = $this->filesystem->keys();

        $this->assertEquals(3, count($actualKeys));
        foreach (array('foo', 'bar', 'baz') as $key) {
            $this->assertContains($key, $actualKeys);
        }
    }
}