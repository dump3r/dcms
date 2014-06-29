<?php

    namespace dcms;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Die Logklasse wird genutzt, um Ereignisse, Warnungen und Fehler
     * anderer Klasse aufzuzeichnen.
     * Diese Logdateien können entweder als HTML ausgegeben oder als Datei
     * gespeichert werden.
     *
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/core/Log
     */
    class Log {
        
        protected static $logs = array();
        protected static $last_message = '';
        
        protected static $format = 'H:i:s';
        protected static $level = array('Undefined', 'Information', 'Warning', 'Error');
        
        protected static $folder = 'share/logs';
        
        /**
         * Einen Logeintrag erstellen.
         * 
         * @param string $message
         * @param mixed $origin
         * @param int $level
         */
        public static function write($message, $origin, $level = 1)
        {
            /**
             * Existiert das Loglevel
             */
            $level_checked = self::_check_level($level);
            
            /**
             * Das Level herausfinden
             */
            $level_string = self::$level[$level_checked];
            
            /**
             * Den Zeitstempel formatieren
             */
            $timestamp = self::_format_timestamp(self::$format, time());
            
            /**
             * Die Herkunft bestimmen
             */
            $origin_string = self::_get_origin($origin);
            
            /**
             * Die Lognachricht absichern
             */
            $message_stripped = strip_tags($message);
            
            /**
             * Die Nachricht zusammensetzen
             */
            $log_string = "[$timestamp][$level_string][$origin_string] $message_stripped";
            
            /**
             * Die Nachricht eintragen
             */
            self::$logs[] = $log_string;
            self::$last_message = $log_string;
        }
        
        /**
         * Den aktuellen Log im HTML-Format ausgeben.
         * Die Ausgabe wird innerhalb eines p-Tags gemacht.
         * 
         * @return void
         */
        public static function display()
        {
            $header = '<p style="font-family: monospace; line-height: 1.5em;">';
            $footer = '</p>';
            
            $message = '<strong>Current Log:</strong><br />';
            $logs = 'No Logs available!';
            
            if(empty(self::$logs) === false):
                $logs = implode('<br />', self::$logs);
            endif;
            
            $output = $header.$message.$logs.$footer;
            echo $output;
        }
        
        /**
         * Alle gesammelten Logeinträge als Array zurückgeben.
         * 
         * @return array
         */
        public static function fetch()
        {
            return self::$logs;
        }
        
        /**
         * Den aktuellen Log in eine Datei speichern. Diese Methode gibt
         * entweder FALSE zurück oder den absoluten Dateipfad zur
         * erstellten Logdatei.
         * 
         * @return boolean|string
         */
        public static function save()
        {
            $folder = self::$folder;
            $basepath = DCMS_ROOT.'/'.$folder;
            
            $timestamp = time();
            $filename = $timestamp.'.log';
            $filepath = $basepath.'/'.$filename;
            
            /**
             * Existiert das Verzeichnis und die Datei schon ?
             */
            if(self::_check_directory_and_file($folder, $basepath, $filepath) === false):
                return false;
            endif;
            
            /**
             * Open the file handle
             */
            $handle = fopen($filepath, 'w');
            if($handle === false):
                self::write("Could not open file handle for $filepath!", self, 3);
                return false;
            endif;

            $content = self::_format_file_content($filename, $timestamp);
            return self::_write_file_content($handle, $content, $filepath);
        }
        
        /**
         * Prüfen, ob das Logverzeichnis beschreibbar ist und existiert.
         * Es wird versucht das Verzeichnis zu erstellen, wenn es nicht da ist.
         * Zudem wird geprüft, ob die Datei schon existiert bzw. ob diese
         * erstellt werden kann.
         * 
         * @param string $folder
         * @param string $basepath
         * @param string $filepath
         * @return boolean
         */
        protected static function _check_directory_and_file($folder, $basepath, $filepath)
        {
            /**
             * Existiert das Verzeichnis schon ?
             */
            if(is_dir($basepath) === false):
                self::write(
                    "The log directory $folder does not seem to exist! Attempting to create it ...", 
                    self, 
                    2
                );
                /**
                 * Versuchen das Verzeichnis zu erstellen.
                 */
                $mkdir = mkdir($basepath);
                if($mkdir === false):
                    
                    self::write("The log directory $folder could not be created!", null, 3);
                    return false;
                    
                endif;
            endif;
            
            /**
             * Kann in das Verzeichnis geschrieben werden ?
             */
            if(is_writeable($basepath) === false):
                self::write("Log file directory $folder is not writeable!", null, 3);
                return false; 
            endif;
            
            /**
             * Wurde die Datei schon erstellt ?
             */
            if(file_exists($filepath) === true):
                self::write("The log file at $filepath already exist!", null, 3);
                return false;
            endif;
            
            return true;
        }
        
        /**
         * Versuchen Inhalt in die Logdatei zu schreiben.
         * 
         * @param resource $handle
         * @param string $content
         * @return boolean
         */
        protected static function _write_file_content($handle, $content, $filepath)
        {
            $fwrite = fwrite($handle, $content);
            fclose($handle);
            
            if($fwrite === false):
                self::write("Could not write logs to $filepath!", self, 3);
                return false;
            endif;
            return $filepath;
        }
        
        /**
         * Den Inhalt einer Logdatei erstellen. Der Header und die Logeinträge
         * werden automatisch formatiert.
         * 
         * @param string $filename
         * @param int $timestamp
         * @return string
         */
        protected static function _format_file_content($filename, $timestamp)
        {
            /**
             * Die Kopfzeile zusammenbauen
             */
            $header = array();
            $header[] = "################ log $timestamp ################";
            $header[] = "## filename: ".$filename;
            $header[] = "## created: ".self::_format_timestamp('d.m.Y \a\t H:i:s', $timestamp);
            $header[] = "## remote address: ".$_SERVER['REMOTE_ADDR'];
            $header[] = "## request method: ".$_SERVER['REQUEST_METHOD'];
            $header[] = "#######################################################";
            
            $header_string = implode("\n", $header);
            
            /**
             * Die Logeinträge zusammensetzen
             */
            $logs = "no log messages";
            if(empty(self::$logs) === false):
                $logs = implode("\n", self::$logs);
            endif;
            
            /**
             * Den fertigen Dateiinhalt zurückgeben.
             */
            $return = $header_string."\n".$logs;
            return $return;
        }
        
        /**
         * Überprüfen, ob das Verzeichnis für die Logdateien existiert und
         * beschreibbar ist.
         * 
         * @param string $basepath
         * @param string $folder
         * @return boolean
         */
        protected static function _check_directory($basepath, $folder)
        {   
            if(is_dir($basepath) === false):
                
                self::write("Log directory $folder does not exist! Attempting to create it ...", self, 2);
            
                $mkdir = mkdir($basepath);
                if($mkdir === false):
                    self::write("Could not create log file directory $folder!", self, 3);
                    return false;
                endif;
                
            endif;
            
            if(is_writeable($basepath) === false):
                self::write("The log directory $folder is not writeable!", self, 3);
                return false;
            endif;
            
            return true;
        }
        
        /**
         * Das Loglevel validieren. Wenn das Level nicht existiert
         * wird 0 zurückgegeben.
         * 
         * @param int $integer
         * @return int
         */
        protected static function _check_level($integer)
        {
            $level = self::$level;
            $return = (int) $integer;
            
            if(is_numeric($integer) === false):
                $return = 0;
            endif;
            
            if(isset($level[$integer]) === false):
                $return = 0;
            endif;
            
            return $return;
        }
        
        /**
         * Einen UNIX Zeitstempel durch den Wert in $format formatieren.
         * Wird für $time ein Wert unter 0 geliefert, wird der aktuelle
         * Zeitstempel genutzt.
         * 
         * @param string $format
         * @param int $time
         * @return string
         */
        protected static function _format_timestamp($format = 'H:i:s', $time = -1)
        {
            $timestamp = (int) $time;
            if(is_numeric($time) === false || $time < 0):
                $timestamp = (int) time();
            endif;
            
            $formatted_time = date($format, $timestamp);
            return $formatted_time;
        }
        
        /**
         * Die Herkunft eines Logeintrages herausfinden. Es kann für
         * $origin direkt ein String übergeben werden. Wird etwas anders
         * als ein String übergeben, wird versucht die Herkunft mit
         * debug_backtrace() herauszufinden.
         * 
         * @param mixed $origin
         * @return string
         */
        protected static function _get_origin($origin)
        {
            if(is_string($origin) === true):
                return $origin;
            endif;
            
            $debug = debug_backtrace(false);
            $new_origin = $debug[2];
            
            $function = $new_origin['function'];
            $class = '';
            
            if(isset($new_origin['class']) === true):
                $class  = $new_origin['class'].'::';
            endif;
            
            $origin_string = $class.$function;
            return $origin_string;
        }

    }
