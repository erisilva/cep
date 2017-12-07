<?php

class Autoloader
{
    public $directories = array();
    
    public function register(){
        
        // recebe uma array, contendo um METODO (a função), e qual classe
        // pertence esse método
        spl_autoload_register(array($this, 'loader'));
    }
    
    public function loader($className){
        // interessante essa constante do php DIRECTORY_SEPARATOR
        // isso seria para tipo se houvessem subpastas de classes
        // no caso hoje se usa o tal de namespcaces
        // que você está tentando entender do outro curso
        
        // observe que o nome da classe precisa ser igual ao nome do arquivo para
        //funcionar corretamente
        $class = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';        
        
        If (!empty($this->directories)){
            foreach ($this->directories as $directory) {
                $classPath = rtrim($directory, '/') . DIRECTORY_SEPARATOR . $class;
                if (file_exists($classPath)){
                    return include_once $classPath;                    
                }
            }
        }
        
        
        // posso passar o nome de somente um arquivo também
        // não entendoi aqui, o cara pulou a explicação kkkk
        if (file_exists($class)){
            return include_once $class;    
        }
        
        $classPath = stream_resolve_include_path($class);
        if ($classPath !== FALSE){
            return include_once $classPath;
        }
    }
}

