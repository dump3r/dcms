<?php

    /**
     * Systemweite Konstanten
     * ----------------------
     * Diese über define() definierten Konstanten können
     * überall ohne Namespace aufgerufen werden.
     */
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Die Systemversion von dCMS.
     */
    define('DCMS_VERSION', '1.0.3');
    
    /**
     * Das Hauptverzeichnis von dCMS.
     * Dieses Verzeichnis entspricht dem Speicherort dieser Datei.
     */
    define('DCMS_ROOT', dirname(__FILE__));
    
    /**
     * Der Verzeichnispfad zum framework-Verzeichnis.
     */
    define('DCMS_FRAMEWORK_DIR', DCMS_ROOT.'/framework');
    
    /**
     * Der Verzeichnispfad zum app-Verzeichnis.
     */
    define('DCMS_APP_DIR', DCMS_ROOT.'/app');
    
    /**
     * Der Verzeichnispfad zum admin-Verzeichnis.
     */
    define('DCMS_ADMIN_DIR', DCMS_ROOT.'/admin');