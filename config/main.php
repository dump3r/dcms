<?php

    /**
     * Die Hauptkonfigurationsdatei des Systems.
     * Alle hier gesetzten Einstellungen sind sowohl für die
     * index.php als auch für die admin.php gültig.
     * Bitte hier keine neuen Werten ergänzen.
     */
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');
    
    /**
     * Die Environmentkonstante setzen. Diese wird genutzt,
     * um das Meldungslevel für Fehler festzulegen. Zudem wird eine
     * Konfigurationsdatei mit diesem Namen gesucht.
     */
    define('DCMS_ENVIRONMENT', 'development');
    
    /**
     * URL Einstellungen
     * ---------------------
     * Hier wird nur die Basisurl gesetzt. Alle anderen
     * URL-Einstellungen werden entweder in app.php bzw.
     * der admin.php gesetzt.
     */
    \dcms\Config::set('url_base', 'http://domain.tld/');
    
    /**
     * Cookieeinstellungen
     * -------------------
     */
    \dcms\Config::set('cookie_prefix', 'cookie_');
    \dcms\Config::set('cookie_path', '/');
    \dcms\Config::set('cookie_domain', '.domain.tld');
    \dcms\Config::set('cookie_secure', false);
    \dcms\Config::set('cookie_httponly', true);
    
    /**
     * Mailereinstellungen
     * -------------------
     * Diese Einstellungen werden von der Maillibrary genutzt und
     * gelten für beide die index.php und die admin.php.
     */
    \dcms\Config::set('mailer_sender_address', 'no-reply@domain.tld');
    \dcms\Config::set('mailer_sender_name', 'dCMS Mailcore');
    \dcms\Config::set('mailer_save', false);
    \dcms\Config::set('mailer_save_type', 'database');
    \dcms\Config::set('mailer_folder', 'mail');
    
    /**
     * SMTP Servereinstellungen
     * ------------------------
     * Diese Einstellungen werden von der Mailerklasse genutzt, um
     * eine Verbindung zu einem SMTP Server herzustellen. EMails werden dann
     * über diesen Server versendet anstatt die PHP mail() Funktion zu nutzen.
     */
    \dcms\Config::set('smtp_use', false);
    \dcms\Config::set('smtp_server', 'localhost');
    \dcms\Config::set('smtp_port', 25);
    \dcms\Config::set('smtp_auth', false);
    \dcms\Config::set('smtp_username', '');
    \dcms\Config::set('smtp_password', '');
    
    /**
     * Datenbankeinstellungen
     * ----------------------
     * Dieses System unterstützt im Moment nur MySQL durch die mysqli
     * Erweiterung von PHP.
     */
    \dcms\Config::set('db_server', 'localhost');
    \dcms\Config::set('db_port', 3306);
    \dcms\Config::set('db_username', '');
    \dcms\Config::set('db_password', '');
    \dcms\Config::set('db_database', 'dcms_database');
    \dcms\Config::set('db_prefix', 'dcms_');
    
    /**
     * Tabellennamen
     * -------------
     * Namen für Datenbanktabellen. Normalerweise muss man diese nicht verändern.
     */
    \dcms\Config::set('table_mails', 'mails');
    \dcms\Config::set('table_users', 'users');
    \dcms\Config::set('table_posts', 'posts');
    \dcms\Config::set('table_comments', 'comments');
    