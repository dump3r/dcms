<?php

    namespace dcms\library;

    /**
     * Description of Cookie
     *
     * @author dump3r
     * @version 1.1.0
     * @see http://blaargh.de/dcms/docs/library/Cookie
     */
    class Cookie extends \dcms\Singleton {
        
        protected static $instance;
        protected $hashing_type = 'haval160,4';
        protected $csrf_chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        
        /**
         * Ein Cookie auslesen und wenn benötigt als JSON validieren.
         * 
         * @param string $name
         * @param mixed $default
         * @param boolean $json
         * @param boolean $prefix
         * @return mixed
         */
        public function grab($name, $default = false, $json = true, $prefix = true)
        {
            $cookie_name = $this->_cookie_name($name, $prefix);
            
            if($this->_isset($cookie_name) === false)
                return $default;
            
            $cookie_data = $_COOKIE[$cookie_name];
            if($json === false)
                return $cookie_data;
            
            $decode = json_decode($cookie_data);
            if($decode !== false)
                return $decode;
            
            \dcms\Log::write("Could not decode cookie value of cookie $cookie_name as JSON!", null, 3);
            return $default;
        }
        
        public function set($name, $value, $ttl = 7200, $prefix = true)
        {
            
        }
        
        /**
         * Den Cookienamen formatieren und bei Bedarf das Cookie-Prefix
         * voranstellen.
         * 
         * @param string $name
         * @param boolean $prefix
         * @return string
         */
        protected function _cookie_name($name, $prefix = true)
        {
            if($prefix === false)
                return $name;
            
            $prefix_string = \dcms\Config::get('cookie_prefix', '');
            return $prefix_string.$name;
        }
        
        /**
         * Prüfen ob ein Cookie gesetzt ist.
         * 
         * @param string $name
         * @return boolean
         */
        protected function _isset($name)
        {
            $result = isset($_COOKIE[$name]);
            if($result === true)
                return true;
            
            \dcms\Log::write("Cookie $name does not exist!", null, 2);
            return false;
        }
        
    }
