<?php
class Mvc {
   public $module="";
   public $controller="";
   public $action="";
   public $params = array();
   public function toString(){
	echo '<pre>';
	echo 'Module: ' . $this->module . '<br/>';
	echo 'Controller: ' . $this->controller . '<br/>';
	echo 'Action: ' . $this->action . '<br/>';
	var_dump($this->params) . '<br/>';
	echo '</pre>';
   }
} 
