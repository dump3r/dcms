<?php

    namespace dcms\library;
    
    if(!defined('DCMS_SECURE'))
        exit('Veboten!');
    
    /**
     * Prüfen ob lessc existiert.
     */
    if(class_exists('lessc') === false):
        \dcms\Log::write('Could not find third-party class lessc (lessphp)! Please install it.', 'init', 3);
        kill("Extension missing!");
    endif;

    /**
     * Description of Compiler
     *
     * @author dump3r
     * @version 1.0.0
     * @since 1.2.0
     * @see http://blaargh.de/dcms/docs/library/Compiler
     */
    class Compiler extends \dcms\Singleton {
        
        protected static $instance;
        protected $lessc;
        
        public function __construct() 
        {
            /**
             * Eine Instanz von lessc ertstellen.
             */
            $this->lessc = new \lessc();
        }
        
        /**
         * Eine less-Datei in CSS umwandeln.
         * 
         * @param string $input
         * @param string $output
         * @return boolean
         */
        public function compile($input, $output)
        {
            $input = DCMS_ROOT.'/'.$input;
            $output = DCMS_ROOT.'/'.$output;
            
            if(file_exists($input) === false):
                \dcms\Log::write("Input file $input does not seem to exist!", null, 3);
                return false;
            endif;
            if(is_file($input) === false):
                \dcms\Log::write("Can not use $input as input file! (Not a file)", null, 3);
                return false;
            endif;
            
            if(is_writeable(dirname($output)) === false):
                \dcms\Log::write("Output file $output is not writeable!", null, 3);
                return false;
            endif;
            
            try {
                
                $this->lessc->compileFile($input, $output);
                \dcms\Log::write("Successfully compiled less file to CSS.", null, 1);
                return true;
                
            } catch (\Exception $e) {

                $message = $e->getMessage();
                \dcms\Log::write("Could not compile less file $input! ($message)", null, 3);
                return false;
                
            }
        }
        
        /**
         * Die ImportDirs für lessc setzen.
         * 
         * @param array $array
         * @return boolean
         */
        public function set_import_dirs($array = array())
        {
            if(is_array($array) === false):
                \dcms\Log::write('The value for $array is not an array!', null, 3);
                return false;
            endif;
            
            $this->lessc->setImportDir($array);
            return true;
        }
        
        /**
         * Ein Verzeichnis zu den ImportDirs hinzufügen.
         * 
         * @param string $dir
         */
        public function add_import_dir($dir)
        {
            $this->lessc->addImportDir($dir);
        }
        
        /**
         * Eine Funktion für lessc hinzufügen.
         * 
         * @param string $name
         * @param callable $callable
         * @return boolean
         */
        public function register_function($name, $callable)
        {
            if(is_callable($callable) === false):
                \dcms\Log::write("Function $name is not callable!", null, 3);
                return false;
            endif;
            
            $this->lessc->registerFunction($name, $callable);
            return true;
        }
        
        /**
         * Eine benutzerdefinierte Funktion löschen.
         * 
         * @param string $name
         */
        public function unregister_function($name)
        {
            $this->lessc->unregisterFunction($name);
        }
        
        /**
         * Variablen für less setzen.
         * 
         * @param array $vars
         * @return boolean
         */
        public function set_variables($vars)
        {
            if(is_array($vars) === false):
                \dcms\Log::write('The value in $vars is not an array!', null, 3);
                return false;
            endif;
            
            $this->lessc->setVariables($vars);
            return true;
        }
        
        /**
         * Einen Variable löschen
         * 
         * @param string $name
         */
        public function unset_variable($name)
        {
            $this->lessc->unsetVariable($name);
        }
        
    }
