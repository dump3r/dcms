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
    
        $filepath = $config_directory.'/'.$config_file.'php';
        \dcms\Core::track_file($filepath, 'config');
        
    endforeach;