<?php

    class LogTest extends \PHPUnit_Framework_TestCase {
        
        public function setUp()
        {
            \dcms\LogMock::_clear();
        }
        
        public function testWriteAndFetch()
        {
            \dcms\LogMock::write('test', null, 1);
            $this->assertEquals(1, count(\dcms\LogMock::fetch()));
        }
        
    }