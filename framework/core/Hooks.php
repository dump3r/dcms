<?php

    namespace dcms;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Die Hookklasse wird genutzt um benutzerdefinierte Funktionen
     * an bestimmten Stellen im System auszuführen. Es gibt vordefnierte
     * Hookstellen es können aber auch eigene erstellt und aufgerufen werden.
     *
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/core/Hooks
     */
    class Hooks {
        
        private static $hooks = array(
            'pre_database' => array(),
            'post_database' => array(),
            'pre_autoloader' => array(),
            'post_autoloader' => array(),
            'pre_controller' => array(),
            'construct_controller' => array()
        );
        
        /**
         * Einen Hook erstellen. Dieser kann später mit call() aufgerufen werden.
         * 
         * @param string $name
         * @return boolean
         */
        public static function add($name)
        {
            if(self::hook_exists($name) === true):
                \dcms\Log::write("Can not create already existing hook $name", null, 2);
                return false;
            endif;
            
            self::$hooks[$name] = array();
            \dcms\Log::write("Created new hook $name", null, 1);
            return true;
        }
        
        /**
         * Alle Funktionen in einem Hook aufrufen. Kann eine Funktion nicht
         * ausgeführt werden, wird das System gestoppt.
         * 
         * @param string $name
         * @return void
         */
        public static function call($name)
        {
            if(self::hook_exists($name) === false):
                \dcms\Log::write("Can not call non-existing hook $name", null, 3);
                return;
            endif;
            
            $hook = self::$hooks[$name];
            if(empty($hook) === true):
                \dcms\Log::write("Hook $name seems to be empty", null, 2);
                return;
            endif;
            
            foreach($hook as $function_name => $function):
                
                if(is_callable($function) === false):
                    \dcms\Log::write("Function $function_name in hook $hook is not callable", null, 3);
                    \dcms\Core::kill("Hook function error!");
                endif;
                
                call_user_func($function);
                \dcms\Log::write("Called function $function_name in hook $hook", null, 1);
                
            endforeach;
            
            \dcms\Log::write("Called all functions in hook $hook", null, 1);
            return;
        }
        
        /**
         * Eine Funktion für einen Hook hinzufügen.
         * 
         * @param string $hook
         * @param string $name
         * @param callable $callable
         * @return boolean
         */
        public static function add_callable($hook, $name, $callable)
        {
            if(self::hook_exists($hook) === false):
                \dcms\Log::write("Can not add a function to a non-exsisting hook $hook", null, 3);
                return false;
            endif;
            
            if(self::callable_exists($hook, $name) === true):
                \dcms\Log::write("Overwriting already existing callable $name in hook $hook", null, 2);
            endif;
            
            if(is_callable($callable) === false):
                \dcms\Log::write("Function $name for hook $hook is not callable", null, 3);
                \dcms\Core::kill("Hook function error!");
            endif;
            
            self::$hooks[$hook][$name] = $callable;
            return true;
        }
        
        /**
         * Eine Funktion in einem Hook löschen.
         * 
         * @param string $hook
         * @param string $name
         * @return boolean
         */
        public static function remove_callable($hook, $name)
        {
            if(self::callable_exists($hook, $name) === false):
                \dcms\Log::write("Can not remove non-existing callable $name in hook $hook", null, 3);
                return false;
            endif;
            
            unset(self::$hooks[$hook][$name]);
            \dcms\Log::write("Removed callable $name from hook $hook", null, 2);
            return true;
        }
        
        /**
         * Prüfen ob ein Hook bereits existiert. Es wird nicht beachtet, 
         * ob er leer ist oder nicht.
         * 
         * @param string $name
         * @return boolean
         */
        public static function hook_exists($name)
        {
            return isset(self::$hooks[$name]);
        }
        
        /**
         * Prüfen ob eine Funktion bereits für eine Hook existiert.
         * 
         * @param string $hook
         * @param string $name
         * @return boolean
         */
        public static function callable_exists($hook, $name)
        {
            return isset(self::$hooks[$hook][$name]);
        }
        
    }
