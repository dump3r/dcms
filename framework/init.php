<?php
    
    /**
     * Die Startdatei des Frameworks.
     * Alle wichtigen Dateien, Klassen und Abhängigkeiten werden hier geladen.
     * Zudem wird eine Datenbankverbindung aufgebaut.
     */
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');
    
    /**
     * Die systemweiten Konstanten laden
     */
    require_once dirname(__FILE__).'/../constants.php';
    
    /**
     * Die statische Kernklasse laden
     */
    require_once DCMS_FRAMEWORK_DIR.'/core/Core.php';
    
    /**
     * Die statische Logklasse laden
     */
    \dcms\Core::core_part('Log');
    \dcms\Core::_set_log(true);
    
    /**
     * Prüfen ob die Verzeichnisse cache, temp und share beschrieben werden können.
     */
    \dcms\Core::_check_directory_permissions();
    
    /**
     * Die Filelibrary laden.
     */
    \dcms\Core::load_file('File', 'framework/libraries', true, true);
    
    /**
     * Die statische Configklasse laden
     */
    \dcms\Core::core_part('Config');
    
    /**
     * Die notwendigen Konfigurationsdateien aus dem config-Verzeichnis laden
     */
    $config_files = array('main', DCMS_CALL);
    $config_directory = 'config';
    
    foreach($config_files as $config_file):
        
        \dcms\Core::load_file($config_file, $config_directory, false, true);
    
        $filepath = $config_directory.'/'.$config_file.'.php';
        \dcms\Core::track_file($filepath, 'config');
        \dcms\Log::write("Loaded config file from $filepath", 'init', 1);
        
    endforeach;
    
    /**
     * Die Umgebungsabhängigen Konfigurationsdateien laden.
     * Diese Dateien müssen nicht vorhanden sein.
     */
    $directory = $config_directory.'/'.DCMS_ENVIRONMENT;
    foreach($config_files as $config_file):
        
        $config_loaded = \dcms\Core::load_file(
            $config_file, 
            $directory, 
            false, 
            false
        );
    
        if($config_loaded === true):
            $filepath = $directory.'/'.$config_file.'.php';
            \dcms\Core::track_file($filepath, 'config');
            \dcms\Log::write("Loaded additional config file $filepath", 'init', 1);
        endif;
        
    endforeach;
    
    /**
     * Die Umgebungskonstante auswerten.
     */
    switch(DCMS_ENVIRONMENT):
        
        case 'development':
            error_reporting(E_ALL);
            break;
        
        case 'testing':
            break;
        
        case 'production':
            error_reporting(0);
            break;
        
    endswitch;
    
    /**
     * Nach der autoload.php im third-party Verzeichnis suchen.
     */
    \dcms\Core::load_file('autoload', 'third_party', true, true);
    
    /**
     * Die Template Engines laden.
     */
    $template_engines = array(
        'Standard' => 'framework/template',
        'Smarty'   => 'framework/template',
        'Markdown' => 'framework/template'
    );
    foreach($template_engines as $name => $directory):
        
        \dcms\Core::load_file($name, $directory, true, true);
        \dcms\Core::track_file($directory.'/'.$name.'.php', 'template');
        
    endforeach;
    
    /**
     * Die statische Errorklasse laden.
     */
    \dcms\Core::core_part('Error');
    
    /**
     * Die statische Loaderklasse laden
     */
    \dcms\Core::core_part('Loader');
    
    /**
     * Die Singletonklasse laden
     */
    \dcms\Core::load_file('Singleton', 'framework/core', true, true);
    \dcms\Core::track_file('framework/core/Singleton.php', 'singleton');
    
    /**
     * Die statische Hookklasse laden
     */
    \dcms\Core::core_part('Hooks');
    
    /**
     * Nach einer benutzerdefinierten hooks.php suchen.
     */
    $loaded_hooks = \dcms\Core::load_file('hooks', DCMS_CALL, true, false);
    if($loaded_hooks === true):
        \dcms\Core::track_file(DCMS_CALL.'/hooks.php', 'hooks');
    endif;
    
    /**
     * Die statische Datenbankklasse laden und die darin vorhandene
     * init() Methode aufrufen. Diese Datenbankklasse wird nur geladen, wenn
     * keine Tests ausgeführt werden.
     */
    if(!defined('PHP_UNIT')):
        
        /**
         * Den Hookpoint pre_database und die darin enthaltenen Funktionen
         * aufrufen.
         */
        \dcms\Hooks::call('pre_database');
        
        \dcms\Core::core_part('Database');
        \dcms\Database::init();
        
        /**
         * Den Hookpoint post_database aufrufen.
         */
        \dcms\Hooks::call('post_database');
        
    endif;
    
    /**
     * Die statische Inputklasse laden.
     */
    \dcms\Core::core_part('Input');
    
    /**
     * Die URL-Klasse laden.
     */
    \dcms\Loader::library('Url');
    
    /**
     * Die statische Outputklasse laden.
     */
    \dcms\Core::core_part('Output');
    
    /**
     * Den folgenden Code nur ausführen, wenn keine PHPUnit-Tests
     * ausgeführt werden.
     */
    if(!defined('PHP_UNIT')):
        
        /**
         * Die Standardwerte für den HTTP Status und den Content-type senden.
         * Diese werden nur gesendet, wenn keine PHPUnit-Tests ausgeführt werden.
         */
        \dcms\Output::clear();
        \dcms\Output::status();
        \dcms\Output::content_type();
        
        /**
         * Die Routerlibrary laden und eine Instanz davon erstellen.
         */
        \dcms\Loader::library('Router', false);
        
        /**
         * Den Basecontroller laden.
         */
        \dcms\Core::load_file('Base', 'framework/controller', true, true);
        \dcms\Core::track_file('framework/controller/Base.php', 'controller');
        
        /* @var $route \dcms\library\Router */
        $route = \dcms\library\Router::get();
        
        /**
         * Versuchen den Controller und das View zu instanzieieren.
         */
        if($route->route() === false):
            $route->show_404();
        endif;
        
    endif;