<?php
spl_autoload_register(function ($class) {    
    if(strpos(strtolower($class), 'controller') !== false){
        $array_class = preg_split('/(?=[A-Z])/', $class);
        $array_class = array_filter($array_class);
        $class_file = strtolower(implode('_', $array_class)).'.php';
        $class_file = dirname(__FILE__). '/../controllers/' . $class_file;

        if(file_exists($class_file) && is_readable($class_file)){
            include_once($class_file);
        }
    }
    elseif(strtolower($class) == 'route') {
        $class_file = dirname(__FILE__) . '/route.php';

        if(file_exists($class_file) && is_readable($class_file)){
            require_once($class_file);
        }
    }
});