<?php

class AppSolo {
  var $schema = array();
  var $argv   = array();
  var $args   = array();

  static $config = array();

  static function config($key, $value=null){
    if(func_num_args()==1) return self::$config[$key];
    self::$config[$key] = $value;
  }

  static function make($configs){
    foreach($configs as $key=>$value) self::$config[$key] = $value;
    return new AppSolo();
  }

  function __construct(){
    if(php_sapi_name() == "cli"){
      global $argv;
      $path_info = $argv[1];
    }else{
      $path_info = trim($_SERVER['PATH_INFO'],'/');
    }

    $this->path = $path_info;
    $this->argv = array_merge(array('.php'), strlen($path_info)?explode('/',$path_info):array());
  }

  function dispatch($schema=array()){
    $this->schema = $schema; 
    $request_path = '/'.$this->path;
    foreach($this->schema['route'] as $route){
      list($method, $pattern, $func) = $route;
      if(preg_match("#$pattern#", $request_path, $matches)){
        $user_argv = array();
        foreach($matches as $k=>$v){ if(is_int($k) && $k>0) $user_argv[] = $v; }
        $this->args = $matches; // TODO
        call_user_func_array($func, $user_argv);
        break;
      }
    }
    AppView::display();
  }
}



class AppView {
  static $vars   = array();
  static $view   = null;
  static $buffer = array();
  static $extend = array();

  static function assign($key, $value){
    self::$vars[$key] = $value;
  }

  static function view($view){
    self::$view = $view;
  }

  static function extend($view){
    foreach(self::$buffer as $k=>$v) unset(self::$buffer[$k]);
    self::load($view);
  }

  static function block($name=null){
     if(empty($name)) {
       throw new Exception('Empty block name found!');
     }

     if($name[0] == '/'){
       $data = ob_get_clean();
       #TODO: check name
       self::$buffer[substr($name,1)] = $data;
       ob_start();
     }else{
       $data = ob_get_clean();
       self::$buffer[] = $data;

       if(!isset(self::$buffer[$name])){
         self::$buffer[$name] = null;
       }
       ob_start();
     }
  }

  static function display(){
    $view = self::$view;
    foreach(self::$buffer as $k=>$v) unset(self::$buffer[$k]);
    self::load($view);
    echo implode('',self::$buffer);
  }

  static function load($view){
    foreach(self::$vars as $key=>$value){
      $$key = $value;
    }

    $view_dir = AppSolo::config('view.dir');
    if(!empty($view)){
      $include_path = set_include_path($view_dir);
      ob_start();
      include("$view_dir/$view");
      self::$buffer[] = ob_get_clean();
      set_include_path($include_path);
    }
  }
}
