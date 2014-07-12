<?php
    
    if(!defined('DCMS_SECURE'))
        exit('Veboten!');
    
    if(!function_exists('pre')) {
        
        /**
         * Eine Variable in einem pre-Container ausgeben.
         * 
         * @param mixed $variable
         */
        function pre($variable)
        {
            \dcms\Core::pre($variable);
        }
        
    }
    
    if(!function_exists('kill')) {
        
        /**
         * Das System stoppen und eine Fehlermeldung ausgeben.
         * Wenn DCMS_ENVIRONMENT auf development steht, wird zudem
         * der aktuelle Log ausgegeben.
         * 
         * @param string $reason
         */
        function kill($reason)
        {
            \dcms\Core::kill($reason);
        }
        
    }