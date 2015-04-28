<?php

class AppSolo {
  var $schema = array();
  var $argv   = array();
  var $args   = array();

  static $config   = array();
  static $registry = array();

  static function config($key, $value=null){
    if(func_num_args()==1) return self::$config[$key];
    self::$config[$key] = $value;
  }

  static function register($key, $value=null){
    if(func_num_args()==1) return self::$registry[$key];
    self::$registry[$key] = $value;
  }

  static function make($configs){
    foreach($configs as $key=>$value) self::$config[$key] = $value;
    return new AppSolo();
  }

  static function redirect($path){
    $site_base = AppRegistry::get('site.base');
    header("Location: $site_base/$path");
  }

  function __construct(){
    if(php_sapi_name() == "cli"){
      global $argv;
      $path_info = $argv[1];
    }else{
      $path_info = trim($_SERVER['PATH_INFO'],'/');
      AppRegistry::set('site.root', rtrim(dirname($_SERVER['SCRIPT_NAME']),'/')); 
      AppRegistry::set('site.base', $_SERVER['SCRIPT_NAME']); 
    }

    $this->path = $path_info;
    $this->argv = array_merge(array('.php'), strlen($path_info)?explode('/',$path_info):array());
  }

  private function match_method($method){
    $request_method = $_SERVER['REQUEST_METHOD'];
    return $method==="*" || $request_method===$method;
  }

  function prelude(){
    // prelude
    foreach($this->schema['prelude'] as $route){
      list($method, $pattern, $func) = $route;
      $pattern = preg_replace(array('/::/', '/:\w+/'), array('[^/]+', '([^/]+)'), $pattern);
      if($this->match_method($method) && preg_match("#$pattern#", $request_path, $matches)){
        $user_argv = array();
        foreach($matches as $k=>$v){ if(is_int($k) && $k>0) $user_argv[] = $v; }
        $this->args = $matches; // TODO
        $user_func = trim($func, '|');
        call_user_func_array($user_func, $user_argv);
      }
    }
  }

  function postlude($request_path){
    // postlude
    foreach($this->schema['postlude'] as $route){
      list($method, $pattern, $func) = $route;
      $pattern = preg_replace(array('/::/', '/:\w+/'), array('[^/]+', '([^/]+)'), $pattern);
      if($this->match_method($method) && preg_match("#$pattern#", $request_path, $matches)){
        $user_argv = array();
        foreach($matches as $k=>$v){ if(is_int($k) && $k>0) $user_argv[] = $v; }
        $this->args = $matches; // TODO
        $user_func = trim($func, '|');
        call_user_func_array($user_func, $user_argv);
      }
    }
  }

  function route($request_path){
    foreach($this->schema['route'] as $route){
      list($method, $pattern, $func) = $route;
      $pattern = preg_replace(array('/::/', '/:\w+/'), array('[^/]+', '([^/]+)'), $pattern);
      if($this->match_method($method) && preg_match("#$pattern#", $request_path, $matches)){
        $user_argv = array();
        foreach($matches as $k=>$v){ if(is_int($k) && $k>0) $user_argv[] = $v; }
        $this->args = $matches; // TODO
        $user_func = trim($func, '|');
        $ret = call_user_func_array($user_func, $user_argv);
        /*
         * If the return is omitted the value NULL will be returned.
         * If no parameter is supplied, NULL will be returned.
         */
        if($ret===true || $func[0]=='|') continue; // filter
        break;
      }
    }
  }

  function dispatch($schema=array()){
    $this->schema = $schema;
    $request_path   = '/'.$this->path;

    $this->prelude($request_path);

    $this->route($request_path);

    $this->postlude($request_path);

    // display
    AppView::display();
  }
}



class AppView {
  static $vars     = array();
  static $view     = null;
  static $buffer   = array();
  static $extended = 0;

  function register($key, $value=null){
    static $askfor = array();

    if(!is_null($value)){
      $askfor[$key] = 1;
    }
    return isset($askfor[$key])?$askfor[$key]:null;
  }

  static function assign($key, $value){
    self::$vars[$key] = $value;
  }

  static function view($view){
    self::$view = $view;
  }

  static function extend($view){
    foreach(self::$buffer as $k=>$v) unset(self::$buffer[$k]);
    self::load($view);
    self::$extended = 1;
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
       if(!self::$extended){
         self::$buffer[] = $data;
       }

       if(!isset(self::$buffer[$name])){
         self::$buffer[$name] = null;
       }
       ob_start();
     }
  }

  static function display(){
    if(is_null(self::$view)) return;

    foreach(self::$buffer as $k=>$v) unset(self::$buffer[$k]);
    self::$extended = 0;

    self::load(self::$view);
    echo implode('',self::$buffer);
    // flush()
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


class AppRegistry {
  static $lut = array();

  static function set($key, $value){
    self::$lut[$key] = $value;
  }

  static function get($key, $value=null){
    return isset(self::$lut[$key])?self::$lut[$key]:$value;
  }
}
