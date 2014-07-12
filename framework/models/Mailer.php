<?php

    namespace dcms\model;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Description of Mailer
     *
     * @author dump3r
     * @version 1.0.0
     * @since 1.0.5
     * @see http://blaargh.de/dcms/docs/model/Mailer
     */
    class Mailer extends \dcms\Singleton {
        
        protected static $instance;
        
        protected $table;
        protected $prefix = true;
        
        public function __construct()
        {
            $this->table = \dcms\Config::get('table_mails', 'mails');
            
            if(DCMS_ENVIRONMENT == 'development'):
                
                $result = \dcms\Database::table_exists($this->table, $this->prefix);
                if($result === false):
                    \dcms\Log::write("Table {$this->table} does not exists!", null, 3);
                    kill('Table missing!');
                endif;
                
            endif;
        }
        
        /**
         * Eine Email in der Datenbank speichern.
         * 
         * @param string $address_string
         * @param string $subject
         * @param string $message
         * @return boolean|int
         */
        public function save_email($address_string, $subject, $message)
        {
            $data = array(
                'address' => $address_string,
                'subject' => $subject,
                'message' => $message,
                'module' => DCMS_CALL,
                'timestamp' => time()
            );
            $query_result = \dcms\Database::insert($this->table, $data, $this->prefix);
            
            if($query_result === false):
                \dcms\Log::write('Could not save email in database!', null, 3);
                return false;
            endif;
            
            $insert_id = \dcms\Database::$insert_id;
            \dcms\Log::write("Saved email in database with id $insert_id", null, 1);
            return $insert_id;
        }
        
        /**
         * Eine Email anhand der ID aus der Datenbank auslesen.
         * 
         * @param int $id
         * @return boolean|array
         */
        public function get_email($id)
        {
            $data = array(
                'select' => array('*'),
                'where' => array(
                    array('id', '=', $id)
                )
            );
            $query_result = \dcms\Database::select($this->table, $data, $this->prefix);
            
            if($query_result === false):
                \dcms\Log::write("Could not get email with id $id from database!", null, 3);
                return false;
            endif;
            if($query_result->num_rows !== 1):
                \dcms\Log::write("Could not get email with id $id from database! Found either 0 or more than 1 row!", null, 3);
                return false;
            endif;
            
            $array = $query_result->fetch_assoc();
            $query_result->free();
            return $array();
        }
        
    }
