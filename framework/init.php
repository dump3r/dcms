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