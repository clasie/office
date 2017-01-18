<?php

class BuildModel extends Db {
    const TABLE_PROVIDER = 'users_tuto_provider';
    private $search = "";
    function __construct(){

        $this->connectDb(
          $_SESSION[Constant::APPLICATION_INI_CONFIG]
                   [$_SESSION[Constant::DB_ENV_KEY]] ['db_host'],
          $_SESSION[Constant::APPLICATION_INI_CONFIG]
                   [$_SESSION[Constant::DB_ENV_KEY]] ['user'],
          $_SESSION[Constant::APPLICATION_INI_CONFIG]
                   [$_SESSION[Constant::DB_ENV_KEY]] ['password'],
          $_SESSION[Constant::APPLICATION_INI_CONFIG]
                   [$_SESSION[Constant::DB_ENV_KEY]] ['db_name']
        );
        /*$sql = "
            CREATE TABLE IF NOT EXISTS test.users_tuto_data (
              id_provider int(5) NOT NULL AUTO_INCREMENT,
              fname varchar(50) NOT NULL,
              name varchar(100) NOT NULL,
              mail varchar(50) NOT NULL,
              PRIMARY KEY (id_user),
              KEY username (fname)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
            ";
            mysql_query($sql);*/
    }
    public function test(){return "ok test";}
    public function xconnectDb(){
        /* mysql_connect(
          $_SESSION[Constant::APPLICATION_INI_CONFIG]
                   [$_SESSION[Constant::DB_ENV_KEY]] ['db_host'],
          $_SESSION[Constant::APPLICATION_INI_CONFIG]
                   [$_SESSION[Constant::DB_ENV_KEY]] ['user'],
          $_SESSION[Constant::APPLICATION_INI_CONFIG]
                   [$_SESSION[Constant::DB_ENV_KEY]] ['password']);
        mysql_select_db(
          $_SESSION[Constant::APPLICATION_INI_CONFIG]
                   [$_SESSION[Constant::DB_ENV_KEY]] ['db_name']) or die(mysql_error());
        //mysql_query("SET NAMES 'utf-8'");
        mysql_query("SET NAMES UTF8");*/
    }
    public function my_addslashes($val){
        if(!get_magic_quotes_gpc())
            $val = addslashes($val);
        return $val;
    }
    public function insertUser($fname, $name, $mail){
        $fname = $this->my_addslashes($fname);
        $name  = $this->my_addslashes($name);
        $mail  = $this->my_addslashes($mail);
        $sql = "INSERT INTO " . Crud::TABLE_PROVIDER . " VALUES(NULL,'$fname','$name','$mail')";
        mysqli_query($this->con,$sql);
    }
    public function insertQuestionResponse($question, $response){
        $question = $this->my_addslashes(trim($question));
        $response = $this->my_addslashes(trim($response));
        if(!(strlen(trim($question)) > 0 && strlen(trim($response)) > 0)){
            //echo "Both values must be none empty!";
            return;
        }else{
        }
        //realm
	$realm = (isset($_POST["realm"]))?
	   htmlspecialchars($_POST["realm"]):'-1';
        //question
        $question = $this->my_addslashes($question);
        $sql = "INSERT INTO " . Constant::TABLE_QUESTION  . 
           " VALUES(NULL,'$question','$question','$question','$realm')";
        //var_dump("</br>sql: " . $sql );
        mysqli_query($this->con,$sql);
        $id = mysqli_insert_id($this->con);
        //var_dump("</br>id: " . $id );
        //Response
        //var_dump($response);
        $response  = $this->my_addslashes($response);
        //var_dump($response);
        $sql = "INSERT INTO " . Constant::TABLE_RESPONSE  .
           " VALUES(NULL,'$response','$response','$response','$id')";
        mysqli_query($this->con,$sql);
    }
    public function updateUser($id_provider, $fname, $name, $mail){
        $id_provider = $this->my_addslashes($id_provider);
        $fname = $this->my_addslashes($fname);
        $name  = $this->my_addslashes($name);
        $mail  = $this->my_addslashes($mail);
        $sql = "UPDATE " . Crud::TABLE_PROVIDER .
           " SET fname='$fname', name='$name', mail='$mail' " .
           " WHERE id_provider=$id_provider";
        mysqli_query($this->con,$sql);
    }
    public function deleteUser($id_provider){
        $id_provider = $this->my_addslashes($id_provider);

        $sql="DELETE FROM " . Crud::TABLE_PROVIDER . " WHERE id_provider=$id_provider";
        mysqli_query($this->con,$sql);
    }
    public function getUser(){}
    public function getAllUsers(){
        $sql = "SELECT * FROM " . Crud::TABLE_PROVIDER . "";
        $res = mysqli_query($this->con,$sql);
        return $res;
    }
    public function searchQuestionResponse($search){
       $this->search = addslashes($search);
    }
    public function getAllQuestions($realm ){
        //realm filter
        $realm_and ="";
        if($realm > 0){
           $realm_and = " AND q.id_realm='" . $realm . "'";
           $realm_on  = " ON q.id_realm='" . $realm . "'";
        }
        //with search filter asked
        if(strlen($this->search) > 0)
        {
           $sql = "
		SELECT q.id_question, q.fr 'question', r.fr 'response' 
		FROM qcms_question AS q 
		JOIN qcms_response_correction AS r 
		ON q.id_question = r.id_question 
		AND 
                (
			(q.fr LIKE '%" . $this->search . "%' OR r.fr LIKE '%" . $this->search . "%')
		        OR
		        (q.nl LIKE '%" . $this->search . "%' OR r.nl LIKE '%" . $this->search . "%')
		        OR
		        (q.en LIKE '%" . $this->search . "%' OR r.en LIKE '%" . $this->search . "%')
                )
               " . $realm_and;
        }
        //get all questions
        else
        {
           $sql = "
		SELECT q.id_question, q.fr 'question', r.fr 'response'
		FROM qcms_question AS q
		JOIN qcms_response_correction AS r 
                ON q.id_question = r.id_question
                " . $realm_and . "
                ORDER BY q.id_question
               ";
        }
        //var_dump($sql);
        $res = mysqli_query($this->con,$sql);
        //var_dump($res); die(__FILE__);
        return $res;
    }
}

class Form {
    public function getForm($action="", $method="POST"){
        
        $form=<<<FORM
           <form name="update" action="$action" method="$method" >
               
               <input type="hidden" name="id_provider" value="ID_VALUE" />

               <input type="text" name="fname" value="FNAME_VALUE" />
               <input type="text" name="name"  value="NAME_VALUE" />
               <input type="text" name="mail"  value="MAIL_VALUE" />

               <input type="submit" name="update" value="Update" />
               <input type="submit" name="delete" value="Delete" />
           </form>
FORM;
        return $form;
    }
    public function getFormInsert($action="", $method="POST"){

        $form=<<<FORM
           <form name="update" action="$action" method="$method" >

               <input type="text" name="fname" value="" />
               <input type="text" name="name"  value="" />
               <input type="text" name="mail"  value="" />

               <input type="submit" name="insert" value="New" />
           </form>
FORM;
        return $form;
    }
}

