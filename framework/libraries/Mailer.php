<?php

    namespace dcms\library;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');
    
    /**
     * Prüfen ob PHPMailer installiert wurde.
     */
    if(class_exists('\PHPMailer') === false):
        \dcms\Log::write('Could not find third-party application PHPMailer! Please install it.', null, 3);
        kill('Extension missing');
    endif;

    /**
     * Description of Mailer
     *
     * @author dump3r
     * @version 1.0.0
     * @since 1.0.5
     * @see http://blaargh.de/dcms/docs/library/Mailer
     */
    class Mailer extends \dcms\Singleton {
        
        protected static $instance;
        
        protected $phpmailer;
        protected $path;
        
        protected $addresses = array();
        protected $subject;
        protected $body;
        
        public function __construct() 
        {
            /**
             * Den Speicherpfad aus der Konfiguration laden
             */
            $this->path = \dcms\Config::get('mailer_folder', 'share/mails');
            
            /**
             * Eine Instanz von PHPMailer erstellen.
             */
            $this->phpmailer = new \PHPMailer();
            $this->phpmailer->isHTML();
            $this->phpmailer->WordWrap = 85;
            
            /**
             * Die Konfigurationswerte in PHPMailer übergeben.
             */
            $this->phpmailer->setFrom(
                \dcms\Config::get('mailer_sender_address', 'no-reply@domain.tld'),
                \dcms\Config::get('mailer_sender_name', 'dCMS Mailcore')
            );
            
            /**
             * Soll SMTP verwendet werden.
             */
            $smtp_use = \dcms\Config::get('smtp_use', false);
            $smtp_auth = \dcms\Config::get('smtp_auth', false);
            
            if($smtp_use === true):
                
                $this->phpmailer->isSMTP();
                $this->phpmailer->Host = \dcms\Config::get('smtp_server', 'localhost');
                $this->phpmailer->Port = \dcms\Config::get('smtp_port', 25);
                
                if($smtp_auth === true):
                    
                    $this->phpmailer->SMTPAuth = true;
                    $this->phpmailer->Username = \dcms\Config::get('smtp_username', '');
                    $this->phpmailer->Password = \dcms\Config::get('smtp_password', '');
                    
                endif;
                
            endif;
        }
        
        /**
         * Eine Email Adresse als Empfänger hinzufügen.
         * 
         * @param string $email
         * @param string $name
         * @return boolean
         */
        public function add_address($email, $name = '')
        {
            $result = $this->phpmailer->addAddress($email, $name);
            if($result === false)
                return false;
            
            $string = $email;
            if(empty($name) === false)
                $string .= '('.$name.')';
            
            $this->addresses[] = $string;
            
            return true;
        }
        
        /**
         * Ein Betreff für die Email setzen.
         * 
         * @param string $string
         */
        public function set_subject($string)
        {
            $this->phpmailer->Subject = $string;
            $this->subject = $string;
        }
        
        /**
         * Den Text der Email festlegen
         * 
         * @param string $message
         * @param string $alt_message
         */
        public function set_body($message, $alt_message = '')
        {
            $this->phpmailer->Body = $message;
            $this->phpmailer->AltBody = $alt_message;
            $this->body = $message;
        }
        
        /**
         * Die Email versenden. Ist $fake_send auf true wird keine
         * Email versendet. Zudem kann die Email gespeichert werden.
         * 
         * @param boolean $fake_send
         * @return boolean
         */
        public function send($fake_send = false)
        {
            $save_email = \dcms\Config::get('mailer_save', false);
            if($save_email === true)
                $this->_save();
            
            $this->addresses = array();
            $this->body = '';
            $this->subject = '';
            
            if($fake_send === true)
                return true;
            
            return $this->phpmailer->send();
        }
        
        /**
         * Eine Email speichern
         * 
         * @return boolean
         */
        protected function _save()
        {
            $address_string = implode(', ', $this->addresses);
            $email_body = $this->body;
            $email_subject = $this->subject;
            
            $save_type = \dcms\Config::get('mailer_save_type', 'database');
            switch($save_type):
                
                case 'database':
                    return $this->_save_database($address_string, $email_subject, $email_body);
                    exit;
                
                case 'file':
                    return $this->_save_file($address_string, $email_subject, $email_body);
                    break;
                
                default:
                    \dcms\Log::write('Could not save email! Unknown save type.', null, 3);
                    return false;
                    break;
                
            endswitch;
        }
        
        /**
         * Eine Email in der Datenbank speichern.
         * 
         * @param string $emails
         * @param string $subject
         * @param string $message
         * @return boolean
         */
        protected function _save_database($emails, $subject, $message)
        {
            \dcms\Loader::model('Mailer');
            
            /* @var $mailer_model \dcms\model\Mailer */
            $mailer_model = \dcms\model\Mailer::get();
            
            /**
             * Die Email speichern
             */
            $result = $mailer_model->save_email($emails, $subject, $message);
            if($result !== false)
                return true;
            
            \dcms\Log::write('Could not save email in database!', null, 3);
            return false;
        }
        
        /**
         * Eine Email in einer Datei speichern.
         * 
         * @param string $emails
         * @param string $subject
         * @param string $message
         * @return boolean
         */
        protected function _save_file($emails, $subject, $message)
        {
            $timestamp = time();
            $filename = 'email_'.$timestamp.'.json';
            
            $file = new \dcms\library\File($this->path.'/'.$filename, true);
            if($file->is_writeable() === false):
                \dcms\Log::write("Can not save email in file $filename. Filepath not writeable!", null, 3);
                return false;
            endif;
            
            /**
             * Den Inhalt aufbauen.
             */
            $content = $this->_file_content($emails, $subject, $message, $timestamp);
            
            /**
             * Datei öffnen und schreiben
             */
            $file->open('w');
            $file->write($content);
            $file->close();
            
            return true;
        }
        
        /**
         * Ein JSON-Objekt erstellen.
         * 
         * @param string $emails
         * @param string $subject
         * @param string $message
         * @param int $timestamp
         * @return string
         */
        protected function _file_content($emails, $subject, $message, $timestamp)
        {
            $array = array(
                'timestamp' => $timestamp,
                'date' => date('d.m.Y \a\t H:i:s', $timestamp),
                'receiver' => $emails,
                'subject' => $subject,
                'message' => $message
            );
            return json_encode($array);
        }
        
    }
