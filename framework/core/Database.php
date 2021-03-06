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
        protected static $mysqli;
        public static $last_query;
        public static $insert_id;
        
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
        
        public static function count($table, $where, $prefix = true)
        {
            $table_name = self::table_name($table, $prefix);
            
            if(is_array($where) === false):
                \dcms\Log::write('No array supplied!', null, 3);
                return false;
            endif;
            $where_string = self::_where($where);
            
            $query_string = "
                SELECT COUNT(*)
                FROM `$table_name`
                $where_string
            ";
            $query_result = self::query($query_string);
            
            if($query_result === false):
                \dcms\Log::write("Could not count rows in $table_name!", null, 3);
                return false;
            endif;
            
            $row = $query_result->fetch_row();
            $query_result->free();
            
            if(isset($row[0]) === false):
                \dcms\Log::write("Could not get row of counted rows!", null, 3);
                return false;
            endif;
            
            return $row[0];
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
                $query_result->free();
                return false;
            endif;
            
            if($row[0] > 1):
                \dcms\Log::write("More than one match for table $table_string", null, 2);
            endif;
            if($row[0] == 1):
                $query_result->free();
                return true;
            endif;
            
            $query_result->free();
            return false;
        }
        
        /**
         * Einen SELECT query aufbauen und ausführen. Diese Methode kann
         * entweder FALSE zurückgeben oder ein mysqli_result-Objekt.
         * 
         * @param string $table
         * @param array $data
         * @param boolean $prefix
         * @return boolean|mysqli_result
         */
        public static function select($table, $data = array(), $prefix = true)
        {
            $table_name = self::table_name($table, $prefix);
            $where_string = '';
            
            $select_string = '*';
            if(isset($data['select']) === true):
                if(is_array($data['select']) === false):
                    \dcms\Log::write('You must supply an array for the SELECT caluse!', null, 2);
                    return false;
                endif;
                $select_array = array();
                foreach($data['select'] as $field):
                    $select_array[] = '`'.self::escape($field).'`';
                endforeach;
                $select_string = implode(', ', $select_array);
            endif;
            
            if(isset($data['where']) === true):
                $where_string = self::_where($data['where']);
            else:
                \dcms\Log::write('You did not specified an WHERE clause. All rows will be pulled!', null, 2);
            endif;
            
            $order_string = '';
            if(isset($data['orderby'], $data['ordertype'])):
                $order_string = 'ORDER BY `'.self::escape($data['orderby']).'` '.$data['ordertype'];
            endif;
            
            $limit_string = '';
            if(isset($data['start'], $data['offset']) === true):
                $limit_string = self::_limit($data['start'], $data['offset']);
            else:
                \dcms\Log::write('You did not specified an limit for the SELECT query!', null, 2);
            endif;
            
            $query_string = "
                SELECT $select_string
                FROM `$table_name`
                $where_string
                $order_string
                $limit_string;
            ";
            $query_result = self::query($query_string);
            
            if($query_result === false):
                \dcms\Log::write("Could not complete SELECT query on $table_name!", null, 3);
                return false;
            endif;
            
            return $query_result;
        }
        
        /**
         * Einen INSERT Query ausführen.
         * 
         * @param string $table
         * @param array $data
         * @param boolean $prefix
         * @return boolean
         */
        public static function insert($table, $data, $prefix = true)
        {
            $table_name = self::table_name($table, $prefix);
            
            if(is_array($data) === false):
                \dcms\Log::write('You must supply an array for $data.', null, 3);
                return false;
            endif;
            
            $fields_array = array();
            $values_array = array();
            
            foreach($data as $field => $value):
                
                $fields_array[] = '`'.self::escape($field).'`';
                $values_array[] = "'".self::escape($value)."'";
                
            endforeach;
            
            $field_string = implode(', ', $fields_array);
            $value_string = implode(', ', $values_array);
            
            $query_string = "
                INSERT INTO `$table_name`
                ($field_string)
                VALUES ($value_string)
            ";
            $query_result = self::query($query_string);
            
            if($query_result === false):
                \dcms\Log::write("Could not insert data into $table_name", null, 3);
                return false;
            endif;
            
            self::$insert_id = self::$mysqli->insert_id;
            return true;
        }
        
        /**
         * Einen UPDATE Query ausführen.
         * 
         * @param string $table
         * @param array $data
         * @param boolean $prefix
         * @return boolean
         */
        public static function update($table, $data, $prefix = true)
        {
            $table_name = self::table_name($table, $prefix);
            
            if(is_array($data) === false):
                \dcms\Log::write('You must supply an array for $data', null, 3);
                return false;
            endif;
            
            if(isset($data['set']) === false):
                \dcms\Log::write('You did not specified what should be updated!', null, 3);
                return false;
            endif;
            
            $set_array = array();
            foreach($data['set'] as $field => $value):
                $set_array[] = "`".self::escape($field)."` = '".self::escape($value)."'";
            endforeach;
            $set_string = implode(', ', $set_array);
            
            $where_string = '';
            if(isset($data['where']) === true):
                $where_string = self::_where($data['where']);
            else:
                \dcms\Log::write('No WHERE clause found. All fields will be updated!', null, 2);
            endif;
            
            $query_string = "
                UPDATE `$table_name`
                SET $set_string
                $where_string
            ";
            $query_result = self::query($query_string);
            
            if($query_result === false):
                \dcms\Log::write("Could not update data in $table_name", null, 3);
                return false;
            endif;
            
            return true;
        }
        
        /**
         * Einen DELETE Query aufbauen und ausführen.
         * 
         * @param string $table
         * @param array $where
         * @param boolean $prefix
         * @return boolean
         */
        public static function delete($table, $where, $prefix = true)
        {
            $table_name = self::table_name($table, $prefix);
            
            if(is_array($where) === false):
                \dcms\Log::write('No array supplied for $where', null, 3);
                return false;
            endif;
            $where_string = self::_where($where);
            
            if(empty($where) === true):
                \dcms\Log::write('No WHERE clause defined! All rows will be deleted!', null, 2);
            endif;
            
            $query_string = "
                DELETE FROM `$table_name`
                $where_string
            ";
            $query_result = self::query($query_string);
            
            if($query_result === false):
                \dcms\Log::write("Could not delete rows in $table_name", null, 3);
                return false;
            endif;
            
            return false;
        }
        
        /**
         * Einen TRUNCATE Query in einer Tabelle ausführen.
         * Es werden alle Daten in dieser Tabelle gelöscht!
         * 
         * @param string $table
         * @param boolean $prefix
         * @return boolean
         */
        public static function truncate($table, $prefix = true)
        {   
            $table_name = self::table_name($table, $prefix);
            \dcms\Log::write("This will delete all data in table $table_name!", null, 2);
            
            $query_string = "
                TRUNCATE `$table_name`
            ";
            $query_result = self::query($query_string);
            
            if($query_result === false):
                \dcms\Log::write("Could not truncate table $table_name", null, 3);
                return false;
            endif;
            
            return true;
        }
        
        /**
         * Den String für die WHERE-Anweisung erstellen.
         * 
         * @param array $array
         * @return string
         */
        protected static function _where($array)
        {
            if(is_array($array) === false):
                \dcms\Log::write('No array supplied!', null, 3);
                return '';
            endif;
            
            if(empty($array) === true)
                return '';
            
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
        protected static function _limit($start, $offset)
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
