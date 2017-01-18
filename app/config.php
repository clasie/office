<?php
die(__FILE__);
/**
 * session
 */
session_start();

/**
 * error config
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

/**
 * get constants for the application
 */

include_once '../lib_custom/Constant.class.php';

/**
 * get env db key for application.ini vals
 */

//$db_key_ini = (Constant::ENV_LOCAL == getenv('ENV'))? 
//   Constant::DB_KEY_ENV_LOCAL: Constant::DB_KEY_ENV_REMOTE;
echo getenv('ENV'); die(__FILE__);
if(Constant::ENV_LOCAL == getenv('ENV')){ // hp ProBook, newest PC
    $db_key_ini = Constant::DB_KEY_ENV_LOCAL;
}
if(Constant::ENV_REMOTE == getenv('ENV')){ //remote ovh server
    $db_key_ini = Constant::DB_KEY_ENV_REMOTE;
}
if(Constant::ENV_LOCAL_OLD_PC == getenv('ENV')){ //hp amster, the oldest PC
    $db_key_ini = Constant::DB_KEY_ENV_LOCAL_OLD_PC; 
}

$_SESSION[Constant::DB_ENV_KEY] = $db_key_ini;

/**
 * get application.ini config
 */

$application_ini = parse_ini_file("../config/application.ini",true);

$_SESSION[Constant::APPLICATION_INI_CONFIG] = $application_ini;

/**
 * import tools
 */

include_once '../lib_custom/Auth.class.php';//to do
include_once '../lib_custom/Crud.class.php';
