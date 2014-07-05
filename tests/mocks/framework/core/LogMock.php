<?php

    namespace dcms;
    
    class LogMock extends \dcms\Log {
        
        public static function _clear()
        {
            self::$logs = array();
        }
        
    }