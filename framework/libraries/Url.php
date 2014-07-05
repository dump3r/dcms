<?php

    namespace dcms\library;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');
    
    class Url extends \dcms\Singleton {
        
        protected static $instance;
        protected $string;
        protected $segments = array();
        
        public function __construct() 
        {
            /**
             * Den URL String auslesen
             */
            $this->_get_string();
            
            /**
             * Den URL String auf vebotene Werte überprüfen.
             */
            $this->_validate_string();
            
            /**
             * Den String parsen
             */
            
            \dcms\Log::write('Done loading the url class', null, 1);
        }
        
        /**
         * Den URL String auslesen. Es wird zunächst nach der PATH_INFO
         * Variable in $_SERVER gesucht. Wird diese nicht gefunden, wird
         * der Standardwert der Konfiguration ('route_default') genutzt-
         */
        protected function _get_string()
        {
            /**
             * Den Standardwert aus der Konfiguration laden.
             */
            $this->string = \dcms\Config::get('route_default', 'welcome/index');
            
            /**
             * Nach der PATH_INFO Variable suchen.
             */
            $path_info = filter_input(INPUT_SERVER, 'PATH_INFO', FILTER_SANITIZE_URL);
            if(empty($path_info) === false):
                $string = substr($path_info, 1);
                if(empty($string) === false):
                    $this->string = $string;
                endif;
            endif;
        }
        
        protected function _validate_string()
        {
            /**
             * Den Pattern aus der Konfiguration auslesen.
             */
            $pattern = \dcms\Config::get('url_pattern', '');
            
            /**
             * Nur den String checken, wenn der Pattern nicht leer ist.
             */
            if(empty($pattern) === true)
                return;
            
            /**
             * Den URL String mit preg_match prüfen.
             */
            $result = preg_match('#[^'.$pattern.']#', $this->string);
            if($result > 0):
                
            endif;
        }
        
        protected function _parse_string()
        {
            
        }
        
    }