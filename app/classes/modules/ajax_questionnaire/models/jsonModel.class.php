<?php
class User {
   public $id ="";
   public $log ="";
   public $mail ="";
}
class JsonModel extends Db {

    //type de responses
    const RESP_TEXT           = '1';
    const RESP_NUMBER         = '2';
    const RESP_DATE           = '3';
    const RESP_EXCLU          = '4';
    const RESP_INCLU          = '5';
    const RESP_CONDITIONNAL   = '6';
    
    //edition
    const TABLE_QUESTIONNAIRE = 'q_ID_USER_questionnaire';
    const TABLE_POSS_ANSWERS  = 'q_ID_USER_poss_answers';
    const TABLE_IMAGES        = 'q_ID_USER_images';
    const DIR_IMAGES          = '/upload/';
    //exam
    const TABLE_QUESTIONNAIRE_EXAM = 'q_ID_USER_questionnaire_exam';
    const TABLE_POSS_ANSWERS_EXAM  = 'q_ID_USER_poss_answers_exam';
    const TABLE_IMAGES_EXAM        = 'q_ID_USER_images_exam';
    const DIR_IMAGES_EXAM     = '/upload_exam/';
    //response
    const TABLE_RESPONSES     = 'q_ID_USER_responses';
    //...
    const ORIGIN_QUEST        = "QUESTIONNAIRE";
    const ORIGIN_EXAM         = "EXAMEN";
    
    //shared tables => no id in it
    const TABLE_USERS_QUEST          = 'q_users_questionnaires';
    const TABLE_USERS_EXAM           = 'q_users_examens';    
    const TABLE_KEYS                 = 'q_keys';  
    const TABLE_KEY2QUESTIONNAIRES   = 'q_key2questionnaire';
    const TABLE_USERS_EXAMEN2KEY     = 'q_users_examens2key';
    
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
        $sql = "SELECT * FROM " . JsonModel::TABLE_USERS_EXAM . " WHERE id='$id_user' ";
        
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

       $ticket_id = $this->getTicketIdFromQuestionnaireId(
          $id_questionnaire,$id_author);
       
       $sql = " SELECT * FROM " . JsonModel::TABLE_USERS_EXAMEN2KEY . 
              " WHERE id_ticket='$ticket_id'";   
       $results  = mysqli_query($this->con,$sql); 
       $users = array();
       $user = array();
       
