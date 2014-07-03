<?php

    namespace dcms;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');
    
    /**
     * Die statische Inputklasse wird genutzt um POST bzw. GET Parameter
     * auszulesen und bei Bedarf auch durch die escape() Methode zu maskieren.
     * 
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh,de/dcms/docs/core/Input
     */
    class Input {
        
        /**
         * Einen Parameter aus dem GET-Array auslesen.
         * 
         * @param string $name
         * @param mixed $default
         * @param boolean $escape
         * @return mixed
         */
        public static function get($name, $default = false, $escape = false)
        {
            $value = self::_grab($_GET, $name, $default);
            return self::_escape($value, $escape);
        }
        
        /**
         * Einen Wert aus dem POST-Array auslesen.
         * 
         * @param string $name
         * @param mixed $default
         * @param boolean $escape
         * @return mixed
         */
        public static function post($name, $default = false, $escape = true)
        {
            $value = self::_grab($_POST, $name, $default);
            return self::_escape($value, $escape);
        }
        
        /**
         * Prüfen, ob ein Wert maskiert werden soll.
         * Es muss ein String übergeben werden, damit
         * maskiert werden kann.
         * 
         * @param mixed $value
         * @param boolean $escape
         * @return mixed
         */
        protected static function _escape($value, $escape = false)
        {
            if($escape === true && is_string($value) === true):
                return \dcms\Database::escape($value);
            endif;
            return $value;
        }
        
        /**
         * Einen Schlüssel in einem Array suchen. Wird der Schlüssel
         * nicht gefunden wird der Wert in $default zurückgegeben.
         * 
         * @param array $array
         * @param string|int $needle
         * @param mixed $default
         * @return mixed
         */
        protected static function _grab($array, $needle, $default = false)
        {
            if(is_array($array) === false):
                \dcms\Log::write('No array supplied for $array', null, 3);
                return $default;
            endif;
            
            if(isset($array[$needle]) === false):
                return $default;
            endif;
            
            return $array[$needle];
        }
        
    }