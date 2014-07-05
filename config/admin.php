<?php

    /**
     * Konfigurationsdatei für Anfragen an die admin.php
     * -------------------------------------------------
     * Diese Datei wird nur geladen, wenn eine Anfrage an
     * die admin.php gestellt wird.
     */

    /**
     * Admineinstellungen
     * --------------------
     * Hier wird der Ttitel für das Administrationsinterface.
     */
    \dcms\Config::set('admin_title', 'administrator');
    
    /**
     * Themeeinstellungen
     * ------------------
     */
    \dcms\Config::set('theme_name', 'default');
    \dcms\Config::set('theme_extension', '.tpl');
    
    /**
     * URL-Einstellungen
     * -----------------
     */
    \dcms\Config::set('url_index', 'admin.php');
    \dcms\Config::set('url_pattern', 'a-zA-Z0-9./-_');
    
    /**
     * Routereinstellungen
     * -------------------
     * Hier wird eine Standardroute und eine Fehlerroute für 404 Fehler
     * definiert.
     */
    \dcms\Config::set('route_default', 'home/index');
    \dcms\Config::set('route_404', 'error/page_missing');
