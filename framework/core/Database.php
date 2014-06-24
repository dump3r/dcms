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
        
        /**
         * Einen Query in der aktuellen Datenbankverbindung ausführen.
         * 
         * @param string $query
         * @return \mysqli_result
         */
        public static function query($query)
        {
            $result = self::$mysqli->query($query);
            self::$last_query = $query;
            
            if($result === false):
                $error = self::$mysqli->error;
                \dcms\Log::write("Could not execute database query! ($error)", null, 3);
                return $result;
            endif;
            
            $substr_query = substr($query, 0, 128);
            \dcms\Log::write("Executed database query ... $substr_query", null, 1);
            return $result;
        }
        
        /**
         * Prüfen ob eine Tabelle in der aktuellen Datenbank existiert.
         * 
         * @param string $name
         * @param boolean $prefix
         * @return boolean
         */
        public static function table_exists($name, $prefix = true)
        {
            $database_name = \dcms\Config::get('db_database', 'dcms_database');
            $database_string = self::escape($database_name);
            
            $table_string = self::table_name($name, $prefix);
            
            $query_string = "
                SELECT COUNT(*) 
                FROM `information_schema`.`tables` 
                WHERE 
                    `table_schema` = '$database_string' AND 
                    `table_name` = '$table_string'
            ";
            $query_result = self::query($query_string);
            
            if($query_result === false):
                \dcms\Log::write("Could not determine whether the table exists or not!", null, 3);
                return false;
            endif;
            
            $row = $query_result->fetch_row();
            if(isset($row[0]) === false):
                \dcms\Log::write('Can not fetch the result of the count query!', null, 3);
                return false;
            endif;
            
            if($row[0] > 1):
                \dcms\Log::write("More than one match for table $table_string", null, 2);
            endif;
            if($row[0] == 1):
                return true;
            endif;
            
            return false;
        }
        
        public static function select($table, $data = array(), $prefix = true)
        {
            $table_name = self::table_name($table, $prefix);
            $where_string = '';
            
            if(isset($data['where']) === true):
                $where_string = self::_where($data['data']);
            else:
                \dcms\Log::write('You did not specified an WHERE clause. All rows will be pulled!', null, 2);
            endif;
            
            $limit_string = '';
            if(isset($data['start'], $data['offset']) === true):
                $limit_string = self::_limit($data['start'], $data['offset']);
            else:
                \dcms\Log::write('You did not specified an limit for the SELECT query!', null, 2);
            endif;
            
            
        }
        
        /**
         * Den String für die WHERE-Anweisung erstellen.
         * 
         * @param array $array
         * @return string
         */
        private static function _where($array)
        {
            if(is_array($array) === false):
                \dcms\Log::write('No array supplied!', null, 3);
                return '';
            endif;
            
            $where_string = ' WHERE ';
            
            foreach($array as $row):
                
                if(is_array($row) === false):
                    \dcms\Log::write('You must supply an array as where clause!', null, 3);
                    return '';
                endif;
                
                $field = '`'.self::escape($row[0]).'` ';
                $value = " '".self::escape($row[2])."' ";
                
                $string = $field.$row[1].$value;
                
                if(isset($row[3]) === true):
                    $string .= $row[3];
                endif;
                
                $where_string .= $string;
                
            endforeach;
            
            return $where_string;
        }
        
        /**
         * Eine LIMIT-Anweisung erstellen.
         * 
         * @param numeric $start
         * @param numeric $offset
         * @return string
         */
        private static function _limit($start, $offset)
        {
            if(is_numeric($start) === false):
                \dcms\Log::write('The value for $start must be numeric!', null, 3);
                return '';
            endif;
            if(is_numeric($offset) === false):
                \dcms\Log::write('The value for $offset must be numeric!', null, 3);
                return '';
            endif;
            
            $return = ' LIMIT ';
            $return .= self::escape($start).', ';
            $return .= self::escape($offset).' ';
            return $return;
        }
        
    }
