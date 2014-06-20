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
     * Die Umgebungsabhängige Konfigurationsdatei laden.
     * Diese Datei muss nicht vorhanden sein.
     */
    $additional_config = \dcms\Core::load_file(DCMS_ENVIRONMENT, 'config', false, false);
    if($additional_config === true):
        
        $filepath = $config_directory.'/'.DCMS_ENVIRONMENT.'.php';
        \dcms\Core::track_file($filepath, 'config');
        \dcms\Log::write("Loaded additional config file $filepath", 'init', 1);
        
    endif;
    
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
     * Die statische Loaderklasse laden
     */
    \dcms\Core::core_part('Loader');
    
    /**
     * Die Singletonklasse laden
     */
    \dcms\Core::load_file('Singleton', 'framework/core', true, true);
    