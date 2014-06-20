<?php

    namespace dcms;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Die statische Loaderklasse wird genutzt, um Models, Libraries, Helper und
     * andere Klassen- bzw. Funktionsdateien zu laden.
     * Alle diese Klassen sollten als Singleton definiert sein.
     *
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/core/Loader
     */
    class Loader {
        
        public static function library($name, $init = true)
        {
            
        }
        
        public static function model($name, $init = false)
        {
            
        }
        
        public static function controller($name)
        {
            
        }
        
        public static function class_file($path, $init = false)
        {
            
        }
        
        public static function helper($name)
        {
            
        }
        
        private static function _load()
        {
            
        }
        
    }
