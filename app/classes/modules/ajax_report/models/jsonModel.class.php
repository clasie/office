<?php
/**
    * UPLOAD a file has been asked
    */
//class UploadedFile {
//    public $file_url        = "";  
//    public $file_name       = ""; 
//    public $file_error      = ""; 
//    public $file_success    = "";
//    public $file_type       = "";
//    public $file_size       = "";
//    public $file_temp       = "";
//    public $file_id         = "";
//} 
class User {
   public $id ="";
   public $log ="";
   public $mail ="";
}
class JsonModel extends Db {
    const TABLE_QUESTIONNAIRE = 'q_ID_USER_questionnaire';
    const TABLE_POSS_ANSWERS  = 'q_ID_USER_poss_answers';
    const TABLE_IMAGES        = 'q_ID_USER_images';
    const DIR_IMAGES          = '/upload/';
    const ORIGIN_QUEST        = "REPORT";
    const ORIGIN_EXAM         = "EXAMEN";
    
    //fpr the exam
    const TABLE_QUESTIONNAIRE_EXAM        = 'q_ID_USER_questionnaire_exam';
    const TABLE_POSS_ANSWERS_EXAM         = 'q_ID_USER_poss_answers_exam';
    const TABLE_RESPONSES_EXAM            = 'q_ID_USER_responses_exam';
    const TABLE_IMAGES_EXAM               = 'q_ID_USER_images_exam';  
    
    //tables pre creator id
    const TABLE_RESPONSES            = 'q_ID_USER_responses';
    
    //shared tables => no id in it
    const TABLE_USERS_QUEST          = 'q_users_questionnaires';
    const TABLE_USERS_EXAM           = 'q_users_examens';    
    const TABLE_USERS_ADMIN          = 'q_users_admin';    
    const TABLE_KEYS                 = 'q_keys';  
    const TABLE_KEY2QUESTIONNAIRES   = 'q_key2questionnaire';
    const TABLE_USERS_EXAMEN2KEY     = 'q_users_examens2key';
    
    const RESPONSE_TYPE_EXCLU  = '4';
    const RESPONSE_TYPE_INCLU  = '5';
    const RESPONSE_TYPE_TEXT   = '1';
    const RESPONSE_TYPE_NUMBER = '2';
    
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
    //public function getTableName($id_user,$table_name){
    //   return str_replace(JsonModel::REPLACE_KEY_ID_USER, $id_user, $table_name);
    //}      
    public function test(){return "ok test";}
    
    public function getQuestionnaireIdExamCorr($id_user, $id){
	    $idFound = "-1";
	    $sql = "SELECT * FROM   " . 
			    $this->getTableName(
				    $id_user,
				    JsonModel::TABLE_QUESTIONNAIRE_EXAM
			    )
			    . 
			    " WHERE qr_orig=$id AND  qr_running_status=1 " . 
			    " ORDER BY position";
		 //var_dump($sql);	
	     $res = mysqli_query($this->con,$sql);  
	 
	     foreach ($res as $key => $value)
	     {
		    $idFound = $value['id'];
		    break;
	     }         
				
	     return $idFound;
    }
    
