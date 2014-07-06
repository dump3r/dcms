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
        
        public function __construct($file, $directory = null)
        {
            /**
             * Das Templateverzeichnis setzen.
             */
            $this->_set_template_directory($directory);
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
        
    }
