<?php
class JsonModel extends Db {
    const REPLACE_KEY_ID_USER = "ID_USER";
    
    //tables pre creator id
    //for the mock
    const TABLE_QUESTIONNAIRE        = 'q_ID_USER_questionnaire';
    const TABLE_POSS_ANSWERS         = 'q_ID_USER_poss_answers';
    const TABLE_RESPONSES            = 'q_ID_USER_responses';
    const TABLE_IMAGES               = 'q_ID_USER_images';   
    
    //fpr the exam
    const TABLE_QUESTIONNAIRE_EXAM        = 'q_ID_USER_questionnaire_exam';
    const TABLE_POSS_ANSWERS_EXAM         = 'q_ID_USER_poss_answers_exam';
    const TABLE_RESPONSES_EXAM            = 'q_ID_USER_responses_exam';
    const TABLE_IMAGES_EXAM               = 'q_ID_USER_images_exam';  
    
    //shared tables => no id in it
    const TABLE_KEYS                 = 'q_keys';  
    const TABLE_KEY2QUESTIONNAIRES   = 'q_key2questionnaire';
    const TABLE_USERS_EXAMENS        = 'q_users_examens';
    const TABLE_USERS_QUESTIONNAIRES = 'q_users_questionnaires';
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
    public function test(){return "ok test 3";}
    public function my_addslashes($val){
        if(!get_magic_quotes_gpc())
            $val = addslashes($val);
        return $val;
    }
    public function getImages($id_user,$id_item,$isMocker=true){        
        //return $id_user;
        $sql = "SELECT * FROM " . 
                $this->getTableName(
                  $id_user,
                  ($isMocker)?
                  JsonModel::TABLE_IMAGES:
                  JsonModel::TABLE_IMAGES_EXAM
                )
                . " WHERE id_item=$id_item ORDER BY weight";   
        //var_dump($sql);
        return mysqli_query($this->con,$sql); 
    }     
    public function insertUser($fname, $name, $mail){
        $fname = $this->my_addslashes($fname);
        $name  = $this->my_addslashes($name);
        $mail  = $this->my_addslashes($mail);
        $sql = "INSERT INTO " . Crud::TABLE_PROVIDER . " VALUES(NULL,'$fname','$name','$mail')";
        mysqli_query($this->con,$sql);
    }
    public function saveResponse(
        $id_user_answering,
        $responsetype,  
        $id_question, 
        $good_response_txt,
        $good_response_date, 
        $good_response_number,
        $qcm_exclu, //already cleaned -> "5;6;8"
        $qcm_inclu, //already cleaned -> "5;6;8"
        $id_questionnaire,
        $idAuthor,
        $isMocker=true
    ){
        $id_user_answering = $this->my_addslashes(trim($id_user_answering));
        $resp_type = $this->my_addslashes(trim($responsetype));  
        $id_question = $this->my_addslashes(trim($id_question));   
        $good_response_txt = $this->my_addslashes(trim($good_response_txt));  
        $good_response_date = $this->my_addslashes(trim($good_response_date));  
        $good_response_number = $this->my_addslashes(trim($good_response_number));  
        //$qcm_exclu = $this->my_addslashes(trim($qcm_exclu));  
        //$qcm_inclu = $this->my_addslashes(trim($qcm_inclu));  
        $id_questionnaire = $this->my_addslashes(trim($id_questionnaire));  
        /**
         * tester QCM
         */
        if($resp_type == JsonModel::RESPONSE_TYPE_EXCLU){
          $rcm = $qcm_exclu;
        }else if($resp_type == JsonModel::RESPONSE_TYPE_INCLU){
          $rcm = $qcm_inclu;
        }else{
          $rcm = "";
        }
        /**
         * tester resp text
         */       
        if($resp_type == JsonModel::RESPONSE_TYPE_TEXT){
          $good_response_txt = $good_response_txt;
        }else if($resp_type == JsonModel::RESPONSE_TYPE_NUMBER){
          $good_response_txt = $good_response_number;
        }else{
          $good_response_txt = "";
        }
        
       //inject the qcm response ids...
       $this->deleteResponse(
                   $id_user_answering,
                   $id_question,
                   $id_questionnaire,
                   $idAuthor,
                   $isMocker);
       
       return $this->insertAnswers(
              $id_question,
              $id_questionnaire,
              $resp_type,
              $rcm,
              $good_response_txt,
              $id_user_answering,
              $good_response_date,
              $idAuthor,
              $isMocker);

    }
    public function getExistingAnswer
       (
       $id_questionnaire,
       $id_question,
       $id_answering_user,
       $idAuthor,
       $isMocker=true){
       
        $sql = "  SELECT * FROM " 
                  //. JsonModel::TABLE_RESPONSES . " WHERE " .
                 . 
                 $this->getTableName(
                    $idAuthor,
                    ($isMocker)?
                       JsonModel::TABLE_RESPONSES:
                       JsonModel::TABLE_RESPONSES_EXAM
                 )
                 .        
               "  WHERE id_questionnaire=$id_questionnaire AND " . 
               "  id_question=$id_question           AND " . 
               "  id_user_answering=$id_answering_user   ";
        //var_dump("getExistingAnswer"); 
       // var_dump($sql);     
        $res = mysqli_query($this->con,$sql);
        return $res;       
    }  
    public function insertAnswers(
      $id_question,
      $id_questionnaire,
      $resp_type,
      $rcm,
      $resp_text,
      $id_user_answering,
      $resp_datetime,
      $idAuthor,
      $isMocker=true
     ){
  
        $sql = " INSERT INTO "
        //. JsonModel::TABLE_RESPONSES . 
                . 
                $this->getTableName(
                  $idAuthor,
                  ($isMocker)?
                  JsonModel::TABLE_RESPONSES:
                  JsonModel::TABLE_RESPONSES_EXAM                  
                  //JsonModel::TABLE_RESPONSES
                )
                .        
               " (id_question,id_questionnaire,resp_type,rcm,resp_text,id_user_answering,resp_datetime) " .
               " VALUES('$id_question','$id_questionnaire','$resp_type','$rcm','$resp_text','$id_user_answering','$resp_datetime')";

        mysqli_query($this->con,$sql);
        $id = mysqli_insert_id($this->con);   
        return $id;
    }
    public function deleteResponse(
    $id_user_answering,$id_question,$id_questionnaire,$idAuthor,$isMocker){
        $sql=" DELETE FROM " 
        //. JsonModel::TABLE_RESPONSES   . 
            . 
            $this->getTableName(
              $idAuthor,
              ($isMocker)?
                  JsonModel::TABLE_RESPONSES:
                  JsonModel::TABLE_RESPONSES_EXAM 
            )
            .
             " WHERE id_user_answering=$id_user_answering " . 
             " AND id_question=$id_question AND id_questionnaire=$id_questionnaire ";
        //var_dump($sql);
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
    public function getTableName($id_user,$table_name){
       return str_replace(JsonModel::REPLACE_KEY_ID_USER, $id_user, $table_name);
    }    
    public function getTicketData($ticket){
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
    } // TABLE_KEY2QUESTIONNAIRES
    public function getTicketedQuestionnaires($ticket_id,$isMocker=true){
    
        $ticket_id = $this->my_addslashes($ticket_id);
        $sql = "SELECT * FROM   " . JsonModel::TABLE_KEY2QUESTIONNAIRES .
               " WHERE id_ticket='$ticket_id' ";
               
        //var_dump($sql); die(__FILE__);
        $results = mysqli_query($this->con,$sql);     
        $questIds = array();
        foreach ($results as $key => $value){
            $questionnairesIds[] = $value['id_questionnaire'];
        }   
        return $questionnairesIds;
    }     
    public function geTree(
        $id_user,    //<--- replace by ticket + current user id
        $idQuestionnaire="",  //<--- replace by ticket + current user id
        $ticket,
        $isMocker=true
    ){  
        //var_dump("oups in geTree---------->");
        if($ticket == null){
           return null;
        }  
        $values = $this->getTicketData($ticket);
        //we need a ticket
        if(count($values) == 0){
           return null;
        }       
        $questionnairesIds = 
          $this->getTicketedQuestionnaires(
          $values['id_ticket'],
          $isMocker);
        // -> add: filter on user allowed to run those selected questionnaires

        

        //switch the ids...
        //modify the where close...
        
        $questionnairesIdsSwitched = "";
        if($isMocker){
            //leave the code as it is
        }else{
        
            //switch the ids
            
            foreach($questionnairesIds as $key=>$value){
                $questionnairesIdsSwitched[] = 
                   $this->getQuestionnaireIdExamCorr($id_user, $value);
            }      
            //var_dump($questionnairesIds);
            //var_dump($questionnairesIdsSwitched);
            $questionnairesIds = null;
            $questionnairesIds = $questionnairesIdsSwitched;
            //var_dump($questionnairesIds);            
        }
        //var_dump("oups in geTree");
        $where_start = " WHERE id IN ( ";
        $where_stop  = " ) ";
        $where = "";
        if(count($questionnairesIds) > 0){
           $ids = implode(',', $questionnairesIds);
           $where = $where_start . $ids . $where_stop;
        }
        $id_author = $values['id_author'];
        

        $sql = "SELECT * FROM   " . 
                $this->getTableName(
                  $id_author,
                      //JsonModel::TABLE_QUESTIONNAIRE
                      ($isMocker)?
                        JsonModel::TABLE_QUESTIONNAIRE:
                        JsonModel::TABLE_QUESTIONNAIRE_EXAM                   
                   )
                .
                " $where  AND qr_state='2' ORDER BY position";
        //disp($sql,__FILE__);
        $results["questionnaires"] = mysqli_query($this->con,$sql);       
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
    public function getQuestionnaireTimestamp($id_user,$id_questionnaire){

        $sql = "SELECT insert_time FROM   " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_QUESTIONNAIRE
                )
                . 
                " WHERE  id='$id_questionnaire'";

        $results = mysqli_query($this->con,$sql);  
        //var_dump($sql);
        foreach ($results as $key => $value)
        {
            return $value['insert_time']; 
        }       
        return false;
    }      
    public function isQuestionnaireActivated(
       $id_user,$id_questionnaire,$sessionTimestamp){
       //var_dump($id_user,$id_questionnaire,$sessionTimestamp);
        $testTimestamp = "";
        if($sessionTimestamp != null){
           $testTimestamp = " AND insert_time='$sessionTimestamp' ";
        }

        $sql = "SELECT qr_state FROM   " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_QUESTIONNAIRE
                )
                . 
                " WHERE  qr_state='2' " . $testTimestamp;

        $results = mysqli_query($this->con,$sql);  
        //var_dump($sql);
        foreach ($results as $key => $value)
        {
            return true; 
        }       
        return false;
    }    
    public function geTreeAndQuestions(
    $id_user, 
    $idQuestionnaire="" ,
    $isAMocker=true
    ){  
        //var_dump($id_user, $idQuestionnaire, $isAMocker);
        
        //test if the root questionnaire is active
        $results = null;
        if(true){ //$this->isQuestionnaireActivated($id_user,$idQuestionnaire,null)){
            //$id_user =  $this-> getAuthorId($idQuestionnaire);
            $where = "";
            if(strlen($idQuestionnaire) > 0){
               if($isAMocker){
                 $where = " WHERE id='$idQuestionnaire' OR parent='$idQuestionnaire' "; 
               }else{//car copie de original -> exam
                  //$idCorr = $this->getQuestionnaireIdExamCorr($id_user, $idQuestionnaire);
                  $idCorr = $idQuestionnaire;
                  $where = " WHERE id='$idCorr' OR parent='$idCorr' "; 
               }
            }
            //disp($idQuestionnaire,__FILE__);
            //return $id_user;
            $sql = "SELECT * FROM   " . 
                    $this->getTableName(
                      $id_user,
                      ($isAMocker)?
                         JsonModel::TABLE_QUESTIONNAIRE:
                         JsonModel::TABLE_QUESTIONNAIRE_EXAM
                    )
                    . 
                    " $where ORDER BY position";
            //disp($sql,__FILE__);
            $results["questionnaires"] = mysqli_query($this->con,$sql);     
        }  
        return $results;
    }     
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
                
