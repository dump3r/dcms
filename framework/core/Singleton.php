<?php

    namespace dcms;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Die Singletonklasse für alle Klassen des Systems.
     *
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/core/Singleton
     */
    class Singleton {
        
        public static function get()
        {
            if(empty(static::$instance)):
                $class_name = get_called_class();
                static::$instance = new $class_name;
            endif;
            return static::$instance;
        }
        
    }
