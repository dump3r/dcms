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
        
        private static $logs = array();
        private static $last_message = '';
        
        private static $format = 'H:i:s';
        private static $level = array('Undefined', 'Information', 'Warning', 'Error');
        
        private static $folder = 'temp/_logs/';
        
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
        
        public static function save()
        {
            
        }
        
        /**
         * Das Loglevel validieren. Wenn das Level nicht existiert
         * wird 0 zurückgegeben.
         * 
         * @param int $integer
         * @return int
         */
        private static function _check_level($integer)
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
        private static function _format_timestamp($format = 'H:i:s', $time = -1)
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
        private static function _get_origin($origin)
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
