<?php

    namespace dcms\library;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');
    
    class Url extends \dcms\Singleton {
        
        protected static $instance;
        protected $string;
        protected $segments = array();
        protected $pattern;
        
        public function __construct() {
            \dcms\Log::write('Done loading the url class', null, 1);
        }
        
    }