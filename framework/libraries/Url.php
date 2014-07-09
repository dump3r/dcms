<?php

    namespace dcms\library;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');
    
    /**
     * Die URL-Klasse verwaltet die URL-Segmente der Anfrage und kann genutzt
     * werden um URLs zu generieren.
     * 
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/library/Url
     */
    class Url extends \dcms\Singleton {
        
        protected static $instance;
        protected $string;
        protected $segments = array();
        
        public function __construct() 
        {
            /**
             * Den URL String auslesen
             */
            $this->_get_string();
            
            /**
             * Den URL String auf vebotene Werte überprüfen.
             */
            $this->_validate_string();
            
            /**
             * Den String parsen und in Segmente unterteilen
             */
            $this->_parse_string();
            
            /**
             * Die ersten beiden Elemente prüfen
             */
            $this->_check_segments();
            
            \dcms\Log::write('Done loading the url class', null, 1);
        }
        
        /**
         * Ein URL Segment auslesen. Wurde das Segment nicht gesetzt, 
         * wird der Wert in $default zurückgegeben.
         * Wird für $n eine Zahl kleiner als 1 übergeben,
         * wird der ganze Array mit Segmenten zurückgegeben.
         * 
         * @param int $n
         * @param mixed $default
         * @return mixed
         */
        public function segment($n = 0, $default = false)
        {
            /**
             * $n eins runterzählen.
             */
            $n--;
            
            /**
             * Wenn ein Wert kleiner als 0  für $n übergeben wurde,
             * wird der ganze Array zurückgegeben.
             */
            if($n < 0)
                return $this->segments;
            
            /**
             * Entweder das Segment oder den Wert in $default zurückgeben..
             */
            if(isset($this->segments[$n]) === false)
                return $default;
            
            return $this->segments[$n];
        }
        
        /**
         * Einen URL-String erstellen.
         * 
         * @param string $uri
         * @return string
         */
        public function base_url($uri = '')
        {
            /**
             * Die Standardwert festlegen.
             */
            $protocol = (isset($_SERVER['HTTPS']) === false or $_SERVER['HTTPS'] == 'off' ? 'http://' : 'https://');
            $default_url = $protocol.$_SERVER['SERVER_NAME'].'/';
            
            /**
             * Die URL aus der Konfiguration auslesen.
             */
            $base_url = \dcms\Config::get('url_base', $default_url);
            
            /**
             * Den URL String aufbauen
             */
            return $base_url.$uri;
        }
        
        /**
         * Eine URL erstellen.
         * 
         * @param string $uri
         * @return string
         */
        public function index_url($uri = '')
        {
            $index_file = \dcms\Config::get('url_index', 'index.php');
            $rewrite_url = \dcms\Config::get('url_rewrite', false);
            
            $string = $index_file.'/'.$uri;
            if($rewrite_url === false)
                return $this->base_url($string);
            
            return $this->base_url('public/'.$string);
        }
        
        /**
         * Einen String parsen und in Segmente unterteilen.
         * 
         * @param string $string
         * @return array|boolean
         */
        public function parse_string($string)
        {
            if(is_string($string) === false):
                \dcms\Log::write('No string supplied!', null, 3);
                return false;
            endif;
            
            $pattern_part = \dcms\Config::get('url_pattern', '');
            if(empty($pattern_part) === false):
                $pattern = '#[^'.$pattern_part.']#';
                $result = preg_match($pattern, $string);
                if($result === false or $result > 0):
                    \dcms\Log::write('Could not parse string! Forbidden characters found.', null, 3);
                    return false;
                endif;
            endif;
            
            $array = explode('/', $string);
            if(isset($array[0]) === false):
                \dcms\Log::write('No array elements found!', null, 3);
                return false;
            endif;
            if(isset($array[1]) or empty($array[1]))
                $array[1] = 'index';
            
            return $array;
        }
        
        /**
         * Den URL String auslesen. Es wird zunächst nach der PATH_INFO
         * Variable in $_SERVER gesucht. Wird diese nicht gefunden, wird
         * der Standardwert der Konfiguration ('route_default') genutzt.
         * 
         * @return void
         */
        protected function _get_string()
        {
            /**
             * Den Standardwert aus der Konfiguration laden.
             */
            $this->string = \dcms\Config::get('route_default', 'welcome/index');
            
            /**
             * Nach der PATH_INFO Variable suchen.
             */
            $path_info = filter_input(INPUT_SERVER, 'PATH_INFO', FILTER_SANITIZE_URL);
            if(empty($path_info) === false):
                $string = substr($path_info, 1);
                if(empty($string) === false):
                    $this->string = $string;
                endif;
            endif;
        }
        
        /**
         * Den URL Stirng durch preg_match prüfen. Es wird nur geprüft,
         * wenn ein Wert für url_pattern in der Knfiguration angegeben wurde.
         * 
         * @return void
         */
        protected function _validate_string()
        {
            /**
             * Den Pattern aus der Konfiguration auslesen.
             */
            $pattern = \dcms\Config::get('url_pattern', '');
            
            /**
             * Nur den String checken, wenn der Pattern nicht leer ist.
             */
            if(empty($pattern) === true)
                return;
            
            /**
             * Den URL String mit preg_match prüfen.
             */
            $result = preg_match('#[^'.$pattern.']#', $this->string);
            if($result > 0 or $result === false):
                \dcms\Error::display('url/forbidden_chars');
            endif;
            
            return;
        }
        
        /**
         * Den URL String in Segmente unterteilen und
         * prüfen ob alle wichtigen Elemente gesetzt sind.
         * 
         * @return void
         */
        protected function _parse_string()
        {
            $array = explode('/', $this->string);
            
            /**
             * Wurde das zweite Element gesetzt.
             */
            if(isset($array[1]) === false or empty($array[1]))
                $array[1] = 'index';
            
            
            $this->segments = $array;
        }
        
        /**
         * Die ersten beiden Segmente auf verbotene Zeichen prüfen.
         * 
         * @return void
         */
        protected function _check_segments()
        {
            $segments = array($this->segments[0], $this->segments[1]);
            foreach($segments as $key => $segment):
                
                $string = str_replace('-', '_', $segment);
                $this->segments[$key] = $string;
                
            endforeach;
        }
        
    }