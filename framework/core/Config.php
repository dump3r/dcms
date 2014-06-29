<?php

    namespace dcms;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Die statische Konfigurationsklasse wird von fast allen anderen Klassen
     * des Systems genutzt. Diese Klasse selbst lädt keine Dateien in denen 
     * Konfigurationswert gepseichert sind.
     *
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/core/Config
     */
    class Config {
        
        protected static $values = array();
        
        /**
         * Einen Konfigurationswert setzen.
         * 
         * @param string $name
         * @param mixed $value
         * @return void
         */
        public static function set($name, $value)
        {
            if(isset(self::$values[$name]) === true):
                \dcms\Log::write("Overriding existing config value $name!", null, 2);
            endif;
            self::$values[$name] = $value;
        }
        
        /**
         * Einen Konfigurationswert auslesen. Existiert der ein Parameter nicht,
         * wird der Wert in $default zurückgegeben.
         * 
         * @param string $name
         * @param mixed $default
         * @return mixed
         */
        public static function get($name, $default = false)
        {
            if(isset(self::$values[$name]) === false):
                \dcms\Log::write(
                    "Config value for $name does not seem to exist. Returning default value ...",
                    null,
                    2
                );
                return $default;
            endif;
            
            return self::$values[$name];
        }
        
    }
