<?php

    class RouterTest extends \PHPUnit_Framework_TestCase {
        
        public function setUp()
        {
            \dcms\Loader::library('Router');
        }
        
        public function testRouteFalse()
        {
            /* @var $router \dcms\library\Router */
            $router = \dcms\library\Router::get();
            
            $result = $router->route('canNotExist', 'defentlyNot');
            $this->assertFalse($result);
        }
        
    }
