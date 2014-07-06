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
         * Einen Filehandler für die Datei in $filepath erstellen.
         * Als $mode kann alles genutzt werden, was die fopen()-Funktion
         * unterstützt.
         * 
         * @param string $filepath
         * @param string $mode
         * @return boolean
         */
        public function open($filepath, $mode = 'w')
        {
            $this->filepath = $filepath;
            $this->handle = fopen($filepath, $mode);
            if($this->handle === false):
                \dcms\Log::write("Could not create filehandle for $filepath!", null, 3);
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
        
    }
