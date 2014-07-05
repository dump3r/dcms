<?php

    class UrlTest extends \PHPUnit_Framework_TestCase {
        
        public function testSegment()
        {
            $_SERVER['PATH_INFO'] = '/welcome/index';
            $url = new \dcms\library\Url();
            
            $this->assertEquals('welcome', $url->segment(1));
            $this->assertEquals('index', $url->segment(2));
        }
        
        public function testParseStringNoString()
        {
            /* @var $url \dcms\library\Url */
            $url = \dcms\library\Url::get();
            
            $wannabe_string = array();
            $result = $url->parse_string($wannabe_string);
            
            $this->assertFalse($result);
        }
        
        public function testParseStringFalse()
        {
            /* @var $url \dcms\library\Url */
            $url = \dcms\library\Url::get();
            
            $string = 'welcome/jksdfhkü=)()';
            $result = $url->parse_string($string);
            
            $this->assertFalse($result);
        }
        
        public function testParseStringArray()
        {
            /* @var $url \dcms\library\Url */
            $url = \dcms\library\Url::get();
            
            $string = 'welcome/index';
            $result_expected = array('welcome', 'index');
            $result_returned = $url->parse_string($string);
            
            $this->assertEquals($result_expected, $result_returned);
        }
        
        public function testParseStringAutofill()
        {
            /* @var $url \dcms\library\Url */
            $url = \dcms\library\Url::get();
            
            $string = 'welcome';
            $result_expected = array('welcome', 'index');
            $result_returned = $url->parse_string($string);
            
            $this->assertEquals($result_expected, $result_returned);
        }
        
    }