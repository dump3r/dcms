<?php
    
    namespace dcms\library;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Description of File
     *
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/library/File
     */
    class File {
        
        protected $filepath;
        protected $handle;
        
        /**
         * @param string $filepath
         */
        public function __construct($filepath, $prepend_basedir = false) {
            $this->filepath = $filepath;
            
            if($prepend_basedir === true)
                $this->filepath = DCMS_ROOT.'/'.$filepath;
        }
        
        /**
         * Prüfen ob die Datei bereits existiert.
         * 
         * @param boolean $prevent_log
         * @return boolean
         */
        public function exists($prevent_log = false)
        {
            if(file_exists($this->filepath) === false):
                
                if($prevent_log === false)
                    \dcms\Log::write("File {$this->filepath} does not exist!", null, 3);
                
                return false;
                
            endif;
            return true;
        }
        
        /**
         * Prüfen ob die Datei beschrieben bzw. erstellt werden kann.
         * 
         * @param boolean $prevent_log
         * @return boolean
         */
        public function is_writeable($prevent_log = false)
        {
            $path = $this->filepath;
            if($this->exists(false) === false)
                $path = dirname($this->filepath);
            
            $result = is_writeable($path);
            if($result === false):
                
                if($prevent_log === false)
                    \dcms\Log::write("File {$this->filepath} is not writeable!", null, 3);
                
                return false;
                
            endif;
            
            return true;
        }
        
        /**
         * Prüfen ob eine Datei gelesen werden kann.
         * 
         * @param boolean $prevent_log
         * @return boolean
         */
        public function is_readable($prevent_log = false)
        {
            $path = $this->filepath;
            if($this->exists(false) === false)
                $path = dirname($this->filepath);
            
            $result = is_readable($path);
            if($result === false):
                
                if($prevent_log === false)
                    \dcms\Log::write("File {$this->filepath} is not readable!", null, 3);
                    
                return false;
            endif;
            
            return true;
        }
        
        /**
         * Versuchen einen Filehandler zu öffnen.
         * 
         * @param string $mode
         * @return boolean
         */
        public function open($mode = 'w')
        {
            $this->handle = fopen($this->filepath, $mode);
            if($this->handle === false):
                \dcms\Log::write("Could not create filehandle for {$this->filepath}!", null, 3);
                return false;
            endif;
            return true;
        }
        
        /**
         * Einen String in die aktuelle Datei schreiben.
         * 
         * @param string $content
         * @return boolean
         */
        public function write($content)
        {
            if($this->handle === false)
                return false;
            
            $fwrite = fwrite($this->handle, $content);
            if($fwrite === false):
                \dcms\Log::write('Could not write content to '.$this->filepath, null, 3);
                $this->close();
                return false;
            endif;
            
            return true;
        }
        
        /**
         * Liest die Anzahl an Bytes aus der aktuellen Datei.
         * 
         * @param int $length
         * @return boolean
         */
        public function read($length)
        {
            if($this->handle === false)
                return false;
            
            $fread = fread($this->handle, $length);
            if($fread !== false)
                return $fread;
            
            \dcms\Log::write("Could not execute fread on current filehandle!", null, 3);
            return false;
        }
        
        /**
         * Den aktuellen Filehandle schliessen.
         * 
         * @return boolean
         */
        public function close()
        {
            if($this->handle === false):
                \dcms\Log::write('Can not close non-existing filehandle!', null, 3);
                return false;
            endif;
            
            $fclose = fclose($this->handle);
            if($fclose === false):
                \dcms\Log::write('Could not close filehandle for '.$this->filepath, null, 3);
                return false;
            endif;
            
            return true;
        }
        
        /**
         * Gibt den aktuellen Filehandler zurück, 
         * sofern einer vorhanden ist.
         * 
         * @return boolean|resource
         */
        public function get_handler()
        {
            if($this->handle !== false)
                return $this->handle;
            
            \dcms\Log::write("Can not return non-existing filhandle!", null, 3);
            return false;
        }
        
    }
