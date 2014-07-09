<?php

    class StandardTest extends \PHPUnit_Framework_TestCase {
        
        public function testIsCachedFalse()
        {
            $template = new \dcms\template\Standard('file/that/can/not/exist.php');
            $result = $template->is_cached();
            $this->assertFalse($result);
        }
        
        public function testCreateCacheFalse()
        {
            $template = new \dcms\template\Standard('file/that/can/not/exist.php');
            $result = $template->create_cache();
            $this->assertFalse($result);
        }
        
        public function testDestroyCache()
        {
            $template = new \dcms\template\Standard('file/that/can/not/exist.php');
            $result = $template->destroy_cache();
            $this->assertTrue($result);
        }
        
        public function testFetchFalse()
        {
            $template = new \dcms\template\Standard('file/that/can/not/exist.php');
            $result = $template->fetch();
            $this->assertFalse($result);
        }
        
        public function testCreateCache()
        {
            $template = new \dcms\template\Standard('testfile', 'tests/framework/template/data/');
            $template->assign('title', 'myTest');
            $result = $template->create_cache();
            $this->assertFileExists($result);
        }
        
        public function testIsCached()
        {
            $template = new \dcms\template\Standard('testfile', 'tests/framework/template/data/');
            $result = $template->is_cached();
            $this->assertTrue($result);
        }
        
        public function testFetch()
        {
            $template = new \dcms\template\Standard('testfile', 'tests/framework/template/data/');
            $testFile = DCMS_ROOT.'/tests/framework/template/data/testfile.html';
            
            $testFileContent = file_get_contents($testFile);
            $templateContent = $template->fetch();
            
            $this->assertEquals($templateContent, $testFileContent);
        }
        
        public function testDestroyCache2()
        {
            $template = new \dcms\template\Standard('testfile', 'tests/framework/template/data/');
            $result = $template->destroy_cache();
            $this->assertTrue($result);
        }
        
    }