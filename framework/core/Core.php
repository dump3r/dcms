<?php

    namespace dcms;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Die Kernklasse des System. Hier kann das komplette Script
     * gestoppt werden und alle weiteren Dateien die geladen werden sind
     * hier abrufbar.
     * 
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/core/core
     */
    class Core {
        
        protected static $files = array();
        protected static $log_available = false;
        
        /**
         * Das aktuelle Script stoppen, den Outputbuffer leeren und eine
         * benutzerdefinierte Nachricht anzeigen.
         * 
         * @param string $reason
         * @return void
         */
        public static function kill($reason) {
            
            /**
             * Den Outputbuffer leeren und damit alle bisher vorbereiteten
             * Header und Cookiedaten verwerfen.
             */
            ob_end_clean();
            
            /**
             * Kann ein aktueller Log dargestellt werden ?
             */
            if(class_exists('\dcms\Log', false) === true):
                if(defined('DCMS_ENVIRONMENT') === true and DCMS_ENVIRONMENT == 'development'):
                    \dcms\Log::display();
                endif;
            endif;
            
            /**
             * Die Nachricht formatieren.
             */
            $header = '<p style="font-family: sans-serif; font-size: 14px; color: #333333;">';
            $footer = '</p>';
            $string = $header.$reason.$footer;
            
            /**
             * Die Nachricht ausgeben und das Script stoppen.
             */
            echo $string;
            exit;
        }
        
        /**
         * Eine Variable via var_dump innerhalb eines pre-Tags ausgeben.
         * 
         * @param mixed $variable
         * @return void
         */
        public static function pre($variable)
        {
            $header = '<pre>';
            $footer = '</pre>';
            
            echo $header, var_dump($variable), $footer;
        }
        
        /**
         * Eine Datei unter einem Tracker listen. Diese Tracker können genutzt
         * werden, um eine Benchmarkklasse zu schreiben und das einbinden von
         * Dateien zu optimieren.
         * 
         * @param string $filepath Der relative Pfad der Datei
         * @param string $tracker Der Tracker für die Datei (Bsp. class, library)
         * @return void
         */
        public static function track_file($filepath, $tracker = 'class')
        {
            /**
             * Wurde die Datei schon unter diesem Tracker geladen ?
             */
            if(isset(self::$files[$tracker]) === true):
                
                if(in_array($filepath, self::$files[$tracker]) === true):
                    return;
                endif;
                
            endif;
            
            /**
             * Die Datei unter diesem Tracker listen.
             */
            self::$files[$tracker][] = $filepath;
        }
        
        /**
         * Die aktuelle Liste aller Datein die einem Tracker zugeordnet sind
         * als Array zurückgeben.
         * 
         * @return array
         */
        public static function get_files()
        {
            return self::$files;
        }
        
        /**
         * Eine Datei in das System laden aber nicht tracken.
         * 
         * @param string $filename Der Dateiname ohne Dateiendung.
         * @param string $directory Das Verzeichnis innerhalb des Systems.
         * @param boolean $once Soll die Datei per require_once eingebunden werden.
         * @param boolean $required Muss die Datei geladen werden.
         * @return boolean
         */
        public static function load_file($filename, $directory = 'framework', $once = true, $required = false)
        {
            /**
             * Den Dateinamen anpassen
             */
            $filename_valid = $filename.'.php';
            
            /**
             * Den absoluten Dateipfad aufbauen
             */
            $path = DCMS_ROOT.'/'.$directory.'/'.$filename_valid;
            
            /**
             * Prüfen ob die Datei existiert.
             */
            if(file_exists($path) === false):
                
                /**
                 * Wenn verfügbar einen Logeintrag erstellen.
                 */
                if(self::$log_available === true):
                    \dcms\Log::write("Could not load file $filename_valid from $directory/", null, 3);
                endif;
                
                /**
                 * Prüfen ob die Datei benötigt wird.
                 */
                if($required === true):
                    self::kill("Die Datei $filename_valid konnte nicht gefunden werden!");
                endif;
                
                /**
                 * Die Datei existiert nicht, also wird FALSE durch return
                 * zurückgegeben.
                 */
                return false;
                
            endif;
            
            /**
             * Wenn verfügbar die Logklasse aufrufen und einen Eintrag erstellen.
             */
            if(self::$log_available === true):
                \dcms\Log::write("Including file $filename_valid from $directory/ ...", null, 1);
            endif;
            
            /**
             * Soll die Datei mit require oder require_once eingebunden werden.
             * In beiden Fällen wird TRUE zurückgegeben.
             */
            if($once === true):
                require_once $path;
                return true;
            endif;
            
            require $path;            
            return true;
        }
        
        /**
         * Eine Kernklasse laden. Alle Kernklassen besitzen nur statische
         * Methoden.
         * 
         * @param string $name Der Klassenname
         */
        public static function core_part($name)
        {
            /**
             * Den Klassennamen anpassen
             */
            $name_valid = ucfirst(strtolower($name));
            
            /**
             * Den relativen Dateipfad und den Dateinamen aufbauen.
             */
            $directory = "framework/core";
            
            /**
             * Prüfen ob die Datei existiert. Wenn nicht wird das
             * Skript gestoppt ansonsten wird die Datei durch
             * require_once geladen.
             */
            self::load_file($name_valid, $directory, true, true);
            
            /**
             * Den Klassennamen überprüfen
             */
            $class_name = "\dcms\\$name_valid";
            if(class_exists($class_name) === false):
                self::kill("Die Kernklasse $class_name wurde nicht definiert!");
            endif;
            
            /**
             * Die Datei tracken
             */
            self::track_file($directory.'/'.$name_valid.'.php', 'core');
            
            /**
             * Prüfen ob man dieses Ergeignis in der Logklasse aufzeichnen kann.
             */
            if(self::$log_available === true):
                \dcms\Log::write("Loaded core class $name_valid", null, 1);
            endif;
        }
        
        /**
         * Dem Kern mitteilen, dass die Logklasse verfügbar ist.
         * 
         * @param boolean $boolean
         */
        public static function _set_log($boolean = true)
        {
            self::$log_available = (boolean) $boolean;
        }
        
        /**
         * Die Verzeichnisse temp, cache und share auf Schreibrechte prüfen.
         * Das System wird gestoppt, sollte in einem der Verzeichnisse nicht
         * geschrieben werden dürfen.
         * 
         * @return void
         */
        public static function _check_directory_permissions()
        {
            $directories = array('cache', 'temp', 'share');
            foreach($directories as $dir):
                
                if(is_writable(DCMS_ROOT.'/'.$dir.'/') === false)
                    self::kill ("Directory <i>$dir</i> is not writeable!");
                
            endforeach;
        }
        
    }