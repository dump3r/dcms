<?php

    class CoreTest extends PHPUnit_Framework_TestCase {
        
        public function setUp()
        {
            \dcms\CoreMock::_clear_tracker();
        }
        
        public function testPre()
        {
            $expected_output = "<pre>NULL\n</pre>";
            $this->expectOutputString($expected_output);
            \dcms\CoreMock::pre(null);
        }
        
        public function testPre2()
        {
            $expected_output = "<pre>array(0) {\n}\n</pre>";
            $this->expectOutputString($expected_output);
            \dcms\CoreMock::pre(array());
        }
        
        public function testTrackFile()
        {
            $filepath1 = 'file/to/track.php';
            $filepath2 = 'antoher/file/to/track.php';
            
            $tracker1 = 'fileTracker';
            $tracker2 = 'anotherFileTracker';
            
            $expected_array = array(
                $tracker1 => array($filepath1),
                $tracker2 => array($filepath2)
            );
            
            \dcms\CoreMock::track_file($filepath1, $tracker1);
            \dcms\CoreMock::track_file($filepath2, $tracker2);
            
            $return = \dcms\CoreMock::get_files();
            $this->assertEquals($return, $expected_array);
        }
        
        public function testLoadFileFalse()
        {
            $return = \dcms\CoreMock::load_file('file', 'non_existing', true, false);
            $this->assertFalse($return);
        }
        
        public function testLoadFileTrue()
        {
            $return = \dcms\CoreMock::load_file('Log', 'framework/core', true, false);
            $this->assertTrue($return);
        }
        
        public function testCoreMockPart()
        {
            $return = \dcms\CoreMock::core_part('Log');
            $this->assertNull($return);
        }
        
    }
