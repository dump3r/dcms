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
     * Hier wird der Ttitel für das Administrationsinterface
     * und das Theme für das Interface festgelegt.
     */
    \dcms\Config::set('admin_title', 'administrator');
    \dcms\Config::set('admin_theme', 'default');
    
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
    \dcms\Config::set('route_default', array('home', 'index'));
    \dcms\Config::set('route_404', array('error', 'page_missing'));
