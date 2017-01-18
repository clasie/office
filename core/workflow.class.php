<?php 
/**
 * key point of all the MVC process
 */
class Workflow{
   //instances
   public $configInstance = null;//config
   public $mvcInstance = null;//mvc
   public $modelInstance = null;//model
   //urls
   public $href_public = null;
   public $db_key_ini = null;

   public function __construct(){
      $this->config(); 
      $this->setConstants();
      $this->setModel();
      $this->buildMvcInstance();
   }
   //application ini
   public function config(){
      $this->configInstance = new Config();
      $this->configInstance->applicationIni = parse_ini_file("../config/application.ini",true);
   }
   //make db connection
   public function setModel(){
       //die(__FILE__.__LINE__);
	$this->db_key_ini = $_SESSION[Constant::DB_ENV_KEY];
                //(Constant::ENV_LOCAL == getenv('ENV'))? 
	   //Constant::DB_KEY_ENV_LOCAL: Constant::DB_KEY_ENV_REMOTE;
   }
   //build the aggregate mvc
   public function buildMvcInstance(){
	/*********************************************************************
	 * extract the params
	 ********************************************************************/

	//directory containing the called script
	$uri = rtrim( dirname($_SERVER["SCRIPT_NAME"]), '/' );
	//echo "<br/>directory containing the called script: " . $uri . "<br/>";
	//var_dump($_SERVER["SCRIPT_NAME"]);

	//build href for public stuffs
	$this->href_public = SERVER_NAME . $uri;
	//echo "<br/>href:  " . $href_public. "<br/>";

	//all url  data (params) following the containing directory
	$uri = '/' . trim( str_replace( $uri, '', $_SERVER['REQUEST_URI'] ), '/' );
	//echo "<br/>all url  data following the containing directory: " . $uri. "<br/>";
	//var_dump($_SERVER["REQUEST_URI"]);

	//translate special url chars in orde to be usable by php script
	$uri = urldecode( $uri );
	//echo "<br/>translate special url chars in orde to be usable by php script: " . $uri. "<br/>";

	//echo "URI: " . $uri . "<br/>";
	//echo "<pre>";
	$tmp = preg_split("|/|",$uri);
	//var_dump($tmp);
	$module     = (isset($tmp[1])    && strlen($tmp[1])>0)?$tmp[1]:"_NO_MODULE_";
	$controller = (isset($tmp[2])    && strlen($tmp[2])>0)?$tmp[2]:"_NO_CONTROLLER_";
	$action     = (isset($tmp[3])    && strlen($tmp[3])>0)?$tmp[3]:"_NO_ACTION_";
	$params     = (isset($tmp[4])    && strlen($tmp[4])>0)? array_slice($tmp, 4):"_NO_PARAMS_";
        //echo "<pre>";
        //var_dump($params);die(__FILE__);
	$params     =  $this->getParametersArray($params,$uri); 
	
	/*********************************************************************
	 * build result
	 ********************************************************************/
    $mvc = new Mvc();
	$mvc->module=$module;
	$mvc->controller=$controller;
	$mvc->action=$action;
	$mvc->params=$params; //array("a"=>"a1","b"=>"b2");
	//echo $mvc->toString();//debug
    $this->mvcInstance = $mvc;
   }
   public function getParametersArray($params){
      $newParamsArray = array();
      if($params == "_NO_PARAMS_"){
	     return $newParamsArray;
	  }else{
                  //echo "<pre>";
	          //var_dump($params);//check if tot is pair...
		  try{
			 $amount = count($params);
                         $increment = 0;
			 for ($i = 0; $i < $amount; $i++) {
			    if(($i  + $increment + 1) <= $amount-1){
				   $newParamsArray[$params[$i + $increment]] = $params[($i + $increment  + 1)];
                                   $increment+= 1;
				}				
			 }	
                         //echo "<pre>";
                         //var_dump($newParamsArray);
                         //die(__FILE__.__LINE__);
			 return $newParamsArray;
		  }catch(Exception $ex){
                     //die(__FILE__.__LINE__);
		     return $newParamsArray[] = 'ERROR in ' . __FILE__ . " line: " .  __LINE__ . " " . $ex->getMessage();
		  }
                  //die(__FILE__.__LINE__);
	  }
   }
   public function setConstants(){
	/**
	 * error
	 */
	error_reporting(E_ALL); ini_set("display_errors", 1);
        if(!defined('ERROR_PATH')){
	   define('ERROR_PATH', realpath(__DIR__.'/../app/layout/error/').DIRECTORY_SEPARATOR);}
        
	/**
	 * layout
	 */
        if(!defined('LAYOUT_DEFAULT_PATH')){ 
	   define('LAYOUT_DEFAULT_PATH', realpath(__DIR__.'/../app/layout/default/').DIRECTORY_SEPARATOR);
        }
        if(!defined('LAYOUT_PATH')){ 
	   define('LAYOUT_PATH', realpath(__DIR__.'/../app/layout/').DIRECTORY_SEPARATOR);}
	/**
	 * module
	 */
        if(!defined('MODULE_PATH')){ 
	   define('MODULE_PATH', realpath(__DIR__.'/../app/classes/modules/').DIRECTORY_SEPARATOR);}
	/**
	 * public
	 */
        if(!defined('PUBLIC_PATH')){ 
	   define('PUBLIC_PATH', realpath(__DIR__.'/../public/').DIRECTORY_SEPARATOR);}
	/**
	 * server
	 */
        if(!defined('SERVER_NAME')){ 
	   define( 'SERVER_NAME', "//" . $_SERVER["SERVER_NAME"] );}
   }
   /**
    * test the existence of module/contoller/action asked
    */
   public function run(){

	/*********************************************************************
	 * test against authorized values
	 ********************************************************************/

	/**
	 * authorized modules
	 */
	$array_modules = array (//todo: dynamic
           //qcm tool"
	       "storage",
	       "questionnaires",
	       "ajax_questionnaire",
           "ajax_exam",
           "ajax_admin",
           "ajax_report",
           "exam",
           "admin",
           "report",
           //ferme
           "ferme"
	);
	/**
	 * authorized controllers
	 */
	$array_controllers = array (//todo: dynamic
           //qcm tool"
	       "show",
		   "build",
		   "json",
           //ferme
           "calculateur"
	);
	/**
	 * authorized actions
	 */
	$array_actions = array (//todo: dynamic
           //qcm tool
	       "resp",
	       "all",
		   "edit",
		   "readquestionnaires",
           //ferme
           "userform"
	);
	$partOfMvcBotFound = FALSE;
	/**
	 * module found
	 */
	if (in_array($this->mvcInstance->module, $array_modules)) {
		/**
		 * controller found
		 */
		if (in_array($this->mvcInstance->controller, $array_controllers)) {
			/**
			 * action found
			 */
			if (in_array($this->mvcInstance->action, $array_actions)) {

				//buils the controller + action + view instances HERE!

				/**
				 * instance of controller
				 */
				include_once( 
				   MODULE_PATH                    . 
				   $this->mvcInstance->module     . '/controllers/' .
				   $this->mvcInstance->controller .
				   'Controller.class.php' );  //ex: showController.class.php
				
				$class = $this->mvcInstance->controller . "Controller";
				//var_dump(__FILE__ . " -> " . $class . " <-" );//die();
				$controllerInstance = new $class();     
				/**
				 * instance of model
				 */

			   $x = MODULE_PATH                    . 
				   $this->mvcInstance->module     . '/models/' .
				   $this->mvcInstance->controller .
				   'Model.class.php' ;  //ex: showController.class.php
                 //var_dump($x);
				 include_once( 
					MODULE_PATH                    . 
					$this->mvcInstance->module     . '/models/' .
					$this->mvcInstance->controller .
					'Model.class.php' ); 
			    $dbHost = $this->configInstance->applicationIni[$this->db_key_ini]['db_host'];
			    $user = $this->configInstance->applicationIni[$this->db_key_ini]['user'];
			    $password = $this->configInstance->applicationIni[$this->db_key_ini]['password'];
			    $dbName = $this->configInstance->applicationIni[$this->db_key_ini]['db_name'];

				$class = $this->mvcInstance->controller . "Model";
                //var_dump($class);
                
				$this->modelInstance = new $class($dbHost,$user,$password,$dbName);
                //echo($this->modelInstance->stest());die();
                //var_dump($this->modelInstance->stest());
				/**
                 * allows to customize Crud class per Module.
                 */
				//include_once ( MODULE_PATH . $this->mvcInstance->module . '/models/' .'Crud.class.php' );                                
				/**
				 * instance of view
				 */
				include_once( LAYOUT_PATH . $this->mvcInstance->module . '/' .'index.php' );   
			    /*$this->mvcInstance->module . 
			    $this->mvcInstance->module .'/index.php' ); */
			}else{
                   //echo "else of: action";die();
		           $partOfMvcBotFound = TRUE;
		        }
	       }else{
              //echo "else of: controller";die();
		      $partOfMvcBotFound = TRUE;
	       }
	}else{
        //echo "else of: module";die();
	    $partOfMvcBotFound = TRUE;
	}
    //var_dump($this->modelInstance->stest()); //die("test");
	/**
	 * default page asked OR something NOT found
	 */
	if($partOfMvcBotFound){
	    /**
	     * wrong part(s) detected
	     */
	    if($this->mvcInstance->module != "_NO_MODULE_" ||
	       $this->mvcInstance->controller != "_NO_CONTROLLER_" ||
	       $this->mvcInstance->action != "_NO_ACTION_"
	    )
	    {
	       include( realpath(ERROR_PATH . '404.php' ));
	    /**
	     * default page
	     */
	    }else{
	       include( LAYOUT_PATH . "default"  . '/index.php' );
	    }
	}
    return true;
   }
}

