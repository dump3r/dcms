<?php

    namespace dcms;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');
    
    /**
     * Die Errorklasse wird verwendet um Fehlermeldungen und
     * Informationen über das System im HTML-Format anzuzeigen.
     * 
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/core/Error
     */
    class Error {

        protected static $_template_path = 'framework/error';
        
        /**
         * Eine Fehlerseite anzeigen.
         * 
         * @param string $file
         */
        public static function display($file)
        {
            /**
             * Die Dateiendung der Templatedateien verändern.
             * Das System wird danach gestoppt also muss der Wert nicht
             * wieder zurückgesetzt werden.
             */
            \dcms\Config::set('theme_extension', '.php');
            
            /**
             * Das Template laden.
             */
            $template = new \dcms\template\Standard('wrapper', self::$_template_path);
            $template->assign('base_url', \dcms\Config::get('url_base', ''));
            $template->assign('template', $file);
            
            /**
             * Das Template anzeigen.
             */
            $template->display();
            
            /**
             * Das System stoppen.
             */
            exit;
        }
        
    }