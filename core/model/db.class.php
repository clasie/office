<?php

class Db {
    private $search = "";
    public $dbHost = "";
    public $con = null;
    function __construct($dbHost,$user,$password,$dbName){
        $this->connectDb($dbHost,$user,$password,$dbName);
    }
    public function connectDb($dbHost,$user,$password,$dbName){
        //die("dbHost ".$dbHost);
        $this->dbHost = $dbHost;
        $this->con = mysqli_connect($dbHost,$user,$password);
        mysqli_select_db($this->con, $dbName) or die(mysql_error());
	if(Constant::ENV_LOCAL == getenv('ENV')){
		//mysqli_query($this->con, "SET NAMES UTF8");
	}
	mysqli_query($this->con, "SET NAMES utf8") ;
    }
    public function my_addslashes($val){
        if(!get_magic_quotes_gpc())
            $val = addslashes($val);
        return $val;
    }
   
}

