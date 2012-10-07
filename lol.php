<?php
/**
* Project MVC by TheRox
*  Class Controller
**/
class Controller {

	public  $request;
	private $vars = array();
	public  $layout = 'default';
	private $rendered = false;

	function __construct($request= null) {
		if($request) {
			$this->request = $request;
		}
	}
	
	public function render($view){
		if($this->rendered) { return false; }
		extract($this->vars);
		if(strpos($view, '/') ===0) {
			$view = ROOT.DS.'view'.$view.'.php';
		} else {
			$view = ROOT.DS.'view'.DS.$this->request->controller.DS.$view.'.php';
		}
		ob_start();
		require($view);
		$content_for_layout = ob_get_clean();
		require ROOT.DS.'view'.DS.'layout'.DS.$this->layout.'.php';
		$this->rendered = true;
	}

	public function set($key, $value=null) {
		if(is_array($key)) {
			$this->vars += $key;
		} else {
			$this->vars[$key] = $value;
		}
	}

	/**
	*	Permet de charger un model
	**/
	public function loadModel($name) {
		$file = ROOT.DS.'model'.DS.$name.'.php';
		require_once($file);
		if(!isset($this->$name)) {
			$this->$name = new $name();
		}
	}

	/**
	* Permet de gerer les errors 404
	**/
	public function e404($message) {
		header("HTTP/1.0 404 Not Found");
		$this->set('message', $message);
		$this->render('/errors/404');
		die();
	}

	/**
	* Permet d'appeller un controller depuis une vue
	**/
	function request($controller,$action){
        $vars = array();
        $controller .= 'Controller';
        require_once ROOT.DS.'controller'.DS.$controller.'.php';
        $c = new $controller();
        return $c->$action();
    }
    /**
    * Permet de rediriger le site
    **/
    public function redirect ($url, $code) {
    	if($code == 301) {
    		header("HTTP/1.1 301 Moved Permanently");
    	}
    	header("Location: ".Router::url($url));
    	
    }
}
