<?php

    class FileTest extends \PHPUnit_Framework_TestCase {
        
        public static function tearDownAfterClass()
        {
            unlink(DCMS_ROOT.'/cache/somefile.tests.txt');
        }
        
        public function testExistsFalse()
        {
            $file = new \dcms\library\File('file/that/not/exist.txt');
            $result = $file->exists();
            $this->assertFalse($result);
        }
        
        public function testReadableFalse()
        {
            $file = new \dcms\library\File('file/that/not/exist.txt');
            $result = $file->is_readable();
            $this->assertFalse($result);
        }
        
        public function testWriteableFalse()
        {
            $file = new \dcms\library\File('file/that/not/exist.txt');
            $result = $file->is_writeable();
            $this->assertFalse($result);
        }
        
        public function testOpenFalse()
        {
            $file = new \dcms\library\File('file/that/not/exist.txt');
            $result = $file->open('a');
            $this->assertFalse($result);
        }
        
        public function testWriteFalse()
        {
            $file = new \dcms\library\File('file/that/not/exist.txt');
            $result = $file->write('makesNoSense!');
            $this->assertFalse($result);
        }
        
        public function testCloseFalse()
        {
            $file = new \dcms\library\File('file/that/not/exist.txt');
            $result = $file->close();
            $this->assertFalse($result);
        }
        
        public function testReadFalse()
        {
            $file = new \dcms\library\File('file/that/not/exist.txt');
            $result = $file->read(1024);
            $this->assertFalse($result);
        }
        
        public function testExistsTrue()
        {
            $file = new \dcms\library\File('tests/framework/libraries/data/testfile.txt', true);
            $result = $file->exists();
            $this->assertTrue($result);
        }
        
        public function testReadableTrue()
        {
            $file = new \dcms\library\File('tests/framework/libraries/data/testfile.txt', true);
            $result = $file->is_readable();
            $this->assertTrue($result);
        }
        
        public function testWriteableTrue()
        {
            $file = new \dcms\library\File('cache/somefile.tests.txt', true);
            $result = $file->is_writeable();
            $this->assertTrue($result);
        }
        
        public function testOpenTrue()
        {
            $file = new \dcms\library\File('tests/framework/libraries/data/testfile.txt', true);
            $result = $file->open('r');
            $this->assertTrue($result);
        }
        
        public function testReadTrue()
        {
            $file = new \dcms\library\File('tests/framework/libraries/data/testfile.txt', true);
            $file->open('r');
            $result = $file->read(1024);
            $this->assertEquals($result, 'test');
        }
        
        public function testWriteTrue()
        {
            $file = new \dcms\library\File('cache/somefile.tests.txt', true);
            $file->open('w');
            $result = $file->write('test');
            $this->assertTrue($result);
        }
        
        public function testCloseTrue()
        {
            $file = new \dcms\library\File('cache/somefile.tests.txt', true);
            $file->open('w');
            $result = $file->close();
            $this->assertTrue($result);
        }
        
    }