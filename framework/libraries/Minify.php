<?php

    namespace dcms\library;
    
    if(!defined('DCMS_SECURE'))
        exit('Verboten!');

    /**
     * Description of Minify
     *
     * @author dump3r
     * @version 1.0.0
     * @since 1.3.0
     * @see http://blaargh.de/
     */
    class Minify extends \dcms\Singleton {
        
        protected static $instance;
        
        protected $css_minifier = '\MatthiasMullie\Minify\CSS';
        protected $css_instance;
        
        protected $jshrink = '\JShrink\Minifier';
        protected $js_files = array();
        
        public function __construct()
        {
            /**
             * Prüfen ob die Klasse CSS im Namespace verfügbar ist.
             */
            if(class_exists($this->css_minifier) === false):
                \dcms\Log::write("Could not find minify class {$this->css_minifier}!", null, 3);
                kill('Extension missing!');
            endif;

            /**
             * Eine Instanz der Klasse erzeugen.
             */
            $this->css_instance = new $this->css_minifier;
            
            /**
             * Prüfen ob die Klasse JShrink\Minifier verfügbar ist.
             */
            if(class_exists($this->jshrink) === false):
                \dcms\Log::write('JS Minifier class missing!', null, 3);
                kill('Extension missing!');
            endif;
        }
        
        /**
         * Eine JavaScript Datei für den minify-Prozess eintragen.
         * 
         * @param string $filepath
         * @return boolean
         */
        public function add_js_file($filepath)
        {
            $filepath = DCMS_ROOT.'/'.$filepath;
            if(file_exists($filepath) === false):
                \dcms\Log::write("Input file $filepath does not exists!", null, 3);
                return false;
            endif;
            if(in_array($filepath, $this->js_files) === true):
                \dcms\Log::write("Input file $filepath already marked!", null, 2);
                return false;
            endif;
            
            $this->js_files[] = $filepath;
            return true;
        }
        
        /**
         * Alle JavaScript Dateien verkleinern und in einer Datei zusammenfassen.
         * 
         * @param string $output
         * @param array $options
         * @return boolean
         */
        public function minify_js($output, $options = array())
        {
            $output = DCMS_ROOT.'/'.$output;
            $directory = dirname($output);
            if(is_writable($directory) === false):
                \dcms\Log::write("Directory of output file is not writeable!", null, 3);
                return false;
            endif;
            
            /**
             * Den Inhalt aller JS-Dateien in einen String einlesen.
             */
            $js_content = '';
            foreach($this->js_files as $file):
                if(is_readable($file) === true):
                    
                    $js_content .= file_get_contents($file)."\n";
                    
                endif;
            endforeach;
            
            /**
             * Den JavaScript Code compilen.
             */
            try {
                
                $return = \JShrink\Minifier::minify($js_content, $options);
                if($return === false):
                    \dcms\Log::write("Could not compile JS files!", null, 2);
                    return false;
                endif;
                
                /**
                 * Write the result to a file.
                 */
                $file = new \dcms\library\File($output);
                
                $file->open('w');
                $file->write($return);
                $file->close();
                
                \dcms\Log::write("Wrote compiled JS code in $output!", null, 1);
                return true;
                
            } catch (\Exception $e) {
                
                $message = $e->getMessage();
                \dcms\Log::write("Could not compile JS files! ($message)", null, 3);
                return false;
                
            }
        }
        
        /**
         * Eine CSS Datei für den minify-Prozess eintragen.
         * 
         * @param string $filepath
         * @return boolean
         */
        public function add_css_file($filepath)
        {
            $filepath = DCMS_ROOT.'/'.$filepath;
            if(file_exists($filepath) === false):
                \dcms\Log::write("Input file $filepath does not exists!", null, 3);
                return false;
            endif;
            
            $this->css_instance->add($filepath);
            return true;
        }
        
        /**
         * Alle CSS Dateien verkleinern und in einer Datei zusammenfassen.
         * 
         * @param string $output
         * @return boolean
         */
        public function minify_css($output)
        {
            $output = DCMS_ROOT.'/'.$output;
            $directory = dirname($output);
            if(is_writeable($directory) === false):
                \dcms\Log::write("Directory of output file $output is not writeable!", null, 3);
                return false;
            endif;
            
            $return = $this->css_instance->minify($output);
            \dcms\Log::write("Successfully minified CSS!", null, 1);
            return $return;
        }
        
    }
