<?php

    /**
     * Konfigurationsdatei für Anfragen an die index.php
     * -------------------------------------------------
     * Diese Datei wird nur geladen, wenn eine Anfrage an
     * die index.php gestellt wird.
     */

    /**
     * Projekteinstellungen
     * --------------------
     * Hier wird unter anderem der Projekttitel festgelegt.
     * Man könnte auch hier einen Konigurationsparameter für eine
     * Theme-Logik setzen sollte man eine solche einbauen.
     */
    \dcms\Config::set('app_title', 'Projekttitel');
    
    /**
     * URL-Einstellungen
     * -----------------
     */
    \dcms\Config::set('url_pattern', 'a-zA-Z0-9./-');
    \dcms\Config::set('url_rewrite', false);
    
    /**
     * Routereinstellungen
     * -------------------
     * Hier wird eine Standardroute und eine Fehlerroute für 404 Fehler
     * definiert.
     */
    \dcms\Config::set('route_default', 'welcome/index');
    \dcms\Config::set('route_404', 'welcome/page_missing');
