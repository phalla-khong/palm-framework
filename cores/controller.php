<?php

class Controller {
   
    public function view($view, $params = [], $layout = 'app'){
        $layout_file = dirname(__FILE__) . '/../views/layouts/'. $layout .'.php';
        $view_file = dirname(__FILE__) . '/../views/'. $view .'.php';
        
        if(count($params)>0){
            extract($params);
        }

        include_once($view_file);
        include_once($layout_file);
    }

}