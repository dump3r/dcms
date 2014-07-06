<?php

    namespace dcms;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Description of Output
     *
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/core/Output
     */
    class Output {
        
        protected static $http_codes = array(
            '100' => 'Continue',
            '101' => 'Switching Protocols',

            '200' => 'OK',
            '201' => 'Created',
            '202' => 'Accepted',
            '203' => 'Non-Authoriative Information',
            '204' => 'No Content',
            '205' => 'Reset Content',
            '206' => 'Partial Content',

            '300' => 'Multiple Choices',
            '301' => 'Moved Permanently',
            '302' => 'Found',
            '303' => 'See Other',
            '304' => 'Not Modified',
            '305' => 'Use Proxy',
            '306' => '(Unused)',
            '307' => 'Temporary Redirect',

            '400' => 'Bad Request',
            '401' => 'Unauthorized',
            '402' => 'Payment Required',
            '403' => 'Forbidden',
            '404' => 'Not Found',
            '405' => 'Method Not Allowed',
            '406' => 'Not Acceptable',
            '407' => 'Proxy Authentication Required',
            '408' => 'Request Timeout',
            '409' => 'Conflict',
            '410' => 'Gone',
            '411' => 'Length Required',
            '412' => 'Precondition Failed',
            '413' => 'Request Entity Too Large',
            '414' => 'Request-URI Too Long',
            '415' => 'Unsupported Media Type',
            '416' => 'Request Range Not Satisfiable',
            '417' => 'Expectation Failed',

            '500' => 'Internal Server Error',
            '501' => 'Not Implemented',
            '502' => 'Bad Gateway',
            '503' => 'Service Unavailable',
            '504' => 'Gateway Timeout',
            '505' => 'HTTP Version Not Supported'
        );
        
        /**
         * Den Outputbuffer leeren.
         * 
         * @return void
         */
        public static function clear()
        {
            ob_clean();
        }
        
        /**
         * Einen HTTP Statuscode senden.
         * Der Outputbuffer wird nicht geleert!
         * 
         * @param string $code
         * @return void
         */
        public static function status($code = '200')
        {
            /**
             * Existiert der Statuscode
             */
            if(isset(self::$http_codes[$code]) === false):
                \dcms\Log::write("Unkown status code $code ... sending internal server error header!", null, 3);
                $code = '500';
                return;
            endif;
            
            /**
             * Den Statuscode senden
             */
            $status = self::$http_codes[$code];
            $server_protocol = $_SERVER['SERVER_PROTOCOL'];
            
            header("$server_protocol $code $status");
            \dcms\Log::write("Sending HTTP header $code - $status", null, 1);
        }
        
        /**
         * Einen Content-type Header senden.
         * 
         * @param string $type
         * @return void
         */
        public static function content_type($type = 'text/html')
        {
            /**
             * Den Header senden
             */
            header('Content-type: '.$type);
            \dcms\Log::write("Changed content type to $type", null, 1);
        }
        
    }
