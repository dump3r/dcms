<?php

    namespace dcms\library;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Description of Router
     *
     * @author dump3r
     * @vesion 1.0.0
     * @see http://blaargh.de/dcms/library/Router
     */
    class Router extends \dcms\Singleton {
        
        protected static $instance;
        
        protected $controller_name;
        protected $controller_namespace;
        protected $controller_path;
        protected $controller_instance;
        
        protected $view_name;
        protected $view_namespace;
        protected $view_path;
        protected $view_instance;
        
        protected $method_prefix = 'view_';
        
        public function __construct()
        {
            /* @var $url \dcms\library\Url */
            $url = \dcms\library\Url::get();
            
            /**
             * Die Namen für den Controller und den Namen des Views/der Methode
             * auslesen.
             */
            $this->controller_name = strtolower($url->segment(1));
            $this->view_name = strtolower($url->segment(2));
            
            /**
             * Die Namespaces setzen
             */
            $this->controller_namespace = '\dcms\controller\\'.DCMS_CALL.'\\';
            $this->view_namespace = '\dcms\view\\'.DCMS_CALL.'\\';
            
            /**
             * Die Verzeichnispfade setzen.
             */
            $this->controller_path = DCMS_CALL.'/controller';
            $this->view_path = DCMS_CALL.'/views';
        }
        
        /**
         * Eine Route durchlaufen und den definierten Controller und das View
         * aufrufen.
         * 
         * @param string|null $controller
         * @param string|null $view
         * @return boolean
         */
        public function route($controller = null, $view = null)
        {
            if(empty($controller))
                $controller = $this->controller_name;
            
            if(empty($view))
                $view = $this->view_name;
            
            /**
             * Den Controller aufrufen
             */
            if($this->controller($controller, $view) === true):
                
                /**
                 * Den Hookpoint für post_controller laden
                 */
                \dcms\Hooks::call('post_controller');
                
                if($this->view($controller) === true):
                    
                    /**
                     * Den Hookpoint für post_view laden.
                     */
                    \dcms\Hooks::call('post_view');
                
                    /**
                     * Die Methode des Controllers ausführen.
                     */
                    if($this->controller_method($view) === false)
                        return false;
                
                    return true;
                    
                endif;
                
                \dcms\Log::write("View class $view could not be created!", null, 3);
                
            else:
                
                \dcms\Log::write("Controller $controller could not be created!", null, 3);
            
            endif;
            
            return false;
        }
        
        /**
         * Einen Instanz des Controllers erstellen.
         * 
         * @param string $controller
         * @param string $view
         * @return boolean
         */
        protected function controller($controller, $view)
        {
            $controller_name = $this->controller_namespace.$controller;
            
            /**
             * Den Controller als Datei laden.
             */
            $loaded = \dcms\Core::load_file($controller, $this->controller_path, true, false);
            if($loaded === false):
                \dcms\Log::write("Could not load controller file $controller.php!", null, 3);
                return false;
            endif;
            
            /**
             * Prüfen ob die Controllerklasse existiert.
             */
            if(class_exists($controller_name) === false):
                \dcms\Log::write("Controller class $controller_name could not be found!", null, 3);
                return false;
            endif;
            
            \dcms\Log::write("Creating instance of controller class $controller_name ...", null, 1);
            \dcms\Hooks::call('pre_controller');
            
            $this->controller_instance = new $controller_name($view);
            $this->controller_name = $controller;
            
            return true;
        }
        
        /**
         * Eine Methode im aktuellen Controller ausführen.
         * 
         * @param string $view
         * @return boolean
         */
        protected function controller_method($view)
        {
            $method_name = $this->method_prefix.$view;
            if(method_exists($this->controller_instance, $method_name) === false):
                \dcms\Log::write("Could not call method $method_name on controller!", null, 3);
                return false;
            endif;
            
            \dcms\Log::write("Calling method $method_name on controller ...", null, 1);
            
            $controller_instance = &$this->controller_instance;
            $controller_instance->{$method_name}();
            
            return true;
        }
        
        /**
         * Eine Instanz der Viewklasse erstellen.
         * 
         * @param string $controller
         * @return boolean
         */
        protected function view($controller)
        {
            $view_name = $this->view_namespace.$controller;
            
            /**
             * Veresuchen die Datei zu laden.
             */
            $loaded = \dcms\Core::load_file($controller, $this->view_path, true, false);
            if($loaded === false):
                \dcms\Log::write("Could not load view class file $controller.php!", null, 3);
                return false;
            endif;
            
            /**
             * Prüfen ob die Klasse existiert
             */
            if(class_exists($view_name) === false):
                \dcms\Log::write("View class $view_name.php could not be found!", null, 3);
                return false;
            endif;
            
            \dcms\Log::write("Creating instance of view class $view_name ...", null, 3);
            \dcms\Hooks::call('pre_view');
            
            $this->view_name = $controller;
            $this->view_instance = new $view_name();
            
            return true;
        }
        
        /**
         * Einen 404 Fehler auslösen.
         * 
         * @return void
         */
        public function show_404()
        {
            $custom_route = \dcms\Config::get('route_404', '');
            if(empty($custom_route) === false)
                return $this->_custom_404($custom_route);
            
            /**
             * Eine Standard 404 Seite der Errorklasse anzeiegen.
             */
            \dcms\Error::display('standard_404');
        }
        
        /**
         * Eine benutzerdefinierte Route definieren.
         * 
         * @param string $route_stirng
         * @return void
         */
        protected function _custom_404($route_stirng)
        {
            /* @var $url \dcms\library\Url */
            $url = \dcms\library\Url::get();
            
            /**
             * Den Routenstring in einen Array umwandeln.
             */
            $route_array = $url->parse_string($route_stirng);
            if($route_array === false)
                return \dcms\Error::display('route/failed');
            
            /**
             * Das Routing ausführen
             */
            $result = $this->route($route_array[0], $route_array[1]);
            if($result === false)
                \dcms\Error::display ('route/failed');
        }
        
        /**
         * Die Instanz der Controllerklasse zurückgeben.
         * 
         * @return resource
         */
        public function &get_controller()
        {
            return $this->controller_instance;
        }
        
        /**
         * Die Instanz der Viewklasse zurückgeben.
         * 
         * @return resource
         */
        public function &get_view()
        {
            return $this->view_instance;
        }
        
    }