       if($results){
            foreach ($results as $key => $value)
            {
                $user['user_id'] = $value['id_user_examens'];
                $retr_user = $this->getUser($user['user_id']);
                //var_dump($retr_user);
                $user['user_log']  = $retr_user[0]['user_log'];
                $user['user_mail'] = $retr_user[0]['user_mail'];
                $user['mocker']    = $retr_user[0]['mocker'];
                $users[] = $user;
            } 
       }   
       return $users;
    }   
    public function updateExamUser( //2222
        $user_id,
        $user_login,
        $user_mail,
        $questionnaire_pass,
        $mocker
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
       
       $sql =   " UPDATE " . JsonModel::TABLE_USERS_EXAM            .
                " SET log='$user_login', pw='$questionnaire_pass',mail='$user_mail', mocker='$mocker' " .
                " WHERE id='$user_id'";
                
       //var_dump($sql);
       mysqli_query($this->con,$sql);
       
       return $res;
    }    
    public function deleteExamUser( //2222
        $user_id,
        $user_login,
        $user_mail,
        $questionnaire_pass,
        $id_auth
                       )
    {
       $res ['error']   = '';
       
       // id user must exists
       if(strlen(trim($user_id)) == 0){
          $res ['error'] = "Unknown user";
          return $res;
       }       
       
       $sql = " DELETE FROM " . JsonModel::TABLE_USERS_EXAM . 
              " WHERE id='$user_id' ";  
       mysqli_query($this->con,$sql); 
       
       $sql = " DELETE FROM " . JsonModel::TABLE_USERS_EXAMEN2KEY . 
              " WHERE id_user_examens='$user_id' ";  
       mysqli_query($this->con,$sql);                 
                
       //delete responses csidel
       $sql = " DELETE FROM " 
       .
        $this->getTableName(
            $id_auth,
            JsonModel::TABLE_RESPONSES
        )  
       . 
       " WHERE id_user_answering='$user_id' ";  
       mysqli_query($this->con,$sql);    
       
       //var_dump("------------------------->");
       //var_dump($sql);
       
       //var_dump($sql);
       //mysqli_query($this->con,$sql);
       
       return $res;
    }     
    public function newExamUser( //2222
        $auth_id,
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
                
       $sql =   "  INSERT INTO " . JsonModel::TABLE_USERS_EXAM          .  
                "  (log,pw,mail,id_author) "                            .
                "  VALUES('$user_login','$questionnaire_pass','$user_mail',$auth_id)";
                
       mysqli_query($this->con,$sql);
       
       $res ['id'] = mysqli_insert_id($this->con);

       return $res;
    }        
    public function getTicketIdFromQuestionnaireId(
    $id_questionnaire,
    $id_author){
    
       //test gathering the icket and users
       $sql = " SELECT id_ticket FROM " . JsonModel::TABLE_KEY2QUESTIONNAIRES . 
              " WHERE id_questionnaire='$id_questionnaire' AND id_author='$id_author' ";
              
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
       $users, $id_questionnaire,$id_user
    )
    {
       /**
        *  get associated ticket id
        */    
       $id_ticket = $this->getTicketIdFromQuestionnaireId(
          $id_questionnaire,$id_user);

       //var_dump($users);
       //var_dump(" id_questionnaire " . $id_questionnaire);
       //var_dump(" id_user " . $id_user);
       //var_dump(" id_ticket " . $id_ticket);
       
       //if(count($users) == 0){
       //   return "No user assigned, please select user(s) first";
       //}           
       
       if($id_ticket == null){
          return null; //No ticket found, please create on ticket first";
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
    /**
     * 
     * delete q_51_images_examCacher
     * delete q_51_poss_answers_examCacher
     * delete q_51_questionnaire_exam -> parent == id old questionnaire
     * delete q_51_responses_examCacher
     * delete images
     * delete folder
     *
     */
    public function deleteOldCampaignStatus(
        $id_author,
        $old_campaign_id,
        $newStatus,
        $questionnaire_id
    ){  
    
       return;
       
       /**
        * actif status, seulement 1 à la fois pour 
        * un même questionnaire source
        */
       if($newStatus == "1"){
       
           //updater toutes les row -> as inactif
           $sql =   "  UPDATE " .
                       $this->getTableName(
                          $id_author,
                          JsonModel::TABLE_QUESTIONNAIRE_EXAM
                       )  .    
                    "  SET qr_running_status='2' " .
                    "  WHERE node_type='QUESTIONNAIRE' AND qr_orig='$questionnaire_id'";
           var_dump($sql);
           mysqli_query($this->con,$sql); 
       
           //updater LA row -> as actif
           //updater toutes les row -> as inactif
           $sql =   "  UPDATE " .
                       $this->getTableName(
                          $id_author,
                          JsonModel::TABLE_QUESTIONNAIRE_EXAM
                       )  .    
                    "  SET qr_running_status='$newStatus' " .
                    "  WHERE id='$old_campaign_id'";
           var_dump($sql);
           mysqli_query($this->con,$sql); 
       
       /**
        * inactif status, il est possible de n'avoir aucune 
        * campagne active pour un même questionnaire
        */
       }else if($newStatus == "2"){
       
           $sql =   "  UPDATE " .
                       $this->getTableName(
                          $id_author,
                          JsonModel::TABLE_QUESTIONNAIRE_EXAM
                       )  .    
                    "  SET qr_running_status='$newStatus', " .
                    "  WHERE id='$old_campaign_id'";
           //var_dump($sql);
           mysqli_query($this->con,$sql); 
           
       /**
        * unknown status
        */
       }else{
          return "";
       }
       /**
        * retourner la liste de toutes les campagnes updatees pour ce questionnaire pour le js
        */
        //...
        
       return ""; //array("e"=>"4");
    }      
    public function updateOldCampaignStatus(
        $id_author,
        $old_campaign_id,
        $newStatus,
        $questionnaire_id
    ){   

       /**
        * actif status, seulement 1 à la fois pour 
        * un même questionnaire source
        */
       if($newStatus == "1"){
       
           //updater toutes les row -> as inactif
           $sql =   "  UPDATE " .
                       $this->getTableName(
                          $id_author,
                          JsonModel::TABLE_QUESTIONNAIRE_EXAM
                       )  .    
                    "  SET qr_running_status='2' " .
                    "  WHERE node_type='QUESTIONNAIRE' AND qr_orig='$questionnaire_id'";
           var_dump("-------1-------updateOldCampaignStatus");                    
           var_dump($sql);
           mysqli_query($this->con,$sql); 
       
           //updater LA row -> as actif
           //updater toutes les row -> as inactif
           $sql =   "  UPDATE " .
                       $this->getTableName(
                          $id_author,
                          JsonModel::TABLE_QUESTIONNAIRE_EXAM
                       )  .    
                    "  SET qr_running_status='$newStatus' " .
                    "  WHERE id='$old_campaign_id'";
           var_dump($sql);
           mysqli_query($this->con,$sql); 
       
       /**
        * inactif status, il est possible de n'avoir aucune 
        * campagne active pour un même questionnaire
        */
       }else if($newStatus == "2"){
       
           $sql =   "  UPDATE " .
                       $this->getTableName(
                          $id_author,
                          JsonModel::TABLE_QUESTIONNAIRE_EXAM
                       )  .    
                    "  SET qr_running_status='$newStatus' " .
                    "  WHERE id='$old_campaign_id'";
           var_dump($sql);
           mysqli_query($this->con,$sql); 
           
       /**
        * unknown status
        */
       }else{
          return "";
       }
       /**
        * retourner la liste de toutes les campagnes updatees pour ce questionnaire pour le js
        */
        //...
        
       return ""; //array("e"=>"4");
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
              var_dump($code);
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
                         $new_id_ticket2questionnaire = $this->saveTicket2questionnaire($new_id,$questionnaire_id,$id_author);
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
    public function saveTicket2questionnaire($id_ticket,$id_questionnaire,$id_author){
        $sql = " INSERT INTO " . JsonModel::TABLE_KEY2QUESTIONNAIRES          .  
               " (id_questionnaire,id_ticket,id_author) " .
               "  VALUES('$id_questionnaire','$id_ticket','$id_author')";
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
    public function isUserKnown($log,$pw,$origine,$ticket){     
        $user_data = array();
        $log = $this->my_addslashes($log);
        $pw  = $this->my_addslashes($pw);
        $origine  = $this->my_addslashes($origine);
        
        //we have to user's tables: quest/exam
        $tabe_users_selected = (JsonModel::ORIGIN_QUEST == $origine)?
           JsonModel::TABLE_USERS_QUEST : JsonModel::TABLE_USERS_EXAM;
           
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
                    $user_data['mocker'] 
                    = (array_key_exists('mocker',$value))?$value['mocker']:"";
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
    public function getCampaigns($id_auth,$id_questionnaire ){        

        $sql = "SELECT * FROM " .
                   $this->getTableName(
                      $id_auth,
                      JsonModel::TABLE_QUESTIONNAIRE_EXAM
                   )            .
                   " WHERE qr_orig='$id_questionnaire' ";
                   
        return mysqli_query($this->con,$sql);       
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
    public function getPossibleAnswersForAQuestion($id_user,$id_question ){        
        //return $id_user;
        $sql = "SELECT * FROM " . 
        $this->getTableName(
          $id_user,
          JsonModel::TABLE_POSS_ANSWERS
        )            .
        " WHERE id_question=$id_question ORDER BY sequence";
        $results["possible_answers"] = mysqli_query($this->con,$sql);       
        return $results;
    }   
    public function getImages($id_user,$id_item ){        
        //return $id_user;
        $sql = "SELECT * FROM " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_IMAGES
                )            . 
                " WHERE id_item=$id_item ORDER BY weight";     
        return mysqli_query($this->con,$sql); 
    }     
    public function isQuestionnaireUpdatable($id_user,$id_question){
            //var_dump("xxxx");
            /**
             * get the questionnaire
             */
            $sql = "SELECT qr_state FROM " . 
                    $this->getTableName(
                        $id_user,
                        JsonModel::TABLE_QUESTIONNAIRE
                    )            . 
                    "  WHERE id='$id_question' ";
            $results = mysqli_query($this->con,$sql);       
            $qr_state = "";
            //var_dump($sql);
            foreach ($results as $value)
            {
                //var_dump("TUTU");
                $qr_state = $value['qr_state'];
                break;
            }  
            //var_dump($qr_state);
            if($qr_state == 1){
               return true; //editable
            }
            return false; //not editable
    }    
    public function isQuestionUpdatable($id_user,$id_question){
            /**
             * get the questionnaire id
             */
            $sql = "SELECT parent FROM " . 
                    $this->getTableName(
                        $id_user,
                        JsonModel::TABLE_QUESTIONNAIRE
                    )            . 
                    "  WHERE id='$id_question' ";
            $results = mysqli_query($this->con,$sql);       
            $id_parent = "-1";
            foreach ($results as $value)
            {
                $id_parent = $value['parent'];
                break;
            }     
            /**
             * get the questionnaire
             */
            $sql = "SELECT qr_state FROM " . 
                    $this->getTableName(
                        $id_user,
                        JsonModel::TABLE_QUESTIONNAIRE
                    )            . 
                    "  WHERE id='$id_parent' ";
            $results = mysqli_query($this->con,$sql);       
            $qr_state = "";
            foreach ($results as $value)
            {
                $qr_state = $value['qr_state'];
                break;
            }  
            if($qr_state == 1){
               return true; //editable
            }
            return false; //not editable
    }
    public function updateNodeQuestion(
        $id_user,$text,$intitule,
        $responsetype,$id_questionnaire,
        $good_response_txt,$good_response_date,
        $good_response_number,$points){ //csi
        $id_user = $this->my_addslashes($id_user);
        $text = $this->my_addslashes($text);
        $intitule  = $this->my_addslashes($intitule);
        $responsetype  = $this->my_addslashes($responsetype);
        $id_questionnaire  = $this->my_addslashes($id_questionnaire);
        $good_response_txt  = $this->my_addslashes($good_response_txt);
        $good_response_date  = $this->my_addslashes($good_response_date);
        $good_response_number  = $this->my_addslashes($good_response_number);
        $points  = $this->my_addslashes($points);
        
        $sql = "UPDATE " . 
                $this->getTableName(
                  $id_user,
                  JsonModel::TABLE_QUESTIONNAIRE
                )            .
               " SET tree_label='$text', q_label='$intitule', q_resp_type='$responsetype', q_good_response_txt='$good_response_txt' " .
               " , q_good_response_date='$good_response_date' , q_good_response_number='$good_response_number' , q_points='$points' " .
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
    private function getUserExamData($id_user){
        $sql = "SELECT * FROM "                 .                 
                    JsonModel::TABLE_USERS_EXAM .
                " WHERE id='$id_user'           ";                
        $results= mysqli_query($this->con,$sql);         
        if ($results) 
        { 
            foreach ($results as $value)
            {
               return $value;
            }
        }                
    }
    private function getQuestionnaireData($id_questionnaire,$author){
        $sql = "SELECT * FROM "                 .                 
                        $this->getTableName(
                          $author,
                          JsonModel::TABLE_QUESTIONNAIRE
                        )  .
                " WHERE id='$id_questionnaire'           ";                
        $results= mysqli_query($this->con,$sql);         
        if ($results) 
        { 
            foreach ($results as $value)
            {
               return $value;
            }
        }                
    }    
    private function getQuestionData($id_question,$author){
        $sql = "SELECT * FROM "                 .                 
                        $this->getTableName(
                          $author,
                          JsonModel::TABLE_QUESTIONNAIRE
                        )  .
                " WHERE id='$id_question'           ";                
        $results= mysqli_query($this->con,$sql);         
        if ($results) 
        { 
            foreach ($results as $value)
            {
               return $value;
            }
        }                
    }     
    public function ArchiveResponses($id_questionnaire,$author_id){
    
        //get id questionnaire
        $old_id_questionnaire = "";
        $sql = "SELECT * FROM " . 
                $this->getTableName(
                    $author_id,
                    JsonModel::TABLE_QUESTIONNAIRE
                )            .
                " WHERE id='$id_questionnaire' ";
        
        var_dump("-------------------- IN ArchiveResponses 1.0 x1");                
        var_dump($sql);
        $results= mysqli_query($this->con,$sql);    
        $id_new_questionnaire = "-1";
        if ($results) 
        { 
           foreach ($results as $value)//questionnaire
           {
              //insert the one questionnaire item inside the exam questionnaire
              $sql = " INSERT INTO " . 
                        $this->getTableName(
                        $author_id,
                        JsonModel::TABLE_QUESTIONNAIRE_EXAM
                        ).  
               " (" . 
               "  id".
               " ,tree_label".
               " ,qr_state" . 
               " ,qr_save" . 
               " ,qr_random" . 
               " ,qr_date_start" . 
               " ,qr_date_stop" .
               " ,qr_autocorrection" .
               " ,qr_comment" .
               " ,parent" .
               " ,node_type" .
               " ,q_label" .
               " ,q_resp_type" .
               " ,q_good_response_txt" .
               " ,q_good_response_date" .
               " ,q_good_response_number" .
               " ,q_is_chrono" .
               " ,q_seconds" .
               " ,q_points" .
               " ,position" .
               " ,qr_orig" .
               " ,qr_running_status" .               
               " ,insert_time" .
               ") " .
               "" . 
               "  VALUES " . 
               "('"     . 
                "null"                  . "','" . 
                $this->my_addslashes($value['tree_label'])    . "','" . 
                $this->my_addslashes($value['qr_state'])    . "','" . 
                $this->my_addslashes($value['qr_save'])    . "','" . 
                $this->my_addslashes($value['qr_random'])    . "','" . 
                $this->my_addslashes($value['qr_date_start'])    . "','" . 
                $this->my_addslashes($value['qr_date_stop'])    . "','" . 
                $this->my_addslashes($value['qr_autocorrection'])    . "','" . 
                $this->my_addslashes($value['qr_comment'])    . "','" . 
                $this->my_addslashes($value['parent'])    . "','" . 
                $this->my_addslashes($value['node_type'])    . "','" . 
                $this->my_addslashes($value['q_label'])    . "','" . 
                $this->my_addslashes($value['q_resp_type'])    . "','" . 
                $this->my_addslashes($value['q_good_response_txt'])    . "','" . 
                $this->my_addslashes($value['q_good_response_date'])    . "','" . 
                $this->my_addslashes($value['q_good_response_number'])    . "','" . 
                $this->my_addslashes($value['q_is_chrono'])    . "','" . 
                $this->my_addslashes($value['q_seconds'])    . "','" . 
                $this->my_addslashes($value['q_points'])     . "','" . 
                $this->my_addslashes($value['position'])     . "','" . 
                $this->my_addslashes($id_questionnaire)      . "','" . 
                ""                     . "','" . 
                $value['insert_time']    . //"','" .                         
               "')";
               //disp($sql,__FILE__);
               var_dump($sql);
               mysqli_query($this->con,$sql);
               $id_new_questionnaire = mysqli_insert_id($this->con);     
               //disp($sql,__FILE__);
               
              /*******************************************************************************
               * clone the image 4 questionnaire
              /*******************************************************************************/  
              $this->cloneImage($author_id,$value['id'],$id_new_questionnaire);               
           }
        }//un seul element
    
        //copier les questions
        $old_id_questionnaire = "";
        $sql = "SELECT * FROM " . 
                $this->getTableName(
                    $author_id,
                    JsonModel::TABLE_QUESTIONNAIRE
                )            .
                " WHERE parent='$id_questionnaire' ";
                   
        $results= mysqli_query($this->con,$sql);  
        
        var_dump("-------------------- IN ArchiveResponses 1.1");
        if ($results) 
        { 
           $questComter = 0;
           foreach ($results as $valueQuestion)//questions
           {
              var_dump("-------QUESTION ---------- IN ArchiveResponses 1.1.0");
              var_dump("-------QUESTION q_label ----1.0.0.1 " . $valueQuestion['q_label']);
              /*******************************************************************************
               * insert the questions
              /*******************************************************************************/
              $sql = " INSERT INTO " . 
                        $this->getTableName(
                        $author_id,
                        JsonModel::TABLE_QUESTIONNAIRE_EXAM
                        ).  
               " (" . 
               "  id".
               " ,tree_label".
               " ,qr_state" . 
               " ,qr_save" . 
               " ,qr_random" . 
               " ,qr_date_start" . 
               " ,qr_date_stop" .
               " ,qr_autocorrection" .
               " ,qr_comment" .
               " ,parent" .
               " ,node_type" .
               " ,q_label" .
               " ,q_resp_type" .
               " ,q_good_response_txt" .
               " ,q_good_response_date" .
               " ,q_good_response_number" .
               " ,q_is_chrono" .
               " ,q_seconds" .
               " ,q_points" .
               " ,position" .
               " ,insert_time" .
               ") " .
               "" . 
               "  VALUES " . 
               "('"     . 
                "null"                  . "','" . 
                $this->my_addslashes($valueQuestion['tree_label'])    . "','" . 
                $this->my_addslashes($valueQuestion['qr_state'])    . "','" . 
                $this->my_addslashes($valueQuestion['qr_save'])    . "','" . 
                $this->my_addslashes($valueQuestion['qr_random'])    . "','" . 
                $this->my_addslashes($valueQuestion['qr_date_start'])    . "','" . 
                $this->my_addslashes($valueQuestion['qr_date_stop'])    . "','" . 
                $this->my_addslashes($valueQuestion['qr_autocorrection'])    . "','" . 
                $this->my_addslashes($valueQuestion['qr_comment'])    . "','" . 
                $this->my_addslashes($id_new_questionnaire)   . "','" . //parent
                $this->my_addslashes($valueQuestion['node_type'])    . "','" . 
                $this->my_addslashes($valueQuestion['q_label'])    . "','" . 
                $this->my_addslashes($valueQuestion['q_resp_type'])    . "','" . 
                $this->my_addslashes($valueQuestion['q_good_response_txt'])    . "','" . 
                $this->my_addslashes($valueQuestion['q_good_response_date'])    . "','" . 
                $this->my_addslashes($valueQuestion['q_good_response_number'])    . "','" . 
                $this->my_addslashes($valueQuestion['q_is_chrono'])    . "','" . 
                $this->my_addslashes($valueQuestion['q_seconds'])    . "','" . 
                $this->my_addslashes($valueQuestion['q_points'])    . "','" . 
                $this->my_addslashes($valueQuestion['position'])    . "','" . 
                $this->my_addslashes($valueQuestion['insert_time'])    . //"','" .                         
               "')";
               //disp($sql,__FILE__);
               var_dump("-------QUESTION SQL ----1.0.0.2 ");
               var_dump($sql);
               mysqli_query($this->con,$sql);
               $id_new_question = mysqli_insert_id($this->con);    
               
               /**
                * process to check if poss answers to clone too,
                * must be of type: RESP_EXCLU or RESP_INCLU
                */
               if( ($valueQuestion['q_resp_type'] == JsonModel::RESP_EXCLU) || 
                   ($valueQuestion['q_resp_type'] == JsonModel::RESP_INCLU)
                 )
               {
                    //find the pos answer
                    $id_old_question = $valueQuestion['id'];
                    $sql = "SELECT * FROM " . 
                            $this->getTableName(
                                $author_id,
                                JsonModel::TABLE_POSS_ANSWERS
                            )            .
                            " WHERE id_question='$id_old_question' ";
                   
                    $resultPossAnswers= mysqli_query($this->con,$sql);  
                    if ($resultPossAnswers) 
                    { 
                       foreach ($resultPossAnswers as $valuePossAnsw)
                       {
                            var_dump("-------------------- IN ArchiveResponses 1.2 TABLE_POSS_ANSWERS_EXAM");
                            $answ_type        = $this->my_addslashes($valuePossAnsw['answ_type']);
                            $content          = $this->my_addslashes($valuePossAnsw['content']);
                            $is_good_response = $this->my_addslashes($valuePossAnsw['is_good_response']);
                            $id_question      = $this->my_addslashes($valuePossAnsw['id_question']);
                            //need to associate the new id with the poss answers
                            $id_question      = $this->my_addslashes($id_new_question);
                            $sequence         = $this->my_addslashes($valuePossAnsw['sequence']);
                            $points           = $this->my_addslashes($valuePossAnsw['q_points']);
                            
                            $sql =  "INSERT INTO " . 
                                    $this->getTableName(
                                      $author_id,
                                      JsonModel::TABLE_POSS_ANSWERS_EXAM
                                    )            . 
                                    "(answ_type,content,is_good_response,id_question,sequence,q_points) " .
                                    " VALUES('$answ_type','$content','$is_good_response','$id_question','$sequence',$points)";
                            //disp($sql,__FILE__); die(__FILE__);
                            mysqli_query($this->con,$sql);
                            $idNewPossAnsw = mysqli_insert_id($this->con);                       
                       }
                    }
               }
                 
              /*******************************************************************************
               * clone the image de question
              /*******************************************************************************/  
              $this->cloneImage($author_id,$valueQuestion['id'],$id_new_question);
              
           }
        }//un seul element
        
        return "-9";                
    }    
    private function cloneImage($id_author,$id_old_item,$id_new_item)
    {
       $sql = " SELECT * FROM " . 
                $this->getTableName(
                  $id_author,
                  JsonModel::TABLE_IMAGES
                )            . 
              " WHERE id_item='$id_old_item' ";
                
        $resultImages= mysqli_query($this->con,$sql); 
        
        if ($resultImages) 
        { 
            foreach ($resultImages as $resultImage)
            {
                //clone
                $id_author    = $this->my_addslashes($id_author);
                $type         = $resultImage['item_type'];
                $id_item      = $this->my_addslashes($id_new_item);
                $fileNameSafe = $resultImage['item_label'];
        
                //switch the id: 'question_174_77.png' -> 'question_88_77.png'
                $to_remove = "_" . $id_old_item . "_";
                $to_add    = "_exam_" . $id_new_item . "_";
                $fileNameSafeNew = str_replace(
                   $to_remove, 
                   $to_add,
                   $fileNameSafe
                );

                $sql =  " INSERT INTO " . 
                            $this->getTableName(
                              $id_author,
                              JsonModel::TABLE_IMAGES_EXAM
                            )            . 
                        " (item_type,id_item,item_label,image_name,weight) " .
                        " VALUES('$type', '$id_item','$fileNameSafeNew','$fileNameSafeNew','1')";        
                        
                mysqli_query($this->con,$sql);
                $id = mysqli_insert_id($this->con);        
                
                //copy file
                $this->copyFile($id_author,$fileNameSafe,$fileNameSafeNew);
            }
        }                
    }
    private function copyFile($id_auth,$old_file_name,$new_file_name){
        $sourcePath = "../upload/" . $id_auth . "/".$old_file_name;  
		$targetPath = "../upload/" . $id_auth . "/".$new_file_name;  // Target path where file is to be stored                                 	
        if (file_exists($sourcePath)) {
            copy($sourcePath,$targetPath) ; //copy file
	    }         	
    }
    public function ResetPositions($parent,$id_user){
    
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
    public function createNodeQuestion(
       $id_user,
       $text,
       $intitule,
       $responsetype,
       $parent,
       $node_type
       ){ //csi
       
        $id_user = $this->my_addslashes($id_user);
        $text = $this->my_addslashes($text);
        $intitule  = $this->my_addslashes($intitule);
        $responsetype  = $this->my_addslashes($responsetype);
        $parent  = $this->my_addslashes($parent);
        $node_type  = $this->my_addslashes($node_type);
        
        if($node_type == "QUESTION"){
           //var_dump("IN 1");
           if(!$this->isQuestionnaireUpdatable($id_user,$parent)){
	          return "-1";
           }
        }
        //var_dump("IN 4");
        
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
         
        //var_dump("IN insertImage");
        //var_dump($sql);
        
        mysqli_query($this->con,$sql);
        $id = mysqli_insert_id($this->con);
        return $id;       
    }     
    public function createNodeQuestionnaire(
            $id_user,
            $text,
            $etat,
            $sauvegarde,
            $examen,
            $date_start,
            $date_stop
            ,$comment,
            $autocorrection,
            $node_type){ //csi
            
        //test if the questionnaire (if create question) est passif
        //if($node_type == "QUESTION"){
        //   if(!isQuestionUpdatable($id_user,$id_question)){
        //      return "-1";
        //   }
        //}
        
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
            $sequence,
            $points){ //csi

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
                "(answ_type,content,is_good_response,id_question,sequence,q_points) " .
                " VALUES('$answ_type','$content','$is_good_response','$id_question','$sequence','$points')";
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

        //test updatability
        if($node_type == 'QUESTION'){
           if(!$this->isQuestionUpdatable($id_user,$id_to_delete)){
	          return "-1";
           }        
        }
        //var_dump("--------in QUESTIONNAIRE 1------>  ");
        
        //test updatability
        
        if($node_type == 'QUESTIONNAIRE'){ 
           //var_dump("--------in QUESTIONNAIRE 2------>  ");
           if(!$this->isQuestionnaireUpdatable($id_user,$id_to_delete)){
           //var_dump("--------in QUESTIONNAIRE 3------>  ");
	          return "-1";
           }          
        }
        
        //return "333";
        
        //reorderpositions if we've deleted a question
        if($node_type == 'QUESTION'){
            //var_dump("--------in QUESTION------>  ");
            $this->ResetPositions($node_parent,$id_user);
            $this->deletePossibleAnswers($id_user,$id_to_delete);
            //$this->deleteQuestion($id_to_delete,$id_user);
        }
        /**
         * supprimer les associations au ticket
         * + 
         * le ticket
         * +
         * association user ticket
         */
        if($node_type == 'QUESTIONNAIRE'){
          //var_dump("--------in QUESTIONNAIRE------>  ");        
          /**
           * extraire les questions liées à ce questionnaire
           */
           //find all questions linked to this  questionnaire
            $sql = "SELECT * FROM " . 
                    $this->getTableName(
                      $id_user,
                      JsonModel::TABLE_QUESTIONNAIRE
                    ) .
                    " WHERE node_type='QUESTION' AND parent='$id_to_delete'  ";      
        
            //var_dump($sql); die(__FILE__);
            $res = mysqli_query($this->con,$sql);
            $image_name = "";
            //supprimer ces questions
            foreach ($res as $key => $value)
            {
                var_dump("--------in loop id question to delete ------>  " . $value['id']);
                $this->deleteQuestion($value['id'],$id_user); 
                $this->deletePossibleAnswers($id_user,$value['id']);
            }  
            
            //delete the questionnaire!!!!!!!!!!! here
            //dont forget
            //!!!!!!!
            $sql=   " DELETE FROM " . 
                      $this->getTableName(
                      $id_user,
                      JsonModel::TABLE_QUESTIONNAIRE
                    )            .
                    " WHERE node_type='QUESTIONNAIRE' AND id=$id_to_delete " ;
            mysqli_query($this->con,$sql);        
            
            //on recupère avant de delete from q_key2questionnaire le id_ticket.
            $sql = "SELECT * FROM " . 
                      JsonModel::TABLE_KEY2QUESTIONNAIRES
                     .
                    " WHERE id_questionnaire='$id_to_delete'  ";      
        
            //var_dump($sql); die(__FILE__);
            $res = mysqli_query($this->con,$sql);
            $id_tiket_to_delete = "";
            //supprimer ces questions
            foreach ($res as $key => $value)
            {
                $id_tiket_to_delete = $value['id_ticket'];
            }              
            //on delete l entree
            $sql=   " DELETE FROM " . 
                    JsonModel::TABLE_KEY2QUESTIONNAIRES          
                    .
                    " WHERE id_questionnaire='$id_to_delete' " ;
            mysqli_query($this->con,$sql);   
            //ensuite tout ce qui a cet id_ticket dans q_users_examens2key doit être deleté aussi
            $sql=   " DELETE FROM " . 
                    JsonModel::TABLE_USERS_EXAMEN2KEY          
                    .
                    " WHERE id_ticket='$id_tiket_to_delete' " ;
            mysqli_query($this->con,$sql);
            
        }else{
          /**
           * delete files associated
           */
           $this->deleteQuestion($id_to_delete,$id_user);        
        } 
        return "222";
    }      
    public function deleteQuestion($id_to_delete,$id_user){
          /**
           * delete files associated
           */
           //find all line in 'q_1_images' having id_item == id question we are deleting
            $sql = "SELECT * FROM " . 
                    $this->getTableName(
                      $id_user,
                      JsonModel::TABLE_IMAGES
                    ) .
                    " WHERE id_item='$id_to_delete'  ";      
        
            //var_dump($sql); die(__FILE__);
            $res = mysqli_query($this->con,$sql);
            $image_name = "";
            foreach ($res as $key => $value)
            {
                //$image_name = $value['image_name'];
                var_dump("------------> " + $id_user + " " + $value['id']);
                $this->deleteFile($id_user,$value['id']);//aa
            }   
            
           //delete image
           $sql = " DELETE FROM " 
           .
            $this->getTableName(
                $id_user,
                JsonModel::TABLE_IMAGES
            )  
           . 
           " WHERE id_item=$id_to_delete "; //OR id_questionnaire=$id_to_delete " ;
           mysqli_query($this->con,$sql);             
        
           //delete responses csidel
           $sql = " DELETE FROM " 
           .
            $this->getTableName(
                $id_user,
                JsonModel::TABLE_RESPONSES
            )  
           . 
           " WHERE id_question=$id_to_delete "; //OR id_questionnaire=$id_to_delete " ;
           mysqli_query($this->con,$sql);    
           
           /**
            * delete question
            */
           $sql = " DELETE FROM " 
           .
            $this->getTableName(
                $id_user,
                JsonModel::TABLE_QUESTIONNAIRE
            )  
           . 
           " WHERE id=$id_to_delete "; //OR id_questionnaire=$id_to_delete " ;
           mysqli_query($this->con,$sql);            
           
           
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
            $id_user,
            $text,
            $etat,
            $sauvegarde,
            $examen,
            $date_start,
            $date_stop,
            $comment,
            $autocorrection,
            $id_questionnaire,
            $is_chrono,
            $seconds
        ){ //csi
        
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
        $is_chrono  = $this->my_addslashes($is_chrono);
        $seconds  = $this->my_addslashes($seconds);
        
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
           "     qr_autocorrection='$autocorrection', qr_comment='$comment', "     .  
           "     q_is_chrono='$is_chrono', q_seconds='$seconds' ,             "     .
           "     insert_time=now() " .
           
           "     WHERE id=$id_questionnaire ";
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

