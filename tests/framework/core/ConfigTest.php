<?php

    class ConfigTest extends PHPUnit_Framework_TestCase {
        
        public function setUp()
        {
            \dcms\Core::core_part('Config');
            \dcms\CoreMock::_clear_tracker();
        }
        
        public function testSetAndGet()
        {
            $name = 'testConfigValue';
            $value = 'valueToAssert';
            
            \dcms\Config::set($name, $value);
            $returnValue = \dcms\Config::get($name);
            
            $this->assertEquals($value, $returnValue);
        }
        
    }
