<?php

    namespace dcms;

    class CoreMock extends \dcms\Core {
        
        public static function _clear_tracker()
        {
            self::$files = array();
        }
        
    }
