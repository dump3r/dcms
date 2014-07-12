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
        
        /**
         * Ein Cookie erstellen. Wird ein Array für $value übergeben,
         * wird es in ein JSON String umgewandelt.
         * 
         * @param string $name
         * @param string|array $value
         * @param int $ttl
         * @param boolean $prefix
         * @return boolean
         */
        public function set($name, $value, $ttl = 7200, $prefix = true)
        {
            $cookie_name = $this->_cookie_name($name, $prefix);
            $cookie_path = \dcms\Config::get('cookie_path', '/');
            $cookie_domain = \dcms\Config::get('cookie_domain', null);
            $cookie_secure = \dcms\Config::get('cookie_secure', false);
            $cookie_httponly = \dcms\Config::get('cookie_httponly', false);
            
            /**
             * Wenn $value ein Array ist wird es in ein JSON String umgewandelt
             */
            if(is_array($value))
                $value = json_encode ($value);
            
            /**
             * Wenn $ttl exakt 0 ist wird kein Zeitstempel addiert.
             */
            if($ttl !== 0)
                $ttl = time() + $ttl;
            
            /**
             * Das Cookie erstellen.
             */
            $setcookie = setcookie(
                $cookie_name,
                $value,
                $ttl,
                $cookie_path,
                $cookie_domain,
                $cookie_secure,
                $cookie_httponly
            );
            
            return $setcookie;
        }
        
        /**
         * Ein Cookie löschen.
         * 
         * @param string $name
         * @param boolean $prefix
         * @return boolean
         */
        public function delete($name, $prefix = true)
        {
            return $this->set($name, null, -7200, $prefix);
        }
        
        /**
         * Ein Cookie erneuern.
         * 
         * @param string $name
         * @param int $ttl
         * @param boolean $prefix
         * @return boolean
         */
        public function rearm($name, $ttl = 7200, $prefix = true)
        {
            $cookie_value = $this->grab($name, false, false, $prefix);
            if($cookie_value === false):
                \dcms\Log::write("Can not rearm non-existing cookie $cookie_name!", null, 3);
                return false;
            endif;
            
            return $this->set($name, $cookie_value, $ttl, $prefix);
        }
        
        /**
         * Ein CSRF-Cookie erstellen.
         * 
         * @param string $name
         * @param int $ttl
         * @param boolean $prefix
         * @return boolean
         */
        public function set_csrf_cookie($name = 'csrf', $ttl = 7200, $prefix = true)
        {
            /**
             * Einen String für das Cookie erstellen.
             */
            $hash_chars = str_shuffle($this->csrf_chars).time().str_shuffle($this->csrf_chars);
            $hash_value = hash($this->hashing_type, $hash_chars);
            
            /**
             * Das Cookie setzen
             */
            return $this->set($name, $hash_value, $ttl, $prefix);
        }
        
        /**
         * Ein String mit dem CSRF-Cookie vergleichen.
         * 
         * @param string $value
         * @param string $name
         * @param boolean $prefix
         * @return boolean
         */
        public function check_csrf_cookie($value, $name = 'csrf', $prefix = true)
        {
            $cookie_value = $this->grab($name, false, false, $prefix);
            if($cookie_value === false):
                \dcms\Log::write("Can not get CSRF cookie value from non-existing cookie", null, 3);
                return false;
            endif;
            
            if($value == $cookie_value)
                return true;
            
            \dcms\Log::write("Cookie value and supplied value does not match! Possible CSRF attack ...", null, 3);
            return false;
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
