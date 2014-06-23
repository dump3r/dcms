<?php

    namespace dcms;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Diese Klasse stellt eine MySQL Datenbankverbindung her und
     * kann genutzt werden, um Querys in der Datenbank auszuführen.
     *
     * @author dump3r
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/core/Database
     */
    class Database {
        
        /* @var $mysqli \mysqli */
        private static $mysqli;
        public static $last_query;
        
        /**
         * Die statische Datenbankklasse initialisieren.
         * 
         * @return void
         */
        public static function init()
        {
            if(class_exists('\mysqli') === false):
                \dcms\Log::write("Could not find mysqli PHP extension! Please install it.", null, 3);
                \dcms\Core::kill("Database error!");
            endif;
            
            $server = \dcms\Config::get('db_server', 'localhost');
            $port = \dcms\Config::get('db_port', 3306);
            $username = \dcms\Config::get('db_username', '');
            $password = \dcms\Config::get('db_password', '');
            $database = \dcms\Config::get('db_database', 'dcms_database');
            
            self::$mysqli = new \mysqli($server, $username, $password, $database, $port);
            
            if(self::$mysqli->connect_errno != 0):
                $connect_error = self::$mysqli->connect_error;
                \dcms\Log::write("Could not connect to database! ($connect_error)", null, 3);
                \dcms\Core::kill("Could not connect to database!");
            endif;
            
            \dcms\Log::write("Connected to database $database on server $server:$port with user $username", null, 1);
            self::$last_query = 'none';
        }
        
        /**
         * Einen String durch die mysqli::escape_string-Methode maskieren.
         * 
         * @param string $string
         * @return string
         */
        public static function escape($string)
        {
            $escape = self::$mysqli->escape_string($string);
            return $escape;
        }
        
        /**
         * Den Tabellennamen maskieren und das Tabellenprefix voranstellen.
         * 
         * @param string $table
         * @param boolean $prefix
         * @return string
         */
        public static function table_name($table, $prefix = true)
        {
            $table_name = $table;
            if($prefix === true):
                $prefix_string = \dcms\Config::get('db_prefix', '');
                $table_name = $prefix_string.$table_name;
            endif;
            
            $return = self::escape($table_name);
            return $return;
        }
        
        public static function query($query)
        {
            $result = self::$mysqli->query($query);
            self::$last_query = $query;
            
            if($result === false):
                $error = self::$mysqli->error;
                \dcms\Log::write("Could not execute database query! ($error)", null, 3);
                return false;
            endif;
            
            \dcms\Log::write("Executed database query ... $substr_query", null, 1);
        }
        
    }
