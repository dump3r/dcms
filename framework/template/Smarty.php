<?php

    namespace dcms\template;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');
    
    /**
     * Prüfen ob die Smartyklasse verfügbar ist.
     */
    if(class_exists('\Smarty') === false):
        \dcms\Log::write('Could not find class Smarty! Please use composer install to install Smarty', 'init', 3);
        \dcms\Core::kill('The Smarty template engine is required!');
    endif;

    /**
     * Description of Smarty
     *
     * @author Monte Ohrt
     * @author Uwe Tews
     * @author Rodney Rehm
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/template/Smarty
     */
    class Smarty extends \Smarty {
        
        protected $_template_file;
        
        public function __construct($file, $directory = null) 
        {
            parent::__construct();
            
            $theme_extension = \dcms\Config::get('theme_extension', '.tpl');
            $this->_template_file = $file.$theme_extension;
            
            if(empty($directory) === true):
                
                $theme_name = \dcms\Config::get('theme_name', '');
                $directory = DCMS_CALL.'/themes/';
                
                if(empty($theme_name) === false)
                    $directory .= $theme_name.'/';
                
            endif;
            $template_directory = DCMS_ROOT.'/'.$directory;
            
            /**
             * Set the path variables for smarty.
             */
            $this->setTemplateDir($template_directory);
            $this->setCacheDir(DCMS_ROOT.'/cache/');
            $this->setCompileDir(DCMS_ROOT.'/temp/');
            $this->setConfigDir(DCMS_ROOT.'/config/');
        }
        
        /**
         * Das Template anzeigen (zum Browser senden).
         * 
         * @param string $template   the resource handle of the template file or template object
         * @param mixed  $cache_id   cache id to be used with this template
         * @param mixed  $compile_id compile id to be used with this template
         * @param object $parent     next higher level of Smarty variables
         * @return void
         */
        public function display($template = null, $cache_id = null, $compile_id = null, $parent = null)
        {
            if(empty($template) === true)
                $template = $this->_template_file;
            
            try {
                parent::display($template, $cache_id, $compile_id, $parent);
            } catch(\SmartyException $e) {
                $template_file = $this->_template_file;
                $error = $e->getMessage();
                \dcms\Log::write("Could not display template $template_file! ($error)", null, 3);
            }
        }
        
        /**
         * Das Template parsen und als String zurückgeben (nicht an den Browser senden).
         * 
         * @param  string $template         the resource handle of the template file or template object
         * @param  mixed $cache_id         cache id to be used with this template
         * @param  mixed $compile_id       compile id to be used with this template
         * @param  object $parent           next higher level of Smarty variables
         * @param  boolean $display          true: display, false: fetch
         * @param  boolean $merge_tpl_vars   if true parent template variables merged in to local scope
         * @param  boolean $no_output_filter if true do not run output filter
         * @return string rendered template output
         */
        public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
        {
            if(empty($template) === true)
                $template = $this->_template_file;
            
            try {
                parent::fetch(
                    $template, 
                    $cache_id, 
                    $compile_id, 
                    $parent, 
                    $display, 
                    $merge_tpl_vars, 
                    $no_output_filter
                );
            } catch (\SmartyException $e) {
                $template_file = $this->_template_file;
                $error = $e->getMessage();
                \dcms\Log::write("Could not fetch template $template_file! ($error)", null, 3);
            }
        }
        
    }
