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
        
        /**
         * Eine Library laden
         * 
         * @param string $name
         * @param boolean $init
         * @return void
         */
        public static function library($name, $init = true)
        {
            $name = ucfirst(strtolower($name));
            
            $directory = 'libraries';
            $filename = $name;
            $tracker = 'library';
            $init = (boolean) $init;
            $namespace = 'library';
            $class_name = $name;
            
            return self::_load($directory, $filename, $tracker, $init, $namespace, $class_name);
        }
        
        /**
         * Ein Model laden
         * 
         * @param string $name
         * @param boolean $init
         * @return void
         */
        public static function model($name, $init = false)
        {
            $name = ucfirst(strtolower($name));
            
            $directory = 'models';
            $filename = $name;
            $tracker = 'model';
            $init = (boolean) $init;
            $namespace = 'model';
            $class_name = $name;
            
            return self::_load($directory, $filename, $tracker, $init, $namespace, $class_name);
        }
        
        /**
         * Einen Controller laden.
         * Diese Methode sollte nicht mehr als einmal genutzt werden!
         * 
         * @param string $name
         * @return void
         */
        public static function controller($name)
        {
            $name = ucfirst(strtolower($name));
            
            $directory = 'controller';
            $filename = $name;
            $tracker = 'controller';
            $init = (boolean) $init;
            $namespace = 'controller';
            $class_name = $name;
            
            return self::_load($directory, $filename, $tracker, $init, $namespace, $class_name);
        }
        
        /**
         * Eine benutzerdefinierte Klassendatei laden
         * 
         * @param string $filename
         * @param string $directory
         * @param boolean $init
         * @return void
         */
        public static function class_file($filename, $directory, $init = false)
        {
            $name = ucfirst(strtolower($filename));
            
            $filename = $name;
            $tracker = 'custom';
            $init = (boolean) $init;
            $namespace = 'custom';
            $class_name = $name;
            
            return self::_load($directory, $filename, $tracker, $init, $namespace, $class_name);
        }
        
        /**
         * Eine Helperdatei laden
         * 
         * @param string $name
         * @return void
         */
        public static function helper($name)
        {
            $name = strtolower($name).'_helper';
            
            $directory = 'helper';
            $filename = $name;
            $tracker = 'helper';
            $init = false;
            $namespace = null;
            $class_name = null;
            
            return self::_load($directory, $filename, $tracker, $init, $namespace, $class_name);
        }
        
        /**
         * Eine Klassen oder Funktionsdatei laden.
         * 
         * @param string $directory
         * @param string $filename
         * @param string $tracker
         * @param boolean $init
         * @param string $namespace
         * @param string $class_name
         * @return boolean
         */
        private static function _load($directory, $filename, $tracker, $init, $namespace, $class_name)
        {
            $base_array = array(
                array('framework', ''),
                array(DCMS_CALL, DCMS_CALL)
            );
            
            foreach($base_array as $base):
                
                $basepath = $base[0].'/'.$directory;
                $loaded = \dcms\Core::load_file($filename, $basepath, true, false);
                
                if($loaded === true):
                    
                    $filepath = $basepath.'/'.$filename.'.php';
                    \dcms\Log::write("Loaded file from $filepath", null, 1);
                    
                    $full_class_name = "\dcms\\$namespace\\{$base[1]}\\$class_name";
                    self::_initialize_class($full_class_name, $init);
                    \dcms\Core::track_file($filepath, $tracker);
                    
                    return true;
                    
                endif;
                
            endforeach;
            
            \dcms\Log::write("Could not load file $filename.php from /$directory/ as $tracker!", null, 3);
            \dcms\Core::kill("Could not load file!");
        }
        
        /**
         * Versuchen eine auf dem Singleton basierende Klasse zu laden.
         * Es wird nach der statischen get() Methode gesucht.
         * 
         * @param string $class_name
         * @param boolean $init
         * @return void
         */
        private static function _initialize_class($class_name, $init)
        {
            if($init === false)
                return;
            
            $class_exists = class_exists($class_name, false);
            if($class_exists === false):
                \dcms\Log::write("Could not find class $class_name!", null, 3);
                \dcms\Core::kill("Class declaration error!");
            endif;
            
            $class_parents = class_parents($class_name, true);
            if(in_array('dcms\Singleton', $class_parents) === false):
                \dcms\Log::write(
                    "Can not initialize class $class_name! This class does not extend the Singleton class.",
                    null,
                    3
                );
                return;
            endif;
            
            // call
            \dcms\Log::write("Calling get() method of $class_name ...", null, 1);
            $class_name::get();
        }
        
    }