         $res = mysqli_query($this->con,$sql);  
         
         foreach ($res as $key => $value)
         {
            $idFound = $value['id'];
            break;
         }         
                    
         return $idFound;
    }
    public function getAuthorIdFromExamKey(
    $key){
    
        $idFound = "-1";
        $sql = "SELECT * FROM   "            . 
                JsonModel::TABLE_KEYS        . 
                " WHERE ticket='$key' ";
         //var_dump($sql);       
         $res = mysqli_query($this->con,$sql);  
         
         foreach ($res as $key => $value)
         {
            $idFound = 
               $value['id_users_questionnaires'];
            break;
         }         
                    
         return $idFound;
    }
    public function getQuestionnaireIdExamOriginal(
          $id_user, 
          $id
       ){
        $idFound = "-1";
        $sql = "SELECT * FROM   " . 
                $this->getTableName(
                    $id_user,
                    JsonModel::TABLE_QUESTIONNAIRE_EXAM
                )
                . 
                " WHERE id=$id ";
                
         $res = mysqli_query($this->con,$sql);  
         
         foreach ($res as $key => $value)
         {
            $idFound = $value['qr_orig'];
            break;
         }         
                    
         return $idFound;
    }    
    public function getPossibleAnswersForAQuestion(
    $id_user,
    $id_question,
    $isMocker=true ){ 
    
        //return $id_user;
        $sql = "SELECT * FROM " . 
                $this->getTableName(
                  $id_user,
                  ($isMocker)?
                  JsonModel::TABLE_POSS_ANSWERS:
                  JsonModel::TABLE_POSS_ANSWERS_EXAM
                )
                . " WHERE id_question=$id_question ORDER BY sequence";
        $results["possible_answers"] = mysqli_query($this->con,$sql);       
        return $results;
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
                )
                .
               " SET tree_label='$text', q_label='$intitule', q_resp_type='$responsetype', q_good_response_txt='$good_response_txt' " .
               " , q_good_response_date='$good_response_date' , q_good_response_number='$good_response_number'  " .
               " WHERE id=$id_questionnaire";
        //disp($sql,__FILE__);
        mysqli_query($this->con,$sql);
    }
    public function reorderDraggedQuestion($position,$oldPosition,$parent,$oldParent,$idQuestion){
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
                $sql = "SELECT id FROM   " . 
                        $this->getTableName(
                          $id_user,
                          JsonModel::TABLE_QUESTIONNAIRE
                        )
                        . 
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
                                    )
                                    .
                               " SET position='$counter' " .        
                               " WHERE id='" . $value . "' ";
                        //var_dump($sql);
                        mysqli_query($this->con,$sql);    
                        $counter++;
                    }
                    var_dump($finals);
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
                        $sql = " UPDATE " . 
                                    $this->getTableName(
                                      $id_user,
                                      JsonModel::TABLE_QUESTIONNAIRE
                                    )
                                    .
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
            $sql = "SELECT id FROM   " . 
                    $this->getTableName(
                      $id_user,
                      JsonModel::TABLE_QUESTIONNAIRE
                    )
                    .
                    " WHERE parent='$parent' ORDER BY position";
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
                    $sql = " UPDATE " . 
                            $this->getTableName(
                              $id_user,
                              JsonModel::TABLE_QUESTIONNAIRE
                            )
                            .
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
                $sql = " UPDATE " . 
                        $this->getTableName(
                          $id_user,
                          JsonModel::TABLE_QUESTIONNAIRE
                        )
                        .
                       " SET parent='$parent' " .        
                       " WHERE id='" . $idQuestion . "' ";      
                mysqli_query($this->con,$sql); 
            }
            //echo " finals ";
            //update target
            $counter = 0;
            foreach ($finals as $value){
                $sql = " UPDATE " . 
                        $this->getTableName(
                          $id_user,
                          JsonModel::TABLE_QUESTIONNAIRE
                        )
                        .
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
            $sql = "SELECT id FROM   " . 
                    $this->getTableName(
                      $id_user,
                      JsonModel::TABLE_QUESTIONNAIRE
                    )
                    .
                    " WHERE parent='$oldParent' ORDER BY position";
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
                        )
                        .
                       " SET position='$counter' " .        
                       " WHERE id='" . $value . "' ";
                //var_dump($sql);
                mysqli_query($this->con,$sql);    
                $counter++;
            }             
        }
    }
    public function ResetPositions($parent){
        $sql = "SELECT id FROM   " 
                . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_QUESTIONNAIRE
                )
                .
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
                        )
                        .
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
                )
                . 
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
                )
                .
               " (tree_label,parent,node_type,q_label,q_resp_type,position) " .
               "  VALUES('$text','$parent','$node_type','$intitule','$responsetype','$max')";
        //disp($sql,__FILE__);
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
        
        $sql =  "INSERT INTO " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_QUESTIONNAIRE
                )
                . 
                "(tree_label,parent,qr_state,qr_save,qr_random,qr_date_start,qr_date_stop,qr_autocorrection,qr_comment,node_type) " .
                " VALUES('$text', '#','$etat','$sauvegarde','$examen','$date_start','$date_stop','$autocorrection','$comment','$node_type')";
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
                  JsonModel::TABLE_QUESTIONNAIRE
                )
                . 
                "(answ_type,content,is_good_response,id_question,sequence) " .
                " VALUES('$answ_type','$content','$is_good_response','$id_question','$sequence')";
        //disp($sql,__FILE__); die(__FILE__);
        mysqli_query($this->con,$sql);
        $id = mysqli_insert_id($this->con);
        return $id;
    }     
    public function deleteNode($id_to_delete,$node_type,$node_parent){
        //delete node
        $id_to_delete = $this->my_addslashes($id_to_delete);
        $sql=" DELETE FROM " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_QUESTIONNAIRE
                )
                .  
                " WHERE id=$id_to_delete OR parent=$id_to_delete " ;
        mysqli_query($this->con,$sql);    
        //delete possible answers 
        $this->deletePossibleAnswers($id_to_delete);  
        //reorderpositions if we've deleted a question
        if($node_type == 'QUESTION'){
            $this->ResetPositions($node_parent);
        }
    }    
    public function deletePossibleAnswers($id_to_delete){      
        //delete possible answers
        $id_to_delete = $this->my_addslashes($id_to_delete);
        $sql="DELETE FROM " . JsonModel::TABLE_POSS_ANSWERS .  " WHERE id_question=$id_to_delete " ;
        mysqli_query($this->con,$sql);        
    }      
    public function deleteExclPossibleAnswers($id_to_delete){      
        //delete possible answers
        $id_to_delete = $this->my_addslashes($id_to_delete);
        $sql =  " DELETE FROM " . JsonModel::TABLE_POSS_ANSWERS .  
                " WHERE id_question=$id_to_delete AND answ_type=4 " ;
        mysqli_query($this->con,$sql);        
    }      
    public function deleteInclPossibleAnswers($id_to_delete){      
        //delete possible answers
        $id_to_delete = $this->my_addslashes($id_to_delete);
        $sql =  "DELETE FROM " . JsonModel::TABLE_POSS_ANSWERS .  
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
        
        $sql = "UPDATE " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_QUESTIONNAIRE
                )
                .
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

