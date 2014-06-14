<?php

    /**
     * Diese Datei verwaltet alle Aufrufe die in das admin-Verzeichnis
     * umgeleitet werden können. Der Name impliziert bereits, dass man
     * darin administrative Aktionen durchführen kann. Hier wird
     * deswegen die session_start() Funktion aufgerufen, damit die
     * $_SESSION-Superglobal genutzt werden kann.
     */
    session_start();
    
    define('DCMS_SECURE', true);
    define('DCMS_CALL', 'admin');
    
    require_once dirname(__FILE__).'/../framework/init.php';