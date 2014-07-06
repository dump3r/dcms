<?php

    namespace dcms\template;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Description of Standard
     *
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/template/Standard
     */
    class Standard {
        
        protected $_template_values = array();
        protected $_template_path;
        protected $_template_file;
        
        protected $_cache_path = 'cache';
        protected $_cache_set = false;
        
        public function __construct($file, $directory = null)
        {
            /**
             * Das Templateverzeichnis setzen.
             */
            $this->_set_template_directory($directory);
            
            /**
             * Den Templatenamen setzen.
             */
            $this->_set_template_name($file);
            
            /**
             * Prüfen ob ein Cache con diesem Template existiert.
             */
            $this->_check_cache();
        }
        
        /**
         * Prüft ob das aktuelle Template bereits eine Cachekopie besitzt.
         * Wird eine Cachekopie gefunden wird das Template nicht erneut geparst.
         * 
         * @return boolean
         */
        public function is_cached()
        {
            return $this->_cache_set;
        }
        
        /**
         * Eine Variable an das Template weitergeben.
         * Unter dem Wert in $name wird die Variable im Template
         * verfügbar sein.
         * 
         * @param string $name
         * @param mixed $value
         * @return \dcms\template\Standard
         */
        public function assign($name, $value)
        {
            if(isset($this->_template_values[$name]) === true)
                \dcms\Log::write("Overwriting existing template variable $name", null, 2);
            
            $this->_template_values[$name] = $value;
            
            /**
             * Die aktuelle Instanz zurückgeben, damit Method-Chaining genutz
             * werden kann.
             */
            return $this;
        }
        
        /**
         * Eine Templatevariable löschen.
         * 
         * @param type $name
         * @return void
         */
        public function remove($name)
        {
            if(isset($this->_template_values[$name]) === false)
                \dcms\Log::write("Can not unset non-existing template variable $name", null, 2);
            
            unset($this->_template_values[$name]);
        }
        
        /**
         * Das Template anzeigen (an den Browser senden).
         * 
         * @return void
         */
        public function display($skip_cache = false)
        {
            $template_value = $this->_load_template($skip_cache);
            echo $template_value;
        }
        
        /**
         * Das Template parsen und als String zurückgeben.
         * Es erfolgt keine Ausgabe an den Browser.
         * 
         * @return string|boolean
         */
        public function fetch($skip_cache = false)
        {
            $template_value = $this->_load_template($skip_cache);
            return $template_value;
        }
        
        /**
         * Eine Cachedatei aus dem aktuellen Template erstellen.
         * 
         * @return boolean
         */
        public function create_cache()
        {          
            /**
             * Die alte Datei löschen sofern vorhanden.
             */
            if($this->_cache_set == true)
                $this->destroy_cache();
            
            /**
             * Die Prüfsumme des Templatepfades ermitteln.
             */
            $filepath = $this->_get_cache_path();
            $filename = $this->_hash_filename($this->_template_path.$this->_template_file).'.html';
            
            /**
             * Den Inhalt des Templates parsen.
             */
            $content = $this->fetch();
            if($content === false):
                \dcms\Log::write('Could not create cache for '.$this->_template_file, null, 3);
                return false;
            endif;
            
            /**
             * Den Inhalt schreiben.
             */
            $file = new \dcms\library\File();
            $file->open($filepath.$filename, 'w');
            
            $write_result = $file->write($content);
            $file->close();
            
            if($write_result === false):
                \dcms\Log::write("Could not write into cache file $filename", null, 3);
                return false;
            endif;
            
            $this->_cache_set = true;
            return $filepath.$filename;
        }
        
        /**
         * Die Cachekopie von diesem Template löschen sofern eine erstellt wurde.
         * 
         * @return boolean
         */
        public function destroy_cache()
        {
            if($this->_cache_set === false)
                return true;
            
            $filename = $this->_hash_filename($this->_template_path.$this->_template_file).'.html';
            $unlink = unlink($this->_get_cache_path().$filename);
            
            if($unlink === true):
                $this->_cache_set = false;
                return true;
            endif;
            
            \dcms\Log::write("Could not delete cache file $filename!", null, 3);
            return false;
        }
        
        /**
         * Prüfen ob das aktuelle Template eine Cachekopie besitzt.
         * 
         * @return void
         */
        protected function _check_cache()
        {
            $filename = $this->_hash_filename($this->_template_path.$this->_template_file).'.html';
            $filepath = $this->_get_cache_path().$filename;
            
            if(file_exists($filepath) === true)
                $this->_cache_set = true;
        }
        
        /**
         * Den Pfad zum cache-Verzeichnis zurückgeben.
         * 
         * @return string
         */
        protected function _get_cache_path()
        {
            return DCMS_ROOT.'/'.$this->_cache_path.'/';
        }
        
        /**
         * Ein Hash aus einem Dateinamen/Pfad erstellen.
         * 
         * @param string $filename
         * @return string
         */
        protected function _hash_filename($filename)
        {
            return hash('haval160,4', $filename);
        }

        /**
         * Das Template parsen und als String zurückgeben.
         * 
         * @param boolean $skip_cache
         * @return string|boolean
         */
        protected function _load_template($skip_cache = false)
        {   
            $filepath = $this->_template_path.$this->_template_file;
            if(file_exists($filepath) === false):
                \dcms\Log::write("Could not load template $filepath!", null, 3);
                return false;
            endif;
            
            /**
             * Prüfen ob eine Cachekopie vorhanden ist.
             */
            if($skip_cache === false && $this->_cache_set === true):
                return $this->_load_cached();
            endif;
            
            ob_start();
            chdir($this->_template_path);
            extract($this->_template_values);
            require $filepath;
            return ob_get_clean();
        }
        
        /**
         * Die Cachekopie laden.
         * 
         * @return string
         */
        protected function _load_cached()
        {
            $filename = $this->_hash_filename($this->_template_path.$this->_template_file).'.html';
            return file_get_contents($this->_get_cache_path().$filename);
        }
        
        /**
         * Das Templateverzeichnis setzen. Wird kein Wert für
         * $directory übergeben, wird das Standardverzeichnis
         * abhängig von DCMS_CALL genutzt.
         * 
         * @param string $directory
         * @return void
         */
        protected function _set_template_directory($directory = null)
        {
            if(empty($directory) === false):
                $this->_template_path = DCMS_ROOT.'/'.$directory;
                return;
            endif;
            
            $theme_path = \dcms\Config::get('theme_name', '');
            if(empty($theme_path) === false)
                $theme_path .= '/';
            
            $this->_template_path = DCMS_ROOT.'/'.DCMS_CALL.'/themes/'.$theme_path;
        }
        
        /**
         * Den Templatenamen setzen. Die Dateiendung wird automatisch aus
         * der Konfiguration geladen.
         * 
         * @param string $file
         * @return void
         */
        protected function _set_template_name($file)
        {
            $template_extension = \dcms\Config::get('theme_extension', '.php');
            $this->_template_file = $file.$template_extension;
        }
        
    }
