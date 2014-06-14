<?php

    /**
     * Die Index-Datei verwaltet alle Aufrufe die in das
     * app-Verzeichnis umgeleitet werden können.
     */
    define('DCMS_SECURE', true);
    define('DCMS_CALL', 'app');
    
    require_once dirname(__FILE__).'/../framework/init.php';