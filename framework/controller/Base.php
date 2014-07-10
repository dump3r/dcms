<?php

    namespace dcms\controller;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Description of Base
     *
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/controller/Base
     */
    class Base {

        protected $view;
        
        public function __construct() 
        {
            \dcms\Hooks::call('construct_controller');
            
            /**
             * Die Viewinstanz importieren
             */
            $route = \dcms\library\Router::get();
            $this->view = &$route->get_view();
        }
        
    }
