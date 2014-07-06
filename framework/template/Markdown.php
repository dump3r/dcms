<?php

    namespace dcms\template;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');
    
    /**
     * Prüfen ob Parsedown installiert wurde
     */
    if(class_exists('\Parsedown') === false):
        \dcms\Log::write('Could not find class Parsedown! Please use composer install to install Parsedown', 'init', 3);
        \dcms\Core::kill('The Parsedown extension is required!');
    endif;

    /**
     * Description of Markdown
     *
     * @author erusev
     * @version 1.0.0
     * @see http://blaargh.de/dcms/docs/template/Markdown
     */
    class Markdown extends \Parsedown {}