    public function my_addslashes($val){
        if(!get_magic_quotes_gpc())
            $val = addslashes($val);
        return $val;
    }
    public function getResponses
       ($id_author,$id_questionnaire,$users){
       
        /**
         * build here an array cor with id question-> point
         */
        $sqlQuest = "  SELECT * FROM " 
        . 
        $this->getTableName(
            $id_author,
            JsonModel::TABLE_QUESTIONNAIRE_EXAM
        )        
        . " WHERE id=$id_questionnaire OR parent=$id_questionnaire";
                
        $resQuest = mysqli_query($this->con,$sqlQuest);
        $questPointsCor = null;
        $questPointsCorGoodAnswer = null;
        foreach ($resQuest as $key => $valueQuest) {
               //echo 1;
               //if($valueQuest['q_points'] != null){
               $questPointsCor[$valueQuest['id']] = $valueQuest['q_points'];
               if(
                  ($valueQuest['q_resp_type'] == '1' ) // q_good_response_txt
                 ){
                 //echo 2;
                  $questPointsCorGoodAnswer[$valueQuest['id']] 
                     = $valueQuest['q_good_response_txt'];
               }
               else if(
                  ($valueQuest['q_resp_type'] == '2' ) // q_good_response_number
                 ){
                 //echo 3;
                  $questPointsCorGoodAnswer[$valueQuest['id']] 
                     = $valueQuest['q_good_response_number'];
               }               
            //}
        }
        //echo "-------1----------->";
        //var_dump($questPointsCor);
        //echo "-------2----------->";
        //var_dump($questPointsCorGoodAnswer);
        //echo "<-------3-----------";
        /**
         * responses
         */
        $select_in = "";
        foreach($users as $value){
          $select_in .= $value . ",";
        }
        
        $select_in = rtrim($select_in, ",");
        $select_in = " AND id_user_answering IN(" . $select_in . ") ";
        
        $sql = "  SELECT * FROM " 
        . 
        $this->getTableName(
            $id_author,
            JsonModel::TABLE_RESPONSES_EXAM
        )        
        . " WHERE id_questionnaire=$id_questionnaire " // AND id_question='163'"
        . $select_in
        . " ORDER BY id_question ASC ";
        
        //var_dump($sql);
        $res = mysqli_query($this->con,$sql);
        
        $responses = array();
        $usersResponse = array();
        $response = array();
                   
        foreach ($res as $key => $value)
        {
            //detecter ici le type de reponses => if possanswers -> aller chercher la correspondance 
            // avec l id
            $response = array();
            $response['id_questionnaire'] = $value['id_questionnaire'];
            $response['id_question'] = $value['id_question'];
            $response['id_user_answering'] = $value['id_user_answering'];
            $response['resp_type'] = $value['resp_type'];
            $response['rcm'] = $value['rcm'];//peut etre multiple ou pas
            $response['resp_text'] = $value['resp_text'];            
            $response['resp_datetime'] = $value['resp_datetime'];
            $response['response_id'] = $value['id'];
            $response['points'] = //"5";
               ($questPointsCor[$response['id_question']]!=null)?
                  $questPointsCor[$response['id_question']]:"0";
            //var_dump($response);     
            //$value['q_points'];
            
            //peut etre multiple ou pas
            $rcm_array = explode(";",$response['rcm']);
            
            $tmpConcat ="";
            $values = null;
            $values_total = 0;
            $counterParenthesis = 0;
            
            /**
               qi IF -> type [4,5]
               
                     -->Boucler sur les resp ids
                     
                     --->getTotalObtenuParLesRepSelectionneesParLeUser
                     --->getTotalMaximumDesPointsDesBonnesReponses
                     
                  IF -> type [2]
                  
                     --->getTotalObtenuEnComparantLaRep
                     --->getTotalObtenuPouLaBonneReponse
             */
            
            //var_dump($value['id_question'] .  " ------------------> ");
            //var_dump($rcm_array);
            
            //on va additionner les points lies 
            //aux reponses selectionnees par le user
            foreach ($rcm_array as $valuex){
                if(trim($valuex) == ""){
                   continue;
                }
                //var_dump("corr->");
                $tmp = $this->getCorrespondingText(
                    $id_author,
                    $response['resp_type'],
                    $valuex
                );
                
                //var_dump($tmp);
                
                //var_dump("<-corr");
                
                $tmpBr ="";
                if($value['resp_type'] == "5"){//INCLUSIVE
                   $tmpBr="</br>";
                   //var_dump($tmp);
                }
                $values_total += $tmp["points"];
                ////var_dump($values_total);
                $tmpConcat .= " [ " . $tmp["content"] . " ] " . $tmpBr;
                $counterParenthesis++;
            }
            //var_dump($value['id_question'] .  " <------------------ ");
            //if($value['resp_type'] == "4"){//EXCLUSIVE
                //var_dump($rcm_array);//$values_total
                //var_dump($values_total);
            //}
            
            if($counterParenthesis==1){
               $tmpConcat =  trim($tmpConcat);
               $tmpConcat = ltrim($tmpConcat,'[');
               $tmpConcat = rtrim($tmpConcat,']');
               $tmpConcat =  trim($tmpConcat);
            }
            
            if($value['resp_type'] == "5" || $value['resp_type'] == "4"){
               $response['points']  = $values_total;
            }else if($value['resp_type'] == "1" || $value['resp_type'] == "2")
            {
               //echo "ok here1";
               //var_dump($questPointsCorGoodAnswer);
               //var_dump($questPointsCorGoodAnswer[$response['id_question']]);
               //var_dump($response['resp_text']);
               if($response['resp_text'] != $questPointsCorGoodAnswer[$response['id_question']]){
                  $response['points'] = 0;
               }
               //$response['id_question']
               //$response['points']  = $response['points'];
            }
            
            //$response['points'] = ($values_total>0)?$values_total:$response['points'];
            $response['resp_text_2'] = $tmpConcat;
            //var_dump($response);
            $res_images = $this->getImages($id_author,$response['id_question']); //$id_item );
            
            //var_dump($tmpConcat); 
            $counter = 0;
            $image_group = array();
            //var_dump("rrrrrrrrrrrrrrrrrrr");
            //var_dump($image_group);
            
            while($image = mysqli_fetch_array($res_images, MYSQLI_ASSOC)){
    
               $uploadedFile = new UploadedFile();
               $uploadedFile->file_url = '../../../upload/' . $id_author . '/' . $image['image_name'];
               $image_group[$counter++] = $uploadedFile; //"tututu " . $counter . ' ' . $uploadedFile->file_url ;
               
            }      
            $response['IMAGES_RESPONSES'] = $image_group; //$images;
            
            $responses[$value['id_question']]
                         [$value['id_user_answering']]
                            [] = $response;

            //var_dump($image_group);
        }        
        //die(__FILE__);
        
        return $responses;       
    }    
    private function getCorrespondingText($id_author,$respType, $id){
        if
        (
           $respType == JsonModel::RESPONSE_TYPE_EXCLU 
           || 
           $respType == JsonModel::RESPONSE_TYPE_INCLU
        )
        {
            $sql = "SELECT * FROM " . 
            $this->getTableName(
              $id_author,
              JsonModel::TABLE_POSS_ANSWERS_EXAM
            )            .
            " WHERE id=$id ";
            //var_dump($sql);
            $results = mysqli_query($this->con,$sql);       
            $valuesx = null;
            try{
                foreach ($results as $key => $value)
                {
                    $valuesx['points']  = $value['q_points'];
                    $valuesx['content'] = $value['content'];
                    //var_dump($valuesx);
                    return $valuesx; //$value['content'];
                }             
                return ""; //Possible answer not found";
            }catch(Exception $ex){
               return ""; //Possible answer not found";
            }
            //return $sql; //"Possible answer not found";
        }
       //return "resp type = ". $respType . " id response = " .  $id; 
    }
    //public function getExistingAnswer
    //   ($id_questionnaire,$id_question,$id_answering_user){
       
    //    $sql = "  SELECT * FROM " . JsonModel::TABLE_RESPONSES . " WHERE " .
    //           "  id_questionnaire=$id_questionnaire AND " . 
    //           "  id_question=$id_question           AND " . 
    //           "  id_user_answering=$id_answering_user   ";
               
