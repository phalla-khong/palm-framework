<?php

class Route {
    private static $_routes = [];

    public static function get($uri, $call, $route_name=''){
        self::$_routes['get:'.$uri] = ['method' => 'GET', 'name' => $route_name, 'call' => $call];
    }

    public static function post($uri, $call, $route_name=''){
        self::$_routes['post:'.$uri] = ['method' => 'POST', 'name' => $route_name, 'call' => $call];
    }

    public static function delete($uri, $call, $route_name=''){
        self::$_routes['delete:'.$uri] = ['method' => 'DELETE', 'name' => $route_name, 'call' => $call];
    }

    public static function accept($methods, $uri, $call, $route_name=''){
        $arr_method = explode(',', $methods);
        
        foreach ($arr_method as $value) {
            $method = strtolower(trim($value));

            if(!in_array($method, ['get', 'post', 'delete'])){
                exit('Request method ['.strtoupper($method).'] invalid for this route ['.$uri.']');
            }else{
                self::$_routes[$method.':'.$uri] = ['method' => strtoupper($method), 'name' => $route_name, 'call' => $call];
            }
        }
    }

    public static function navigate(){
        $request_uri = explode('?', $_SERVER['REQUEST_URI']);
        $request_uri = $request_uri[0]; 
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if(isset(self::$_routes[$method.':'.$request_uri])){
            $route_info = self::$_routes[$method.':'.$request_uri];

            if( strpos(strtolower($route_info['method']), strtolower($_SERVER['REQUEST_METHOD'])) === false ){
                exit('This route not accept method ['.$_SERVER['REQUEST_METHOD'].']');
            }

            if(is_callable($route_info['call'])){
                call_user_func($route_info['call']);
            }else{                
                $array = explode('@', $route_info['call']);
                $array_class = preg_split('/(?=[A-Z])/', $array[0]);
                $array_class = array_filter($array_class);

                $class_file = strtolower(implode('_', $array_class)).'.php';

                if(!file_exists(dirname(__FILE__). '/../controllers/' . $class_file)){
                    exit('Path file of class ['.$array[0].'] not found');
                }

                if( !class_exists($array[0]) ){
                    exit('Class ['.$array[0].'] not found');
                }

                if( !method_exists($array[0], $array[1]) ){
                    exit('Method ['.$array[0].'] not found');
                }

                $obj = new $array[0]();
                $obj->{$array[1]}();
            }
        }else{
            exit('Route ['.$request_uri.'] not found');
        }
    }

    public static function info(){
        echo '<pre><span style="width: 200px;display: inline-block;">Route</span><span style="width: 200px;display: inline-block;">Method</span><span style="width: 200px;display: inline-block;">Name</span><span style="width: 200px;display: inline-block;">Invoke</span></pre>';

        foreach (self::$_routes as $uri => $value) {
            $call = '';
            if(is_callable($value['call'], false, $callable_name)){
                $call = $callable_name;
            }else{
                $call = $value['call'];
            }

            $routes = explode(':', $uri);

            echo '<pre><span style="width: 200px;display: inline-block;">'.$routes[1].'</span><span style="width: 200px;display: inline-block;">'.$value['method'].'</span><span style="width: 200px;display: inline-block;">'.$value['name'].'</span><span style="width: 200px;display: inline-block;">'.$call.'</span></pre>';
        }
    }
}