    //    $res = mysqli_query($this->con,$sql);
    //    return $res;       
    //}      
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
    //public function getUser(){}
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
    private function getTicketData($ticket){
        $ticket = $this->my_addslashes($ticket);
        $sql = "SELECT * FROM   " . JsonModel::TABLE_KEYS .
               " WHERE ticket='$ticket' ";
               
        $results = mysqli_query($this->con,$sql);   
        $values = array();
        
        if(!$results){ //ne result found
           return $values;
        }
        foreach ($results as $key => $value)
        {
            $values['id_ticket'] = $value['id'];
            $values['id_author'] = $value['id_users_questionnaires'];
            break;
        }   
        return $values;
    }  
    public function getQuestionnaireListForOneAuthor($id_author){
    
        /**
         * 1- get the tickets
         */
        $sql = " SELECT * FROM   " . JsonModel::TABLE_KEYS .
               " WHERE id_users_questionnaires='$id_author' ";
        $results = mysqli_query($this->con,$sql); 
        $values = array();
        foreach ($results as $key => $value)
        {
            $values[] = $value['id'];
        }  

        /**
         * 2- get the ticket's ids
         */
        $questionnaireIds = array();
        foreach ($values as $value) {
            $sql = " SELECT * FROM   " . JsonModel::TABLE_KEY2QUESTIONNAIRES .
                   " WHERE id_ticket='$value' ";
            $results = mysqli_query($this->con,$sql); 
            foreach ($results as $key => $value)
            {
                $questionnaireIds[] = $value['id_questionnaire'];
            }             
        }
        
        /**
         * 3- get questionnaires label + id
         */        
        $questionnaires = array();        
        foreach ($questionnaireIds as $value) {
        
            $sql = "SELECT * FROM " . 
                   $this->getTableName(
                      $id_author,
                      JsonModel::TABLE_QUESTIONNAIRE
                   )            .
                   " WHERE id='$value'   ORDER BY position";
                   
            //var_dump($sql);     
            
            $results = mysqli_query($this->con,$sql); 
            foreach ($results as $key => $value)
            {
                $questionnaire = array();
                $questionnaire['id'] = $value['id'];
                $questionnaire['tree_label'] = $value['tree_label'];
                $questionnaires[] = $questionnaire;
            }
        }
        return $questionnaires;
    }     
    public function getTicketDataFromId($id_ticket){
        $id_ticket = $this->my_addslashes($id_ticket);
        $sql = "SELECT * FROM   " . JsonModel::TABLE_KEYS .
               " WHERE id='$id_ticket' ";
               
        $results = mysqli_query($this->con,$sql);   
        $values = array();
        
        if(!$results){ //ne result found
           return $values;
        }
        foreach ($results as $key => $value)
        {
            $values['id_ticket'] = $id_ticket;
            $values['id_author'] = $value['id_users_questionnaires'];
            $values['value']     = $value['ticket'];
            $values['label']     = $value['label'];
            break;
        }   
        return $values;
    }     
    public function isUserLinkToThisKey($id_user,$id_ticket){
       $sql = "SELECT * FROM " . JsonModel::TABLE_USERS_EXAMEN2KEY . " WHERE id_user_examens='$id_user' AND id_ticket='$id_ticket' ";
       $results  = mysqli_query($this->con,$sql); 
       if($results){
         return true;
       }else{
         return false;
       }       
    }
    public function getUser($id_user){
        $user_data = array();
        $sql = "SELECT * FROM " . JsonModel::TABLE_USERS_EXAM 
             . " WHERE id='$id_user' ";
        
        $results  = mysqli_query($this->con,$sql);  
        $users = array();
        //echo 1;
        if($results){
            //echo 2;            
            $rowcount = mysqli_num_rows($results);
            if($rowcount > 0)
            {                
                foreach ($results as $key => $value)
                {
                    $user_data['user_id'] = $value['id'];
                    $user_data['user_log'] = $value['log'];
                    $user_data['user_mail'] = $value['mail'];
                    $user_data['mocker'] = $value['mocker'];
                    $users[] = $user_data;
                }             
                return $users; //false;
            }else{
               //echo 4;
               $user_data['users_found'] = false;
               $users[] = $user_data;
               return $users; //false;
            }
        }else{
           //echo 5;
           $user_data['users_found'] = false;
           $users[] = $user_data;
           return $users; //false;
        }     
    }
    public function getLinkedUsers($id_questionnaire,$id_author){ 

       $ticket_id = $this->getTicketIdFromQuestionnaireId($id_questionnaire);
       
       //var_dump("id questionnaire: " . $id_questionnaire);
       //var_dump("id id author: " .$id_author);
       //var_dump("id ticket: " .$ticket_id);
       
       $sql = " SELECT * FROM " . JsonModel::TABLE_USERS_EXAMEN2KEY . 
              " WHERE id_ticket='$ticket_id'"; // AND id_author='id_author' ";   
       $results  = mysqli_query($this->con,$sql); 
       $users = array();
       $user = array();
       
       if($results){
            foreach ($results as $key => $value)
            {
                $retr_user = $this->getUser($value['id_user_examens']);
                
                if($retr_user[0]['mocker'] != "on"){
                    $user['user_id'] = $value['id_user_examens'];
                    //$retr_user = $this->getUser($user['user_id']);
                    $user['user_log'] = $retr_user[0]['user_log'];
                    $user['user_mail'] = $retr_user[0]['user_mail'];
                    $user['mocker'] = $retr_user[0]['mocker'];
                    $users[] = $user;
                }
            } 
       }   
       return $users;
    }   
    public function updateAuthUser( //2222
        $user_id,
        $user_login,
        $user_mail,
        $questionnaire_pass 
                       )
    {
       $res ['error']   = '';
       
       // id user must exists
       if(strlen(trim($user_id)) == 0){
          $res ['error'] = "Unknown user";
          return $res;
       }
       
       // login
       if(strlen(trim($user_login)) == 0){
          $res ['error'] = "Login empty";
          return $res;
       }       
       
       // mail
       //if(length(trim($user_mail)) == 0){
       //   $res ['error'] = "Mail empty";
       //   return $res;
       //}       
       
       // pw
       if(strlen(trim($questionnaire_pass)) == 0){
          $res ['error'] = "Pw empty";
          return $res;
       }         
       
       $sql =   " UPDATE " . JsonModel::TABLE_USERS_QUEST            .
                " SET log='$user_login', pw='$questionnaire_pass',mail='$user_mail'" .
                " WHERE id='$user_id'";
                
       //var_dump($sql);
       mysqli_query($this->con,$sql);
       
       return $res;
    }    
    public function deleteAuthUser( //2222
        $user_id,
        $user_login,
        $user_mail,
        $questionnaire_pass 
                       )
    {
       $res ['error']   = '';
       
       // id user must exists
       if(strlen(trim($user_id)) == 0){
          $res ['error'] = "Unknown user";
          return $res;
       }
            
       /**
        * delete Auth user
        */
       $sql = " DELETE FROM " . JsonModel::TABLE_USERS_QUEST . 
              " WHERE id='$user_id' ";  
       mysqli_query($this->con,$sql); 
       /**
        * delete exam users
        */
       $sql = " DELETE FROM " . JsonModel::TABLE_USERS_EXAM . 
              " WHERE id_author='$user_id' ";                
       mysqli_query($this->con,$sql); 
       /**
        * delete auth x tables
        */
       $table_name = "q_" . $user_id . "_image";
       $sql = " DROP TABLE $table_name ";  
       mysqli_query($this->con,$sql);        
       $table_name = "q_" . $user_id . "_poss_answers";
       $sql = " DROP TABLE $table_name "; 
       mysqli_query($this->con,$sql);        
       $table_name = "q_" . $user_id . "_questionnaire";
       $sql = " DROP TABLE $table_name "; 
       mysqli_query($this->con,$sql);        
       $table_name = "q_" . $user_id . "_responses";
       $sql = " DROP TABLE $table_name "; 
       mysqli_query($this->con,$sql);        
       
       /**
        * delete all link with thickets
        */
       $sql = " SELECT * FROM " . JsonModel::TABLE_KEYS . 
              " WHERE id_users_questionnaires='$user_id'";   
       $results  = mysqli_query($this->con,$sql); 
       
       $ticket = "";          
       if($results){
            foreach ($results as $key => $value)
            {
               $ticket = $value['id'];
               /**
                * delete  users - tickets
                */
               $sql = " DELETE FROM " . JsonModel::TABLE_USERS_EXAMEN2KEY . 
                      " WHERE id_ticket='$ticket' ";     
               mysqli_query($this->con,$sql); 
               /**
                * delete tickets
                */
               $sql = " DELETE FROM " . JsonModel::TABLE_KEYS . 
                      " WHERE id='$ticket' ";   
               mysqli_query($this->con,$sql);
               /**
                * delete tickets
                */
               $sql = " DELETE FROM " . JsonModel::TABLE_KEY2QUESTIONNAIRES . 
                      " WHERE id_ticket='$ticket' ";     
               mysqli_query($this->con,$sql);                    
            } 
       }        
       /**
        * delete the dir
        */
       $path_dir = realpath(getcwd() . '/..'); ///upload/' . $user_id;
       $path_dir = realpath($path_dir . '/upload/' . $user_id);
       $this->rrmdir($path_dir);
       
       return $res;
    }     
    function rrmdir($dir) {
       if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
             if (filetype($dir."/".$object) == "dir"){
                rrmdir($dir."/".$object);
             }else{ 
                unlink($dir."/".$object);
             }
           }
         }
         reset($objects);
         rmdir($dir);
      }
    }    
    public function newAuthUser( //2222
        //$auth_id,
        $user_login,
        $user_mail,
        $questionnaire_pass 
                       )
    {
       $res ['error']   = '';
       
       // login
       if(strlen(trim($user_login)) == 0){
          $res ['error'] = "Login empty";
          return $res;
       }            
       
       // pw
       if(strlen(trim($questionnaire_pass)) == 0){
          $res ['error'] = "Pw empty";
          return $res;
       }         
                
       $sql =   "  INSERT INTO " . JsonModel::TABLE_USERS_QUEST          .  
                "  (log,pw,mail) "                            .
                "  VALUES('$user_login','$questionnaire_pass','$user_mail')";
                
       mysqli_query($this->con,$sql);
       /**
        * get the new id back
        */
       $res ['id'] = mysqli_insert_id($this->con);
       /**
        * create folder system
        */
        $path_dir = getcwd() . '/../upload/' . $res ['id'];
        //var_dump($path_dir);
        if (!is_dir($path_dir)) {
            @mkdir($path_dir);
        }
       /**
        * create tables
        */
       $res ['message'] = $this->createDbTableStructure($res ['id']);
       
       return $res;
    }        
    public function createDbTableStructure($new_id_author){   
        $message = "";
        /**
         * ceration table q_1_images
         */
        $table_name = "q_" . $new_id_author . "_images";
        $sql = "              
            CREATE TABLE IF NOT EXISTS " . $table_name . " (
              id int(11) NOT NULL AUTO_INCREMENT,
              item_type varchar(30) CHARACTER SET utf8 NOT NULL,
              id_item int(6) NOT NULL,
              item_label varchar(100) CHARACTER SET utf8 NOT NULL,
              image_name varchar(100) CHARACTER SET utf8 NOT NULL,
              weight int(6) NOT NULL,
              PRIMARY KEY (id)
            ) ENGINE=MyISAM  DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin AUTO_INCREMENT=60
            ";
        if ($this->con->query($sql) === TRUE) {
            $message .= " $table_name created successfully" . 
"
";
        } else {
            $message .= "Error creating table: $table_name ,  error" . $this->con->error .
"
";   
        }
        /**
         * ceration table q_1_poss_answers
         */
        $table_name = "q_" . $new_id_author . "_poss_answers";
        $sql = "              
            CREATE TABLE IF NOT EXISTS " . $table_name . " (
              id int(11) NOT NULL AUTO_INCREMENT,
              answ_type int(3) NOT NULL,
              content text NOT NULL,
              is_good_response tinyint(1) NOT NULL,
              id_question int(11) NOT NULL,
              sequence int(5) NOT NULL,
              PRIMARY KEY (id),
              KEY answ_type (answ_type,id_question)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=210 ;
        ";
        if ($this->con->query($sql) === TRUE) {
            $message .=  " $table_name created successfully".
"
"; 
        } else {
            $message .=  "Error creating table: $table_name ,  error" . $this->con->error.
"
"; 
        }        
        /**
         * ceration table q_1_questionnaire
         */
        $table_name = "q_" . $new_id_author . "_questionnaire";
        $sql = "              
                CREATE TABLE IF NOT EXISTS " . $table_name . " (
                  id int(11) NOT NULL AUTO_INCREMENT,
                  tree_label text,
                  qr_state int(3) DEFAULT NULL,
                  qr_save int(3) DEFAULT NULL,
                  qr_random int(3) DEFAULT NULL,
                  qr_date_start date DEFAULT NULL,
                  qr_date_stop date DEFAULT NULL,
                  qr_autocorrection tinyint(1) DEFAULT NULL,
                  qr_comment text,
                  parent varchar(30) NOT NULL,
                  node_type varchar(40) NOT NULL,
                  q_label text NOT NULL,
                  q_resp_type int(4) NOT NULL,
                  q_good_response_txt text NOT NULL,
                  q_good_response_date date NOT NULL,
                  q_good_response_number varchar(250) NOT NULL,
                  position int(5) NOT NULL,
                  PRIMARY KEY (id)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=170 ;
        ";
        if ($this->con->query($sql) === TRUE) {
            $message .=  " $table_name created successfully".
"
";             
        } else {
            $message .=  "Error creating table: $table_name ,  error" . $this->con->error.
"
";             
        }     
        /**
         * ceration table q_1_responses
         */
        $table_name = "q_" . $new_id_author . "_responses";
        $sql = "                 
            CREATE TABLE IF NOT EXISTS " . $table_name . " (
              id int(11) NOT NULL AUTO_INCREMENT,
              id_question int(5) NOT NULL,
              id_questionnaire int(5) NOT NULL,
              resp_type int(4) NOT NULL,
              rcm varchar(150) CHARACTER SET utf8 NOT NULL,
              resp_text text COLLATE armscii8_bin NOT NULL,
              id_user_answering int(5) NOT NULL,
              resp_datetime date NOT NULL,
              PRIMARY KEY (id),
              KEY id_question (id_question,id_questionnaire,resp_type,rcm)
            ) ENGINE=MyISAM  DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin AUTO_INCREMENT=243 ;
        ";
        if ($this->con->query($sql) === TRUE) {
            $message .=  " $table_name created successfully".
                         "
                         ";             
        } else {
            $message .=  "Error creating table: $table_name ,  error" . $this->con->error.
                         "
                         ";             
        }      
        return $message;
    }
    public function getTicketIdFromQuestionnaireId($id_questionnaire){
    
       $sql = " SELECT id_ticket FROM " . JsonModel::TABLE_KEY2QUESTIONNAIRES . 
              " WHERE id_questionnaire='$id_questionnaire'";
              
       $results  = mysqli_query($this->con,$sql); 
       if($results){
            foreach ($results as $key => $value)
            {
                return $value['id_ticket'];
            } 
       }else{
          return null;
       }   
    }  
    public function linkUsersToTicket(
       $users, $id_questionnaire
    )
    {
       /**
        *  get associated ticket id
        */    
       $id_ticket = $this->getTicketIdFromQuestionnaireId($id_questionnaire);
       if($id_ticket == null){
          return "No ticket found, please create on ticket first";
       }
       
       /**
        *  delete all
        */
       $sql = " DELETE FROM " . JsonModel::TABLE_USERS_EXAMEN2KEY . 
              " WHERE id_ticket='$id_ticket' ";             
       mysqli_query($this->con,$sql); 
       /**
        * insert all
        */
       foreach ($users as $id_user){
            $sql = "  INSERT INTO " . JsonModel::TABLE_USERS_EXAMEN2KEY          .  
                   "  (id_user_examens,id_ticket) " .
                   "  VALUES('$id_user','$id_ticket')";
            mysqli_query($this->con,$sql);
            mysqli_insert_id($this->con);  
       }
       return "";
    }
    public function getUniqueTicketForAQuestionnaire(
        $id_author,
        $old_ticket,
        $ticket_label,
        $questionnaire_id
    ){   
        // compare and save it here: q_key2questionnaire

        $code = $this->generateCode();
        $continue = true;
        $new_id = "";
        $counter = 0;
        while($continue){
           $counter++;
           if($counter>10){
              $continue = false;
              //var_dump($code);
           }
           try{
               if($this->isUniqueTicket($code)) // on a un code unique to insert ou update
               { 
                  /**
                   * test si OLD ticket exists -> we just update it
                   */
                  if(!$this->isUniqueTicket($old_ticket)){  //update
                  
                        //var_dump("old_ticket $old_ticket n est pas unique -> on update");
                        
                        $sql = " UPDATE " . JsonModel::TABLE_KEYS            .
                               " SET ticket='$code', label='$ticket_label' " .
                               " WHERE ticket='$old_ticket'";
                        //var_dump($sql);
                        mysqli_query($this->con,$sql);
                        $a['ticket'] = $code; 
                        $a['new_id_ticket'] = "-2";
                        return $a;         
                        
                  }else{                                    // insert
                        $new_id = $this->saveUniqueTicket($code,$ticket_label,$id_author);
                        $a['ticket'] = $code; 
                        $a['new_id_ticket'] = $new_id;
                        
                        /**
                         * Associate questionnaire id when FIRST creation of this TICKET
                         */               
                         $new_id_ticket2questionnaire = $this->saveTicket2questionnaire($new_id,$questionnaire_id);
                         $a['new_id_ticket2questionnaire'] = $new_id_ticket2questionnaire;
                         
                        return $a;                     
                  }
                  $continue = false;
               }else{
                  var_dump("NOT UNIQUE");
               }
           }catch(Exception $e){
              $continue = false;
              $a['ticket'] = "Exception occured " . $e->getMessage();
              $a['new_id_ticket'] = "-1";
              return $a;
           }
        }
    }    
    // TABLE_KEY2QUESTIONNAIRES
    public function saveTicket2questionnaire($id_ticket,$id_questionnaire){
        $sql = " INSERT INTO " . JsonModel::TABLE_KEY2QUESTIONNAIRES          .  
               " (id_questionnaire,id_ticket) " .
               "  VALUES('$id_questionnaire','$id_ticket')";
        //disp($sql,__FILE__);
        mysqli_query($this->con,$sql);
        return mysqli_insert_id($this->con);      
    }    
    public function saveUniqueTicket($code, $label, $id_users_questionnaires){
        $sql = " INSERT INTO " . JsonModel::TABLE_KEYS          .  
               " (ticket,label,id_users_questionnaires) " .
               "  VALUES('$code','$label','$id_users_questionnaires')";
        //disp($sql,__FILE__);
        mysqli_query($this->con,$sql);
        return mysqli_insert_id($this->con);      
    }
    public function isUniqueTicket($unique){

        $sql        = "SELECT * FROM " . JsonModel::TABLE_KEYS . " WHERE ticket='$unique' ";
        //var_dump("test is unique: " . $sql);
        $results    = mysqli_query($this->con,$sql);  
        $users      = array();
        $rowcount   = mysqli_num_rows($results);
        if($rowcount>0)
        {          
           return false;
        }else{
           return true;
        }      
    }
    public function generateCode(){

        $unique =   FALSE;
        $length =   19;
        $chrDb  =   array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9');

        //while (!$unique){

        $str = '';
        for ($count = 0; $count < $length; $count++){

            $chr = $chrDb[rand(0,count($chrDb)-1)];

            if (rand(0,1) == 0){
                $chr = strtolower($chr);
            }
            if (3 == $count || 6 == $count || 9 == $count || 12 == $count || 15 == $count){
                $str .= '-';
            }
            $str .= $chr;
        }
        return $str;
    }    
    public function getAuthorUsers(){     
        $user_data = array();
        $sql = "SELECT * FROM " . JsonModel::TABLE_USERS_QUEST ;
        
        $results  = mysqli_query($this->con,$sql);  
        $users = array();
        //echo 1;
        if($results){
            //echo 2;            
            $rowcount = mysqli_num_rows($results);
            if($rowcount > 0)
            {                
                foreach ($results as $key => $value)
                {
                    $user_data['user_id'] = $value['id'];
                    $user_data['user_log'] = $value['log'];
                    $user_data['user_mail'] = $value['mail'];
                    $user_data['user_pw'] = $value['pw'];
                    $users[] = $user_data;
                }             
                return $users; //false;
            }else{
               //echo 4;
               $user_data['users_found'] = false;
               $users[] = $user_data;
               return $users; //false;
            }
        }else{
           //echo 5;
           $user_data['users_found'] = false;
           $users[] = $user_data;
           return $users; //false;
        }
    }     
    public function getExamUsersForOneAuthor($id_author){     
        $user_data = array();
        $sql = "SELECT * FROM " . JsonModel::TABLE_USERS_EXAM . " WHERE id_author='$id_author' ";
        
        $results  = mysqli_query($this->con,$sql);  
        $users = array();
        //echo 1;
        if($results){
            //echo 2;            
            $rowcount = mysqli_num_rows($results);
            if($rowcount > 0)
            {                
                foreach ($results as $key => $value)
                {
                    $user_data['user_id'] = $value['id'];
                    $user_data['user_log'] = $value['log'];
                    $user_data['user_mail'] = $value['mail'];
                    $user_data['user_pw'] = $value['pw'];
                    $users[] = $user_data;
                }             
                return $users; //false;
            }else{
               //echo 4;
               $user_data['users_found'] = false;
               $users[] = $user_data;
               return $users; //false;
            }
        }else{
           //echo 5;
           $user_data['users_found'] = false;
           $users[] = $user_data;
           return $users; //false;
        }
    }     
    public function isUserKnown($log,$pw,$origine,$ticket){     
        $user_data = array();
        $log = $this->my_addslashes($log);
        $pw  = $this->my_addslashes($pw);
        $origine  = $this->my_addslashes($origine);
        
        //we have to user's tables: quest/exam
        $tabe_users_selected = JsonModel::TABLE_USERS_ADMIN;
           
        $sql = "SELECT * FROM " . $tabe_users_selected . " WHERE log='$log' AND pw='$pw' ";
        
        $results  = mysqli_query($this->con,$sql);  
        //echo 1;
        if($results){
            //echo 2;
            $rowcount = mysqli_num_rows($results);
            if($rowcount > 0){
                foreach ($results as $key => $value)
                {
                    $user_data['user_id'] = $value['id'];
                    $user_data['user_log'] = $value['log'];
                    $user_data['user_mail'] = $value['mail'];
                }            
                //questionnaire edition
                if(JsonModel::ORIGIN_QUEST == $origine){ 
                   $user_data['user_identified'] = true;
                   return $user_data; //true;
                   
                //exam running 
                }else{
                   //test ticket constraint
                   $tick_result = $this->getTicketData($ticket);

                   //no ticket found
                   if(!count($tick_result) > 0){
                       $user_data['user_identified'] = false;
                       return $user_data; //false;                  
                   }
               
                   //this found ticket is linked with this user
                   if($this->isUserLinkToThisKey($value['id'],$tick_result['id_ticket'])){
                       $user_data['user_identified'] = true;
                       return $user_data; //true;
                   }else{
                       $user_data['user_identified'] = false;
                       return $user_data; //false;               
                   }                
               }
            }else{
               //echo 4;
               $user_data['user_identified'] = false;
               return $user_data; //false;
            }
        }else{
           //echo 5;
           $user_data['user_identified'] = false;
           return $user_data; //false;
        }
    }      
    public function getTableName($id_user,$table_name){
       return str_replace("ID_USER", $id_user, $table_name);
    }
    public function geTree($id_user ){        

        $sql = "SELECT * FROM " . 
                   $this->getTableName(
                      $id_user,
                      JsonModel::TABLE_QUESTIONNAIRE
                   )            .
                   " ORDER BY position";
                   
        $results["questionnaires"] = mysqli_query($this->con,$sql);       
        return $results;
    }         
    public function geTreeForReport($id_author,$id_questionnaire){        

        $sql = "SELECT * FROM " . 
                   $this->getTableName(
                      $id_author,
                      JsonModel::TABLE_QUESTIONNAIRE_EXAM
                   )            .
                   " WHERE id='$id_questionnaire' OR parent='$id_questionnaire' " .
                   " ORDER BY position ";
        //var_dump($sql);           
        $results["questionnaires"] = mysqli_query($this->con,$sql);       
        return $results;
    }   
    public function getPossibleAnswersForAQuestion($id_user,$id_question ){        
        //return $id_user;
        $sql = "SELECT * FROM " . 
        $this->getTableName(
          $id_user,
          JsonModel::TABLE_POSS_ANSWERS_EXAM
        )            .
        " WHERE id_question=$id_question ORDER BY sequence";
        //var_dump($sql);
        $results["possible_answers"] = mysqli_query($this->con,$sql);       
        return $results;
    }   
    public function getAuthorId($id_questionnaire){

        $sql = "SELECT * FROM   " . 
                  JsonModel::TABLE_KEY2QUESTIONNAIRES
                . 
                " where id_questionnaire=$id_questionnaire";

        $results = mysqli_query($this->con,$sql);  
        //var_dump($sql);
        foreach ($results as $key => $value)
        {
            return $value['id_author'];
        }       
        return "-1";
    }    
    public function getImages($id_user,$id_item ){        
        //return $id_user;
        $sql = "SELECT * FROM " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_IMAGES_EXAM
                )            . 
                " WHERE id_item=$id_item ORDER BY weight";   
        //var_dump($sql);
        return mysqli_query($this->con,$sql); 
    }     
    public function updateNodeQuestion(
            $id_user,$text,$intitule,$responsetype,$id_questionnaire,$good_response_txt,$good_response_date,$good_response_number){ //csi
        $id_user = $this->my_addslashes($id_user);
        $text = $this->my_addslashes($text);
        $intitule  = $this->my_addslashes($intitule);
        $responsetype  = $this->my_addslashes($responsetype);
        $id_questionnaire  = $this->my_addslashes($id_questionnaire);
        $good_response_txt  = $this->my_addslashes($good_response_txt);
        $good_response_date  = $this->my_addslashes($good_response_date);
        $good_response_number  = $this->my_addslashes($good_response_number);
        
        $sql = "UPDATE " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_QUESTIONNAIRE
                )            .
               " SET tree_label='$text', q_label='$intitule', q_resp_type='$responsetype', q_good_response_txt='$good_response_txt' " .
               " , q_good_response_date='$good_response_date' , q_good_response_number='$good_response_number'  " .
               " WHERE id=$id_questionnaire";
        //disp($sql,__FILE__);
        mysqli_query($this->con,$sql);
    }
    public function reorderDraggedQuestion(
        $position,$oldPosition,$parent,$oldParent,$idQuestion,$id_user){
        
        $parent = $this->my_addslashes($parent);
        $position = $this->my_addslashes($position);
        $idQuestion = $this->my_addslashes($idQuestion);
        $oldParent = $this->my_addslashes($oldParent);
        /**
         * case when parent old == current parent 
         * => means we work in the same questionnaire
         */
        if($parent == $oldParent){
            if($position != $oldPosition){
                //select all the questions      
                $sql = "SELECT id FROM " . 
                        $this->getTableName(
                          $id_user,
                          JsonModel::TABLE_QUESTIONNAIRE
                        )                .         
                        " WHERE parent='$parent' ORDER BY position";                        
                $results = mysqli_query($this->con,$sql);       
                $hasReachedThePosition = false;
                $originals = array();
                $finals = array();
                foreach ($results as $key => $value)
                {
                    $originals [] = $value['id'];
                }
                //var_dump($originals);
                //sort
                if($oldPosition<$position){ // 2 -> 4
                    $tmp = null;
                    $counter = 0;
                    foreach ($originals as $value){
                        if($counter == $oldPosition){
                            $tmp = $value;
                            $counter++;
                            continue;
                        }
                        if($counter == $position){
                            $finals [] = $value; 
                            $finals [] = $tmp;
                        }else{
                            $finals [] = $value;      
                        }
                        $counter++;
                    }     
                    //update
                    $counter = 0;
                    //var_dump($finals);
                    foreach ($finals as $value){
                        $sql = " UPDATE " . 
                                $this->getTableName(
                                    $id_user,
                                    JsonModel::TABLE_QUESTIONNAIRE
                                )            .
                               " SET position='$counter' " .        
                               " WHERE id='" . $value . "' ";
                        //var_dump($sql);
                        mysqli_query($this->con,$sql);    
                        $counter++;
                    }
                    //var_dump($finals);
                }
                if($position<$oldPosition){ // 4 -> 2
                    $counter = 0;
                    foreach ($originals as $value){
                        if($counter == $position){
                            $finals [$counter] = $originals[$oldPosition];
                            $finals [] = $value; 
                            $counter++;
                            continue;
                        }
                        if($counter == $oldPosition){
                            $counter++;
                            continue;
                        }
                        $finals [] = $value; 
                        $counter++;
                    } 
                    //update
                    $counter = 0;
                    foreach ($finals as $value){
                        $sql =  " UPDATE " . 
                                $this->getTableName(
                                  $id_user,
                                  JsonModel::TABLE_QUESTIONNAIRE
                                )                . 
                                " SET position='$counter' " .        
                                " WHERE id='" . $value . "' ";
                        //var_dump($sql);
                        mysqli_query($this->con,$sql);    
                        $counter++;
                    }                   
                    //var_dump($finals);
                }       
                //update in db
            }
        }
        /**
         * case when parent old != current parent
         */        
        else{
            /**
             * part 1: insert dropped element inside his new questionnaire
             * 
             */
            //var_dump( " part 2 " );
            //select all the questions from the new questionnaire     
            $sql = "SELECT id FROM " . 
                    $this->getTableName(
                        $id_user,
                        JsonModel::TABLE_QUESTIONNAIRE
                    )            . 
                    "  WHERE parent='$parent' ORDER BY position";
            $results = mysqli_query($this->con,$sql);       
            $originals = array();
            $finals = array();
            $counter = 0;
            foreach ($results as $value)
            {
                $originals [$counter] = $value['id'];
                $counter++;
            }      
            //var_dump($originals);
            //return;
            $counter = 0;
            //echo " originals: position =  $position ";
            $hasBeePutInIt = false;
            foreach ($originals as $value)
            {
                if($counter == $position){
                    $hasBeePutInIt = true;
                    //record the id
                    $finals[$counter] = $idQuestion;
                    //update the question with the new parent id
                    $sql = " UPDATE " . JsonModel::TABLE_QUESTIONNAIRE .
                           " SET parent='$parent' " .        
                           " WHERE id='" . $idQuestion . "' ";
                    mysqli_query($this->con,$sql);     
                    $counter++;
                    $finals [$counter] = $value; 
                    $counter++;
                }else{
                    $finals [$counter] = $value; 
                    $counter++;
                }                  
                //$finals [$counter] = $value; 
                //$counter++;
            }   
            if(!$hasBeePutInIt){
                $finals[$counter] = $idQuestion;
                //update the question with the new parent id
                $sql = " UPDATE " . JsonModel::TABLE_QUESTIONNAIRE .
                       " SET parent='$parent' " .        
                       " WHERE id='" . $idQuestion . "' ";      
                mysqli_query($this->con,$sql); 
            }
            //echo " finals ";
            //update target
            $counter = 0;
            foreach ($finals as $value){
                $sql = " UPDATE " . JsonModel::TABLE_QUESTIONNAIRE .
                       " SET position='$counter' " .        
                       " WHERE id='" . $value . "' ";
                //var_dump($sql);
                mysqli_query($this->con,$sql);    
                $counter++;
            } 
            /**
             * part 2: update the order inside the old questionnaire
             * 
             */            
            //select all the questions from the new questionnaire     
            $sql = "SELECT id FROM " . 
                        $this->getTableName(
                          $id_user,
                          JsonModel::TABLE_QUESTIONNAIRE
                        )            . "
                        WHERE parent='$oldParent' ORDER BY position";
            $results = mysqli_query($this->con,$sql);       
            $originals = array();
            $finals = array();
            $counter = 0;
            foreach ($results as $value)
            {
                $originals [$counter] = $value['id'];
                $counter++;
            } 
            //update target
            $counter = 0;
            foreach ($originals as $value){
                $sql = " UPDATE " . 
                        $this->getTableName(
                          $id_user,
                          JsonModel::TABLE_QUESTIONNAIRE
                        )                . 
                       " SET position='$counter' " .        
                       " WHERE id='" . $value . "' ";
                //var_dump($sql);
                mysqli_query($this->con,$sql);    
                $counter++;
            }             
        }
    }
    public function ResetPositions($parent){
        $sql = "SELECT id FROM " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_QUESTIONNAIRE
                )            . 
                " WHERE parent='$parent' ORDER BY position";
        $results = mysqli_query($this->con,$sql);       
        $originals = array();        
        if (!$results) { 
            $counter = 0;
            foreach ($results as $value)
            {
                $originals [$counter] = $value['id'];
                $counter++;
            }   
            //update target
            $counter = 0;
            foreach ($originals as $value){
                $sql =  " UPDATE " . 
                        $this->getTableName(
                          $id_user,
                          JsonModel::TABLE_QUESTIONNAIRE
                        )                . 
                        " SET position='$counter' " .        
                        " WHERE id='" . $value . "' ";
                //var_dump($sql);
                mysqli_query($this->con,$sql);    
                $counter++;
            }
        }    
    }
    public function createNodeQuestion($id_user,$text,$intitule,$responsetype,$parent,$node_type){ //csi
        $id_user = $this->my_addslashes($id_user);
        $text = $this->my_addslashes($text);
        $intitule  = $this->my_addslashes($intitule);
        $responsetype  = $this->my_addslashes($responsetype);
        $parent  = $this->my_addslashes($parent);
        $node_type  = $this->my_addslashes($node_type);
        $sql = " SELECT MAX(position) as max FROM " . 
                        $this->getTableName(
                          $id_user,
                          JsonModel::TABLE_QUESTIONNAIRE
                        )                .  
               " WHERE parent='$parent' ";
        $res = mysqli_query($this->con,$sql);
        $max = 0;
        while($row = $res->fetch_assoc()) {
            $max = $row['max'];
        }
        $max++;
        $sql = " INSERT INTO " . 
                        $this->getTableName(
                          $id_user,
                          JsonModel::TABLE_QUESTIONNAIRE
                        )                .  
               " (tree_label,parent,node_type,q_label,q_resp_type,position) " .
               "  VALUES('$text','$parent','$node_type','$intitule','$responsetype','$max')";
        //disp($sql,__FILE__);
        mysqli_query($this->con,$sql);
        $id = mysqli_insert_id($this->con);
        return $id;
    }    
    public function insertImage($id_user,$type,$id_item,$fileNameSafe){ //789789
    
        $id_user = $this->my_addslashes($id_user);
        $type    = $this->my_addslashes($type);
        $id_item = $this->my_addslashes($id_item);
        $fileNameSafe = $this->my_addslashes($fileNameSafe);
        
        $sql =  " INSERT INTO " . 
                    $this->getTableName(
                      $id_user,
                      JsonModel::TABLE_IMAGES
                    )            . 
                " (item_type,id_item,item_label,image_name,weight) " .
                " VALUES('$type', '$id_item','$fileNameSafe','$fileNameSafe','1')";
                
        mysqli_query($this->con,$sql);
        $id = mysqli_insert_id($this->con);
        return $id;       
    }     
    public function createNodeQuestionnaire(
            $id_user,$text,$etat,$sauvegarde,$examen,$date_start,$date_stop,$comment,$autocorrection,$node_type){ //csi
        
        $id_user = $this->my_addslashes($id_user);
        $text = $this->my_addslashes($text);
        $etat  = $this->my_addslashes($etat);
        $sauvegarde  = $this->my_addslashes($sauvegarde);
        $examen  = $this->my_addslashes($examen);
        $date_start  = $this->my_addslashes($date_start);//transform it
        $date_stop  = $this->my_addslashes($date_stop);
        $comment  = $this->my_addslashes($comment);
        $autocorrection  = $this->my_addslashes($autocorrection);   
        $node_type  = $this->my_addslashes($node_type); 
        $position = -1;
        
        $sql =  "INSERT INTO " . 
                $this->getTableName(
                    $id_user,
                    JsonModel::TABLE_QUESTIONNAIRE
                )                . 
                "(tree_label,parent,qr_state,qr_save,qr_random,qr_date_start,qr_date_stop,qr_autocorrection,qr_comment,node_type,position) " .
                " VALUES('$text', '#','$etat','$sauvegarde','$examen','$date_start','$date_stop','$autocorrection','$comment','$node_type','$position')";
        mysqli_query($this->con,$sql);
        $id = mysqli_insert_id($this->con);
        return $id;
    }     
    public function createNewPossibleAnswer(
            $id_user,
            $answ_type,
            $content,
            $is_good_response,
            $id_question,
            $sequence){ //csi
        
        $id_user = $this->my_addslashes($id_user);
        $answ_type = $this->my_addslashes($answ_type);
        $content  = $this->my_addslashes($content);
        $is_good_response  = $this->my_addslashes($is_good_response);
        $id_question  = $this->my_addslashes($id_question);
        
        $sql =  "INSERT INTO " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_POSS_ANSWERS
                )            . 
                "(answ_type,content,is_good_response,id_question,sequence) " .
                " VALUES('$answ_type','$content','$is_good_response','$id_question','$sequence')";
        //disp($sql,__FILE__); die(__FILE__);
        mysqli_query($this->con,$sql);
        $id = mysqli_insert_id($this->con);
        return $id;
    }     
    public function getImagPath($id_image,$id_user){
       $dir_image = JsonModel::DIR_IMAGES; // '/upload/'
       $file_name = $this->getFileName($id_image,$id_user);
       //$path      = '../..' . $dir_image .  $id_user . '/' . $file_name;
       $path      = '..' . $dir_image .  $id_user . '/' . $file_name;
       return $path;
    }
    public function getFileName($id_image,$id_user){
        //$sql = "SELECT * FROM " . JsonModel::TABLE_IMAGES . " WHERE id='$id_image'  ";
        $sql = "SELECT * FROM " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_IMAGES
                ) .
                " WHERE id='$id_image'  ";      
        
        //var_dump($sql); die(__FILE__);
        $res = mysqli_query($this->con,$sql);
        $image_name = "";
        foreach ($res as $key => $value)
        {
            $image_name = $value['image_name'];
            break;
        }        
        return $image_name;
    }    
    public function deleteFile($user_id,$file_id){//m5
        //var_dump("1");
        $user_id = $this->my_addslashes($user_id);
        $file_id = $this->my_addslashes($file_id);
        
        $path_image = $this->getImagPath($file_id,$user_id);
        
        //delete file
        if(file_exists ($path_image )){
           //dir
           unlink($path_image);
           //db
           //$sql="DELETE FROM " . JsonModel::TABLE_IMAGES .  " WHERE id=$file_id " ;
           $sql= "DELETE FROM " . 
                  $this->getTableName(
                  $user_id,
                  JsonModel::TABLE_IMAGES
                  ) .
                 " WHERE id=$file_id " ;
           //var_dump($sql);
           mysqli_query($this->con,$sql); 
           //return "ok";
        }else{
           //return $path_image . " " . getcwd(); //"ko";
        }
        //return $path_image;
        //delete node

        //find url and name
        //delete on directory
        //delete from db
        //...
        //$sql="DELETE FROM " . JsonModel::TABLE_IMAGES .  " WHERE id=$file_id " ;
        //mysqli_query($this->con,$sql);    
    }      
    public function deleteNode($id_user,$id_to_delete,$node_type,$node_parent){
        //delete node
        $id_to_delete = $this->my_addslashes($id_to_delete);
        $sql=   " DELETE FROM " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_QUESTIONNAIRE
                )            .
                " WHERE id=$id_to_delete OR parent=$id_to_delete " ;
        mysqli_query($this->con,$sql);    
        //delete possible answers 
        $this->deletePossibleAnswers($id_user,$id_to_delete);  
        //reorderpositions if we've deleted a question
        if($node_type == 'QUESTION'){
            $this->ResetPositions($node_parent);
        }
    }    
    public function deletePossibleAnswers($id_user,$id_to_delete){      
        //delete possible answers
        $id_to_delete = $this->my_addslashes($id_to_delete);
        $sql=   " DELETE FROM " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_POSS_ANSWERS
                )            .
                " WHERE id_question=$id_to_delete " ;
        mysqli_query($this->con,$sql);        
    }      
    public function deleteExclPossibleAnswers($id_user,$id_to_delete){      
        //delete possible answers
        $id_to_delete = $this->my_addslashes($id_to_delete);
        $sql =  " DELETE FROM " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_POSS_ANSWERS
                )            .  
                " WHERE id_question=$id_to_delete AND answ_type=4 " ;
        mysqli_query($this->con,$sql);        
    }      
    public function deleteInclPossibleAnswers($id_user,$id_to_delete){      
        //delete possible answers
        $id_to_delete = $this->my_addslashes($id_to_delete);
        $sql =  " DELETE FROM " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_POSS_ANSWERS
                )            . 
                " WHERE id_question=$id_to_delete AND answ_type=5 " ;
        mysqli_query($this->con,$sql);        
    }      
    public function updateNodeQuestionnaire(
            $id_user,$text,$etat,$sauvegarde,$examen,$date_start,$date_stop,$comment,$autocorrection,$id_questionnaire){ //csi
        
        $id_user = $this->my_addslashes($id_user);
        $text = $this->my_addslashes($text);
        $etat  = $this->my_addslashes($etat);
        $sauvegarde  = $this->my_addslashes($sauvegarde);
        $examen  = $this->my_addslashes($examen);
        $date_start  = $this->my_addslashes($date_start);//transform it
        $date_stop  = $this->my_addslashes($date_stop);
        $comment  = $this->my_addslashes($comment);
        $autocorrection  = $this->my_addslashes($autocorrection);
        $id_questionnaire  = $this->my_addslashes($id_questionnaire);
        
        /*
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `tree_label` text,
            `qr_state` int(3) DEFAULT NULL,
            `qr_save` int(3) DEFAULT NULL,
            `qr_random` int(3) DEFAULT NULL,
            `qr_date_start` date DEFAULT NULL,
            `qr_date_stop` date DEFAULT NULL,
            `qr_autocorrection` tinyint(1) DEFAULT NULL,
            `qr_comment` text,
            `parent` varchar(30) NOT NULL,
            `node_type` varchar(40) NOT NULL,
            `q_label` text NOT NULL,
            `q_resp_type` int(4) NOT NULL,*/       
        
        $sql = "UPDATE " 
                                 . 
                $this->getTableName(
                    $id_user,
                    JsonModel::TABLE_QUESTIONNAIRE
                )                . 
           " SET tree_label='$text', qr_state='$etat', qr_save='$sauvegarde' ," .
           "     qr_random='$examen', qr_date_start='$date_start', qr_date_stop='$date_stop' ," .
           "     qr_autocorrection='$autocorrection', qr_comment='$comment'" .          
           " WHERE id=$id_questionnaire";
        //disp($sql,__FILE__);
        mysqli_query($this->con,$sql);
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

