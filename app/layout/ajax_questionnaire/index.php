<?php 
//die();
global $workflow; 
$origin = "QUESTIONNAIRE"; 
class Result {
   public $message = null;
}
class Authentification {
   public $status = null;
   public $id     = null;
   public $log    = null;
   public $mail   = null;   
}
function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\.\-]/', '', $string); // Removes special chars.

   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}
/**
 * UPLOAD a file has been asked
 */
class UploadedFile {
    public $file_url        = "";  
    public $file_name       = ""; 
    public $file_error      = ""; 
    public $file_success    = "";
    public $file_type       = "";
    public $file_size       = "";
    public $file_temp       = "";
    public $file_id         = "";
}
/**
 * campaign
 */
class Campaign {
    public $title    = "";  
    public $id       = ""; 
    public $status   = ""; 
}

if (
    isset($_POST['upload_image'])          && 
    isset($_POST['current_node_type'])     && 
    isset($_POST['current_node_id']) 
    ){
    
    if(!sessionExists($origin)){
        return;
    }
    
    $a = new UploadedFile();
    
    if(isset($_FILES["file"]["type"]))  
    {
        $fileNameSafe = strtolower(clean($_FILES["file"]["name"]));
        $typeSafe     = strtolower(clean($_POST['current_node_type']));
        $current_node_id_Safe     = strtolower(clean($_POST['current_node_id']));
        
        $fileNameSafe = $typeSafe . '_' . $current_node_id_Safe . '_' . $fileNameSafe;
        
        $validextensions = array("jpeg", "jpg", "png");
        $temporary = explode(".", $fileNameSafe); 
        $file_extension = end($temporary);
        
        $a->file_error = "";
        $a->error =  "";
        
        $auth2 = unserialize($_SESSION['auth']);
        
        if ((($_FILES["file"]["type"] == "image/png") || 
             ($_FILES["file"]["type"] == "image/jpg") || 
             ($_FILES["file"]["type"] == "image/jpeg")
            ) && ($_FILES["file"]["size"] < 10000000)//Approx. 100kb files can be uploaded.
              && in_array($file_extension, $validextensions)) 
	    {
          
            if ($_FILES["file"]["error"] > 0)
		    {
                $a->file_error = "Return Code: " . $_FILES["file"]["error"];
            } 
		    else 
		    { 
				if (file_exists("../upload/" . $auth2->id .  "/" . $fileNameSafe)) {
                    $a->file_error =  $fileNameSafe . " already exists.";
				} 
				else 
				{			
                   try{
					    $sourcePath = $_FILES['file']['tmp_name'];   // Storing source path of the file in a variable
					    $targetPath = "../upload/" . $auth2->id . "/".$fileNameSafe;  // Target path where file is to be stored
                        
                        $a->file_url = "../../" . $targetPath;
                        
					    move_uploaded_file($sourcePath,$targetPath) ; //  Moving Uploaded file						
					
					    $a->file_success = "Image Uploaded Successfully";
					    $a->file_name =  $fileNameSafe;
					    $a->file_type =  $_FILES["file"]["type"];
					    $a->file_size = ($_FILES["file"]["size"] / 1024) . " kB<br>";
					    $a->file_temp =  $_FILES["file"]["tmp_name"];	
                        
                        /**
                         * sauver willy ...
                         */
                         $auth2 = unserialize($_SESSION['auth']);
                        /**
                         * update nodes
                         */
                        $id = $workflow->modelInstance->insertImage(
                            $auth2->id, //"1",
                            $typeSafe, //QUESTION/QUESTIONNAIRE
                            $_POST['current_node_id'],
                            $a->file_name);
                            
                        $a->file_id = $id;
                            
                    }catch(Exception $e){
                       $a->error =  $e->message();
                    }
				}	                
            }        
        }   
	    else 
	    {
           $a->error =  "Invalid file Size or Type";
        }
    }
    echo json_encode($a); 
    return;
}
//die(__FILE__);
/**
 * Questionnaire building
 */

function disp($messages,$data){
    echo "<pre>";
    var_dump($messages);
    var_dump($data);
}
function convertDateJsToDB($date){ // dd/mm/yyyy -> yyyy-mm-dd
    if($date == NULL) return;
    $tmp = explode("/", $date);
    $tmp = $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0];
    return $tmp;
}
function convertDateDBToJs($date){ // yyyy-mm-dd -> dd/mm/yyyy
    if($date == NULL) return;
    $tmp = explode("-", $date);
    $tmp = $tmp[2] . '/' . $tmp[1] . '/' . $tmp[0];
    return $tmp;    
}
// ask to safe data -> to do  -> make a switch for all of that
// OR implement methods in your framework
// 
class ID{
    public $id;
}
class Debug{
   public $message;
}
class tutu{
   public $a;
}
//class Authentification {
//   public $status = null;
//   public $id     = null;
//   public $log    = null;
//   public $mail   = null;   
//}
class SessionAuthentification {
   public $status = null;
}
function sessionExists($origin){
   if(!isset($_SESSION['auth'])){
      return false;
   }else{
      $auth = unserialize($_SESSION['auth']);
      if($auth->origine == $origin){ //prevent cross session
         return true;
      }else{
         $_SESSION['auth'] = null; //destroy it
         return false;
      }
   }
}
/**
 * Authentification
 */
 function set_session_here(){
        global $workflow;
        $auth = new Authentification();
        /**
         * make here the auth test
         */
        $log = $_POST['log'];
        $pw  = $_POST['pw'];
        $origine  = $_POST['origine'];
        /**
         * used in the exam side
         */
        if(isset($_POST['ticket'])){ 
           $ticket  = $_POST['ticket'];
        }else{
           $ticket = "";
        }
        
        $user_data = $workflow->modelInstance->isUserKnown($log,$pw,$origine,$ticket);
        
        if($user_data['user_identified']){ 
           $auth->status = "OK";
           $auth->id = $user_data['user_id'];
           $auth->log = $user_data['user_log'];
           $auth->mail = $user_data['user_mail'];
           $auth->origine = $origine;
           $auth->ticket = $ticket;
           $auth->mocker = $user_data['mocker'];
           
           $_SESSION['auth'] = serialize($auth);
        }else{
           $auth->status = "KO";  
           $_SESSION['auth'] = null;
        } 
 }
 function set_session(){
    global $workflow; 
    $auth = null;
    if(!isset($_SESSION['auth'])){
       set_session_here(); //create session
    }else{
        $auth = unserialize($_SESSION['auth']);
        if($auth->origine != $_POST['origine']){
           set_session_here(); //renew session
        }else{
           //keep the session likr it is
        }
    }
    return (isset($_SESSION['auth'])? unserialize($_SESSION['auth']):null);
}
/******************************************************************
 * CUD
 ******************************************************************/
if (isset($_POST['service'])) {
    $flag = $_POST['service'];
    switch ($flag) {
        /**
         *
         * EXAM_USERS_CRUDS_NEW      
         *
         *  var EXAM_USERS_CRUDS_UPDATE = "EXAM_USERS_CRUDS_UPDATE";
         *  var EXAM_USERS_CRUDS_NEW    = "EXAM_USERS_CRUDS_NEW";
         *  var EXAM_USERS_CRUDS_DELETE = "EXAM_USERS_CRUDS_DELETE";         
         */   
        case 'EXAM_USERS_CRUDS_NEW':      
            $auth = new SessionAuthentification();
            $res = array();
            $res ['action'] = 'EXAM_USERS_CRUDS_NEW';
            
            if(isset($_SESSION['auth'])){
  
               $auth_2 = unserialize($_SESSION['auth']);
            
               //$user_id = "";               
               //if(isset($_POST['user_id'])){
               //   $user_id = $_POST['user_id'];
               //}
               
               $user_login = "";
               if(isset($_POST['user_login'])){
                  $user_login = $_POST['user_login'];               
               }
               $user_mail = "";
               if(isset($_POST['user_mail'])){
                  $user_mail = $_POST['user_mail'];               
               }
               $questionnaire_pass = "";
               if(isset($_POST['questionnaire_pass'])){
                  $questionnaire_pass = $_POST['questionnaire_pass'];
               }

               $results = 
                   $workflow->modelInstance->newExamUser(
                       //$user_id,
                       $auth_2->id,
                       $user_login,
                       $user_mail,
                       $questionnaire_pass                   
                   );    
                   
               $res ['res']     = 'EXAM_USERS_CRUDS_NEW OK';                   
               $res ['error']   = $results['error'];
               if(strlen($res ['error']) == 0){
                  $res ['id']      = $results['id'];
               } else {
                  $res ['id'] = '';
               }
               $res ['return']  = 'Return 1';
               
            }else{
            
               $res ['res']     = 'EXAM_USERS_CRUDS_NEW KO';
               $res ['error']   = 'No Session';               
               $res ['return']  = 'Empty';
               $res ['id']      = "";
               
            }
            echo json_encode($res); 
            return;
            break;     
        /**
         *
         * EXAM_USERS_CRUDS_UPDATE      
         *
         *  var EXAM_USERS_CRUDS_UPDATE = "EXAM_USERS_CRUDS_UPDATE";
         *  var EXAM_USERS_CRUDS_NEW    = "EXAM_USERS_CRUDS_NEW";
         *  var EXAM_USERS_CRUDS_DELETE = "EXAM_USERS_CRUDS_DELETE";         
         */   
        case 'EXAM_USERS_CRUDS_UPDATE':      
            $auth = new SessionAuthentification();
            $res = array();
            $res ['action'] = 'EXAM_USERS_CRUDS_UPDATE';
            
            if(isset($_SESSION['auth'])){
  
               $user_id = "";               
               if(isset($_POST['user_id'])){
                  $user_id = $_POST['user_id'];
               }
               $user_login = "";
               if(isset($_POST['user_login'])){
                  $user_login = $_POST['user_login'];               
               }
               $user_mail = "";
               if(isset($_POST['user_mail'])){
                  $user_mail = $_POST['user_mail'];               
               }
               $questionnaire_pass = "";
               if(isset($_POST['questionnaire_pass'])){
                  $questionnaire_pass = $_POST['questionnaire_pass'];
               }
               $mocker = "";
               if(isset($_POST['mocker'])){
                  $mocker = $_POST['mocker'];
               }
               $results = 
                   $workflow->modelInstance->updateExamUser(
                       $user_id,
                       $user_login,
                       $user_mail,
                       $questionnaire_pass,
                       $mocker
                   );    
                   
               $res ['res']     = 'EXAM_USERS_CRUDS_UPDATE OK';                   
               $res ['error']   = $results['error'];
               $res ['return']  = 'Return 1';
               
            }else{
            
               $res ['res']     = 'EXAM_USERS_CRUDS_UPDATE KO';
               $res ['error']   = 'No Session';               
               $res ['return']  = 'Empty';
               
            }
            echo json_encode($res); 
            return;
            break; 
        /**
         *
         * EXAM_USERS_CRUDS_DELETE      
         *
         *  var EXAM_USERS_CRUDS_UPDATE = "EXAM_USERS_CRUDS_UPDATE";
         *  var EXAM_USERS_CRUDS_NEW    = "EXAM_USERS_CRUDS_NEW";
         *  var EXAM_USERS_CRUDS_DELETE = "EXAM_USERS_CRUDS_DELETE";         
         */   
        case 'EXAM_USERS_CRUDS_DELETE':      
            $auth = new SessionAuthentification();
            $res = array();
            $res ['action'] = 'EXAM_USERS_CRUDS_DELETE';
            
            if(isset($_SESSION['auth'])){
  
               $user_id = "";               
               if(isset($_POST['user_id'])){
                  $user_id = $_POST['user_id'];
               }
               $user_login = "";
               if(isset($_POST['user_login'])){
                  $user_login = $_POST['user_login'];               
               }
               $user_mail = "";
               if(isset($_POST['user_mail'])){
                  $user_mail = $_POST['user_mail'];               
               }
               $questionnaire_pass = "";
               if(isset($_POST['questionnaire_pass'])){
                  $questionnaire_pass = $_POST['questionnaire_pass'];
               }
               $auth_2 = unserialize($_SESSION['auth']);
               $results = 
                   $workflow->modelInstance->deleteExamUser(
                       $user_id,
                       $user_login,
                       $user_mail,
                       $questionnaire_pass,
                       $auth_2->id
                   );    
                   
               $res ['res']     = 'EXAM_USERS_CRUDS_DELETE OK';                   
               $res ['error']   = $results['error'];
               $res ['return']  = 'Return 1';
               
            }else{
            
               $res ['res']     = 'EXAM_USERS_CRUDS_DELETE KO';
               $res ['error']   = 'No Session';               
               $res ['return']  = 'Empty';
               
            }
            echo json_encode($res); 
            return;
            break; 
        /**
         *
         * GET_LINK_USERS        
         *
         */   
        case 'GET_LINKED_USERS':      
            $auth = new SessionAuthentification();
            $res = array();
            if(isset($_SESSION['auth'])){
               $auth_2 = unserialize($_SESSION['auth']);
               $res['topic'] = 'GET_LINKED_USERS OK';
               
               if(isset($_POST['questionnaire_id'])){
                   $questionnaire_id = $_POST['questionnaire_id'];
                   $res ['id'] = 'id found';
                   $res ['users'] = 
                      $workflow->modelInstance->getLinkedUsers(
                         $_POST['questionnaire_id'],$auth_2->id  
                    );    
               }else{
                   $res['res'] = 'id NOT found';                               
               }
            }else{
               $res['res'] = 'GET_LINKED_USERS KO';
            }
            echo json_encode($res); 
            return;
            break;       
        /**
         *
         * GET_UNIQUE_TICKET_FOR_A_QUESTIONNAIRE
         * 
         * AXIOM: questionnaires [1]----to----[1] Tickets
         *
         *
         * New:
         * 
         * -> crud on ticket (if needed)
         * -> crud on users (if needed)
         * -> display results
         * 
         */        
        case 'LINK_USERS_TO_TICKET':    
            
            //return;
            $auth = new SessionAuthentification();
            $res = array();
            if(isset($_SESSION['auth']))
            {
               $res[] = 'SESSION OK';
               $auth_2 = unserialize($_SESSION['auth']);
               if(isset($_POST['users_to_link']))
               {
			   
			       /**
				    * manage ticket stuff
					*/
				   $old_ticket = $file_id = $_POST['old_tiket'];
				   $ticket_label = ""; //$file_id = $_POST['ticket_label'];
				   $questionnaire_id = $file_id = $_POST['questionnaire_id'];
				   /**
					* UPDATE/CREATE ticket
					*/
                   if(strlen(trim($old_ticket))  == 0 )
                   {
                     $res = 
                          $workflow
                             ->modelInstance
                                ->getUniqueTicketForAQuestionnaire
                                   (
                                      $auth_2->id,
                                      $old_ticket,
                                      $ticket_label,
                                      $questionnaire_id
                                   );
                   }

				   //$res['result']['ticket']  = $res['ticket'];
                   /**
				    * link user to the ticket
					*/
                   $res['result'] = 
                      $workflow->modelInstance->linkUsersToTicket(
                         $_POST['users_to_link'],
                         $_POST['questionnaire_id'],
                         $auth_2->id
                      );      
                      
                   if(strlen(trim($old_ticket))  > 0 ){
                      $res['result']['ticket']  = $old_ticket;
                   }else{
                      $res['result']['ticket']  = $res['ticket'];
                   }
                   
               }else{
                   $res['result'] = 
                      $workflow->modelInstance->linkUsersToTicket(
                         array(),
                         $_POST['questionnaire_id'],
                         $auth_2->id
                      );                  
              }
            }else{
               $res['session'] = 'NO SESSION';
            }
            echo json_encode($res); 
            return;
            break;        
        /**
         * GET_UNIQUE_TICKET_FOR_A_QUESTIONNAIRE
         */        
        case 'GET_UNIQUE_TICKET_FOR_A_QUESTIONNAIRE':      
            $auth = new SessionAuthentification();
            $res = array();
            if(isset($_SESSION['auth'])){
               $auth->status = "GET_UNIQUE_TICKET_FOR_A_QUESTIONNAIRE OK";
               $auth_2 = unserialize($_SESSION['auth']);
               //params
               $old_ticket = $file_id = $_POST['old_tiket'];
               $ticket_label = ""; //$file_id = $_POST['ticket_label'];
               $questionnaire_id = $file_id = $_POST['questionnaire_id'];
               /**
                * UPDATE/CREATE ticket
                */
               $res = 
                  $workflow
                     ->modelInstance
                        ->getUniqueTicketForAQuestionnaire
                           (
                              $auth_2->id,
                              $old_ticket,
                              $ticket_label,
                              $questionnaire_id
                           );
            }
            $res['result'] = $res;
            echo json_encode($res); 
            return;
            break;      
        /**
         * UPDATE_CAMPAIGN_STATUS
         */        
        case 'UPDATE_CAMPAIGN_STATUS':      
            $auth = new SessionAuthentification();
            $res = array();
            if(isset($_SESSION['auth'])){
               $auth_2 = unserialize($_SESSION['auth']);
               $old_campaign_id = $_POST['old_campaign_id'];
               $newStatus = $_POST['newStatus'];
               $questionnaire_id = $_POST['questionnaire_id'];
               /**
                * UPDATE/CREATE ticket
                */
               $res = 
                  $workflow
                     ->modelInstance
                        ->updateOldCampaignStatus
                           (
                              $auth_2->id,
                              $old_campaign_id,
                              $newStatus,
                              $questionnaire_id
                           );
            }
            echo json_encode($res); 
            return;
            break;   
        /**
         * UPDATE_CAMPAIGN_STATUS
         */        
        case 'DELETE_CAMPAIGN_STATUS':      
            return;
            $auth = new SessionAuthentification();
            $res = array();
            if(isset($_SESSION['auth'])){
               $auth_2 = unserialize($_SESSION['auth']);
               $old_campaign_id = $_POST['old_campaign_id'];
               $newStatus = $_POST['newStatus'];
               $questionnaire_id = $_POST['questionnaire_id'];
               /**
                * UPDATE/CREATE ticket
                */
               $res = 
                  $workflow
                     ->modelInstance
                        ->deleteOldCampaignStatus
                           (
                              $auth_2->id,
                              $old_campaign_id,
                              $newStatus,
                              $questionnaire_id
                           );
            }
            echo json_encode($res); 
            return;
            break;              
        /**
         * GET_EXAM_USERS_FOR_SESSION_AUTHOR
         */        
        case 'GET_EXAM_USERS_FOR_SESSION_AUTHOR':      
            $auth = new SessionAuthentification();
            $res = array();
            if(isset($_SESSION['auth'])){
               /**
                * liste 1
                */
               $auth->status = "GET_EXAM_USERS_FOR_SESSION_AUTHOR OK";
               $auth_2 = unserialize($_SESSION['auth']);
               $res = 
                  $workflow->modelInstance->getExamUsersForOneAuthor($auth_2->id);
               /**
                * liste 2
                */
               //$res2 = 
               //     $workflow->modelInstance->getLinkedUsers(
               //         $_POST['questionnaire_id']);
               //var_dump($res);
               //var_dump($res2);
            }else{
               //$auth->status = "GET_EXAM_USERS_FOR_SESSION_AUTHOR KO";
            }
            echo json_encode($res); 
            return;
            break;          
        /**
         * DELETE_FILE
         */        
        case 'DELETE_FILE':      
            $auth = new SessionAuthentification();
            if(isset($_SESSION['auth'])){
               $auth->status = "OK";
               //delete file
               $file_id = $_POST['file_id'];
               //delete from db + file system
               $auth2 = unserialize($_SESSION['auth']);
               $user_id = $auth2->id; //"1";
               $res = $workflow->modelInstance->deleteFile($user_id,$file_id);
               //disp($res,__FILE__);
            }else{
               $auth->status = "KO";
            }
            echo json_encode($auth); 
            return;
            break;       
        /**
         * DISTROY_SESSION_AUTH_CHALLENGE
         */        
        case 'DISTROY_SESSION_AUTH_CHALLENGE':
            $_SESSION['auth'] = null;
            echo json_encode(new SessionAuthentification()); 
            return;
            break;     
        /**
         * SESSION_AUTH_CHALLENGE
         */        
        case 'SESSION_AUTH_CHALLENGE':
            $auth = new SessionAuthentification();
            if(isset($_SESSION['auth'])){
           
               $auth->status = "OK";
               $auth2 = unserialize($_SESSION['auth']);
               $auth->id_user = $auth2->id;
               
            }else{
               $auth->status = "KO";
            }
            echo json_encode($auth); 
            return;
            break;     
        /**
         * RESET_AUTH
         */        
        case 'RESET_AUTH':
            $auth = null;
            if(isset($_SESSION['auth'])){
               $_SESSION['auth'] = null;
            }        
            echo json_encode($auth); 
            return;
            break;      
        /**
         * AUTH_CHALLENGE
         */        
        case 'AUTH_CHALLENGE':
            $auth = set_session();
            echo json_encode($auth); 
            return;
            break;      
        /**
         * ORIGIN_CHALLENGE
         */        
        case 'ORIGIN_CHALLENGE':
            $origin = $_POST['origine'];
            if(!sessionExists($origin)){
               $ret['error'] = 1;
               echo json_encode($ret); 
               break;
            }else{
               $ret['error'] = 0;
               echo json_encode($ret); 
               break;            
            }
            return;
            break;              
        /**
         * a question has been d&dropped 
         * => we have to update the position values accordingly
         */
        case 'REORDER_DRAGGED_QUESTION':
            if(!sessionExists($origin)){
               break;
            }
            
            $auth2 = unserialize($_SESSION['auth']);
            $user_id = $auth2->id; //"1";          
            
            //disp('REORDER_DRAGGED_QUESTION',__FILE__);
            $position = $_POST['position'];
            $old_position = $_POST['old_position'];
            $parent = $_POST['parent'];
            $old_parent = $_POST['old_parent'];
            $id_question = $_POST['id_question'];
                                    
            $resutls = $workflow->modelInstance->reorderDraggedQuestion(
                $position,$old_position,$parent,$old_parent,$id_question,$id_user);
            break;
        /**
         * asked to update existing question node
         */        
        case 'UPDATE_QUESTION_NODE':
            if(!sessionExists($origin)){
               break;
            }
            $auth2 = unserialize($_SESSION['auth']);
            
            //echo "UPDATE_QUESTION_NODE";
            //disp($_POST,__FILE__);die(__FILE__);
            $text = $_POST['text'];
            $intitule = $_POST['intitule'];
            $responsetype = $_POST['responsetype'];  
            $id_question = $_POST['id_question']; 

            $good_response_txt = 
			   (!isset($_POST['good_response_txt']))?null:$_POST['good_response_txt']; 

			$dateIfAny =         
			   (!isset($_POST['good_response_date']))?"":$_POST['good_response_date']; 

            $good_response_date = convertDateJsToDB($dateIfAny); //($_POST['good_response_date']); 

            $good_response_number =  
			   (!isset($_POST['good_response_number']))?"": $_POST['good_response_number'];

            $qcm_exclu = (isset($_POST['qcmExclu']))?$_POST['qcmExclu']:null; 

			//var_dump($qcm_exclu);

            $qcm_inclu = (isset($_POST['qcmInclu']))?$_POST['qcmInclu']:null; 
            $points = (isset($_POST['points']))?$_POST['points']:null; 
            
            /**
             * check if the questionnaire is actif, if so avoid
             */
             //...select the parent id...check if the parent STATE
             $boolResult = $workflow
                ->modelInstance
                   ->isQuestionUpdatable($auth2->id,$id_question);
                   
            //var_dump($boolResult);
            if(!$boolResult){
               var_dump("not editable because actif");
               return;
            }else{
               //var_dump("YES editable because NOT actif");
            }

            /**
             * update nodes
             */
            $resutls = $workflow->modelInstance->updateNodeQuestion(
                            $auth2->id, //$workflow->mvcInstance->params["id_user"],
                            $text,
                            $intitule,
                            $responsetype,
                            $id_question,
                            $good_response_txt,
                            $good_response_date,
                            $good_response_number,
                            $points);
            /**
             * update possible answers EXCLUSIVES
             */
             //delete all possible answers for this question
             $workflow->modelInstance->deleteExclPossibleAnswers
                ($auth2->id,$id_question);
             //save the new answers
			 //echo"77";
			 $x = 88;
             if($qcm_exclu != null){
                 $sequence = 0;
				 $x = 89;
                 foreach ($qcm_exclu as $key => $value){
				     //var_dump("oups!");
				     //var_dump($value);
					 //var_dump($value);
					 $x = 90;
                     $x = $workflow->modelInstance->createNewPossibleAnswer(
                                    $auth2->id, //$workflow->mvcInstance->params["id_user"],
                                    '4',
                                    $value['text'],
                                    ($value['isGood'] == 'true')?1:0 ,
                                    $id_question,
                                    $sequence++,
                                    $value['points']
                                    );  
	                //echo $x;
                 }
				 //echo $x;
             } 
			 var_dump( $x);           
            /**
             * update possible answers INCLUSIVES
             */            
             //delete all possible answers for this question
             $workflow->modelInstance->deleteInclPossibleAnswers
             ($auth2->id,$id_question);
             //save the new answers
             if($qcm_inclu != null){
                 $sequence = 0;
                 foreach ($qcm_inclu as $key => $value){
                     echo $workflow->modelInstance->createNewPossibleAnswer(
                                    $auth2->id, //$workflow->mvcInstance->params["id_user"],
                                    '5',
                                    $value['text'],
                                    ($value['isGood'] == 'true')?1:0 ,
                                    $id_question,
                                    $sequence++,
                                    $value['points']
                                    );                  
                 }
             }
             break;
        /**
         * asked to update existing questionnaire node
         */         
        case 'ARCHIVE_QUESTIONNAIRE':
            if(!sessionExists($origin)){
               break;
            }
            //$text = $_POST['text']; 
            //$etat = $_POST['etat']; 
            //$sauvegarde = $_POST['sauvegarde']; 
            //$examen = $_POST['examen']; 
            //$date_start = convertDateJsToDB($_POST['date_start']); 
            //$date_stop = convertDateJsToDB($_POST['date_stop']); 
            //$comment = $_POST['comment']; 
            $id_questionnaire = $_POST['id_questionnaire'];   
            //$autocorrection = $_POST['autocorrection'];
            //$is_chrono = $_POST['is_chrono'];
            //$seconds = $_POST['seconds'];
      
            $auth2 = unserialize($_SESSION['auth']);
            
            $res = $workflow->modelInstance
                  ->ArchiveResponses(
                     $id_questionnaire,
                     $auth2->id); 

            $resutls = new Result();
            $resutls->message = $res;
            echo json_encode($resutls); 
            return;            
            break;               
        /**
         * asked to update existing questionnaire node
         */         
        case 'UPDATE_QUESTIONNAIRE_NODE':
            //echo "UPDATE_QUESTIONNAIRE_NODE";
            if(!sessionExists($origin)){
               break;
            }
            $text = $_POST['text']; 
            $etat = $_POST['etat']; 
            $sauvegarde = $_POST['sauvegarde']; 
            $examen = $_POST['examen']; 
            $date_start = convertDateJsToDB($_POST['date_start']); 
            $date_stop = convertDateJsToDB($_POST['date_stop']); 
            $comment = $_POST['comment']; 
            $id_questionnaire = $_POST['id_questionnaire'];   
            $autocorrection = $_POST['autocorrection'];
            $is_chrono = $_POST['is_chrono'];
            $seconds = $_POST['seconds'];
      
            $auth2 = unserialize($_SESSION['auth']);
            
            $resutls = $workflow->modelInstance->updateNodeQuestionnaire(              
                    $auth2->id, //$workflow->mvcInstance->params["id_user"],
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
                    );            
            
            break;  
        /**
         * asked to create new questionnaire node
         */         
        case 'CREATE_QUESTIONNAIRE_NODE':
            //echo "CREATE_QUESTION_NODE";
            if(!sessionExists($origin)){
               break;
            }
            $text = $_POST['text']; 
            $etat = $_POST['etat']; 
            $sauvegarde = $_POST['sauvegarde']; 
            $examen = $_POST['examen']; 
            $date_start = convertDateJsToDB($_POST['date_start']); 
            $date_stop = convertDateJsToDB($_POST['date_stop']); 
            $comment = $_POST['comment']; 
            $node_type = $_POST['node_type']; 
            //$id_questionnaire = $_POST['id_questionnaire'];   
            $autocorrection = $_POST['autocorrection'];
      
            $auth2 = unserialize($_SESSION['auth']);
            
            $newId = $workflow->modelInstance->createNodeQuestionnaire(              
                    $auth2->id, //$workflow->mvcInstance->params["id_user"],
                    $text,
                    $etat,
                    $sauvegarde,
                    $examen,
                    $date_start,
                    $date_stop,
                    $comment,
                    $autocorrection,
                    $node_type
                    );  
            $id = new ID();
            $id->id = $newId;
            //$return = "{'new_id':$newId}";
            echo json_encode($id); 
            return;
            break;          
        /**
         * asked to create new questionnaire node
         */         
        case 'CREATE_QUESTION_NODE':
            //echo "CREATE_QUESTION_NODE";
            if(!sessionExists($origin)){
               break;
            }
            $text = $_POST['text'];
            $intitule = $_POST['intitule'];
            $responsetype = $_POST['responsetype'];   
            $parent = $_POST['parent'];
            $node_type = $_POST['node_type'];
            
            $auth2 = unserialize($_SESSION['auth']);
            
            $newId = $workflow->modelInstance->createNodeQuestion(
                    $auth2->id, //$workflow->mvcInstance->params["id_user"],
                    $text,
                    $intitule,
                    $responsetype,
                    $parent,
                    $node_type);
                    
            $id = new ID();
            $id->id = $newId;
            //$return = "{'new_id':$newId}";
            echo json_encode($id); 
            return;
            break;      
        /**
         * asked to create new questionnaire node
         */         
        case 'DELETE_NODE':
            //echo "DELETE_NODE";  
            if(!sessionExists($origin)){
               break;
            }            
            $auth2 = unserialize($_SESSION['auth']);
            $id_to_delete = $_POST['id_to_delete'];
            $node_type = $_POST['node_type'];
            $node_parent = $_POST['node_parent'];
            $res = $workflow->modelInstance->deleteNode
            (
               $auth2->id,
               $id_to_delete,
               $node_type,
               $node_parent
            );
            $id = new ID();
            $id->id = $res;
            //$return = "{'new_id':$newId}";
            echo json_encode($id); 
            return;
            break;         
        /**
         * asked for an unknown service
         */         
        default:
            echo "UNKNOWS SERVICE: " . $flag;
    }    
  return;
}

/******************************************************************
 * R
 ******************************************************************/
class QCMResponse {
    public $text; // = "Mon test response";  
    public $isGood; // = true;
    /**
            var TEXT = '1';
            var NUMBER = '2';
            var DATE = '3';
            var QCM_EXCLU = '4';
            var QCM_INCLU = '5';
            var CONDITIONNAL = '6';
     */    
    public $type; // = true;
    public $points;
    function __construct($text,$isGood,$id_qcm_response,$points){
        $this->text = $text;
        $this->isGood = $isGood;       
        $this->id_qcm_response = $id_qcm_response;
        $this->points = $points;
    }
 }
 /**
  * can concern either a questionnaire OR a question
  */
 class DataQuestionnaire{ 
     public   $TYPE;
     public   $ETAT;
     public   $SAUVEGARDE;
     public   $EXAMEN;
     public   $DATE_START;
     public   $DATE_STOP;
     public   $AUTO_CORRECTION;
     public   $COMMENT;       
     //questions
     public   $INTITULE;
     public   $TYPE_REPONSE;    
     //test array
     public   $TYPE_VALUES;
     //images array
     public   $IMAGES;
     public   $ticket;
     public   $isChrono;
     public   $seconds;
     public   $points;
     //array of already existing campaigns
     public   $campaigns;
     
     function __construct(
        $type,$etat,$sauvegarde,$examen,$date_start,
        $date_stop,$auto_correction,$comment,$intitule,$type_response,
        $good_response_txt,
             
        $exclusiveResponsesArray,
        $inclusiveResponsesArray,
             
        $good_response_date,
        $good_response_number,
        $IMAGES,
        $ticket,
        $isChrono = "",
        $seconds  = "",
        $points  = "",
        $campaigns
     ){
        $this->TYPE                 = $type;
        $this->ETAT                 = $etat;
        $this->SAUVEGARDE           = $sauvegarde;
        $this->EXAMEN               = $examen;
        $this->DATE_START           = $date_start;
        $this->DATE_STOP            = $date_stop;
        $this->AUTO_CORRECTION      = $auto_correction;
        $this->COMMENT              = $comment;      
        $this->INTITULE             = $intitule;
        $this->TYPE_REPONSE         = $type_response;    
        $this->GOOD_RESPONSE_TXT    = $good_response_txt;
        $this->GOOD_RESPONSE_DATE   = $good_response_date;
        $this->GOOD_RESPONSE_NUMBER = $good_response_number;
        $this->IMAGES               = $IMAGES;
                /*
                array(
                    new QCMResponse("Mon test response ex",true), 
                    new QCMResponse("Mon test response ex",false)); */
        $this->INCLUSIVE_TYPE_VALUES = $inclusiveResponsesArray;
        $this->EXCLUSIVE_TYPE_VALUES = $exclusiveResponsesArray;
                /*
                array(
                    new QCMResponse("Mon test response in",true), 
                    new QCMResponse("Mon test response in",true), 
                    new QCMResponse("Mon test response in",true)); */
         $this->ticket              = $ticket;
         $this->isChrono            = $isChrono;
         $this->seconds             = $seconds;
         $this->points              = $points;
         $this->campaigns           = $campaigns;
     }       
 }
 class Ticket{
     public $id;
     public $value;
     public $label;
 }
 class Node{
     public $id;
     public $parent;
     public $text;
     public $data;
     function __construct($id,$parent,$text,$data){        
         $this->id = $id;
         $this->parent = $parent;
         $this->text = $text;
         $this->data = $data;
     }     
 }
 class Nodes{
     public $nodes;      
 } 
/************************************************************************
 * appel ajax qui construit l arbre par defaut
 ************************************************************************/
 /**
  * build questionnaires nodes
  */
 $tree_nodes = new Nodes();
 
 /**
  * to do do this from DB
  */
 //build exclu possible answers
 $exclusiveResponsesArray = array();
 $inclusiveResponsesArray = array();
 /**
  * ask DB
  */
 $auth2 = unserialize($_SESSION['auth']);
 $resutls = $workflow->modelInstance->geTree($auth2->id); //$workflow->mvcInstance->params["id_user"]);
 /**
  * feed instances
  */
 while($resQuestionnaire = mysqli_fetch_array($resutls["questionnaires"], MYSQLI_ASSOC)){
    /**
     * catch possible answers for the current question
     */
    $poss_answ_resutls = 
            $workflow->modelInstance->getPossibleAnswersForAQuestion(
                    $auth2->id,
                    //$workflow->mvcInstance->params["id_user"],
                    $resQuestionnaire["id"]);
    
    $exclusiveResponsesArray = array();
    $inclusiveResponsesArray = array();
    
    if($resQuestionnaire["node_type"] == "QUESTION"){
        
        while($resPossAnsw = mysqli_fetch_array($poss_answ_resutls["possible_answers"], MYSQLI_ASSOC)){
             //disp($resPossAnsw,__FILE__);
             //EXCLUSIVE
             if($resPossAnsw['answ_type'] == '4'){
                 $exclusiveResponsesArray[] =  
                         new QCMResponse(
                            $resPossAnsw['content'],
                            ($resPossAnsw['is_good_response'] == '1')? true:false,
                            $resPossAnsw['id'],
                            $resPossAnsw['q_points']
                         ); 
             }
             //INCLUSIVE 
             if($resPossAnsw['answ_type'] == '5'){
                 $inclusiveResponsesArray[] =  
                         new QCMResponse(
                             $resPossAnsw['content'],
                             ($resPossAnsw['is_good_response'] == '1')? true:false,
                             $resPossAnsw['id'],
                             $resPossAnsw['q_points']
                         );   
             }         
        }
        if($resQuestionnaire["node_type"] == "QUESTION"){
           //disp($exclusiveResponsesArray,__FILE__);
        }
    }
    /**
     * catches all the images link to this node
     */
    $image_group = array();
    $auth2 = unserialize($_SESSION['auth']);
    //var_dump($auth2);
    $res_images = 
            $workflow->modelInstance->getImages(
                    $auth2->id,
                    //$workflow->mvcInstance->params["id_user"],
                    $resQuestionnaire["id"]);
    //extract imgs from db
    if($res_images != false){
        while($image = mysqli_fetch_array($res_images, MYSQLI_ASSOC)){
    
           $uploadedFile = new UploadedFile();
           $uploadedFile->file_url = '../../../upload/' . $auth2->id . '/' . $image['image_name'];
           $uploadedFile->file_name = $image['image_name']; //m5
           $uploadedFile->file_id = $image['id'];
           $image_group[] = $uploadedFile;
        }    
    }      
    /**
     * get the ticket value from db if node is questionnaire
     */
    $tick = new Ticket();
    if($resQuestionnaire["node_type"] == "QUESTIONNAIRE"){
        $tick->id = 
           $workflow->modelInstance
              ->getTicketIdFromQuestionnaireId(
                 $resQuestionnaire["id"],
                 $auth2->id);
        /**
         * fill the ticket values   
         */
        if($tick->id != null){
            $ticket_values = 
               $workflow->modelInstance
                  ->getTicketDataFromId($tick->id); 
                  
            $tick->value =  $ticket_values['value']; 
            $tick->label =  $ticket_values['label']; 
        }
    }
    
    //get all existing campaign
    $campaigns = 
       $workflow->modelInstance->getCampaigns
          ($auth2->id,$resQuestionnaire["id"]); 
          
    //setCampaignLinkedToThisQuestionnaire
    $campaignsArray = "";      
    while($campaign = mysqli_fetch_array($campaigns, MYSQLI_ASSOC)){
       $camp = new Campaign();
       $camp->id     = $campaign['id'];
       $camp->title  = $campaign['tree_label'] .  " /id/" . $campaign['id'];
       $camp->status = 
          ($campaign['qr_running_status'] == null)?"Inactive":$campaign['qr_running_status'];
          
       $campaignsArray[] = $camp;
    }          
    //var_dump($campaignArray);
    //die(__FILE__);
    
    $dataQuestionnaire =  
        new DataQuestionnaire(// == node = questionnaire OR question
                $resQuestionnaire["node_type"], //$type,
                $resQuestionnaire["qr_state"], //$etat,
                $resQuestionnaire["qr_save"], //$sauvegarde,
                $resQuestionnaire["qr_random"], //$examen,
                convertDateDBToJs($resQuestionnaire["qr_date_start"]), //$date_start, // ---> changer  le format
                convertDateDBToJs($resQuestionnaire["qr_date_stop"]), //$date_stop,   // ---> changer  le format
                $resQuestionnaire["qr_autocorrection"], //$auto_correction,
                $resQuestionnaire["qr_comment"],//$comment,
                //question only
                $resQuestionnaire["q_label"],
                $resQuestionnaire["q_resp_type"],
                $resQuestionnaire["q_good_response_txt"],
                //exclu
                $exclusiveResponsesArray,
                //exclu
                $inclusiveResponsesArray,
                //good answers
                convertDateDBToJs($resQuestionnaire["q_good_response_date"]), //q_good_response_number
                $resQuestionnaire["q_good_response_number"],
                $image_group,
                $tick,
                $resQuestionnaire["q_is_chrono"],
                $resQuestionnaire["q_seconds"],
                $resQuestionnaire["q_points"],
                $campaignsArray
            );//777-1
    
    $nodeQuestionnaire = new Node( //$id,$parent,$text,$data){
            $resQuestionnaire["id"], //$id,
            $resQuestionnaire["parent"], //$parent,
            $resQuestionnaire["tree_label"], //$text,
            $dataQuestionnaire);
    $tree_nodes->nodes[] = $nodeQuestionnaire;
    
    if($resQuestionnaire["node_type"] == "QUESTION"){
        //disp($tree_nodes->nodes,__FILE__);
    }
 }
class App2{
    public $jsTreeNodes;
    function __construct($tree_nodes){
         $this->jsTreeNodes = $tree_nodes;
    }     
}
//echo "<pre>";
if(!sessionExists($origin)){
return;
}
echo json_encode(new App2($tree_nodes)); 
//{"jsTreeNodes":{"nodes":[{"id":"789","parent":"#","text":"Questionnaire 1","data":{"TYPE":"QUESTIONNAIRE","ETAT":"1","SAUVEGARDE":"2","EXAMEN":"3","DATE_START":"23\/12\/2014","DATE_STOP":"24\/12\/2014","AUTO_CORRECTION":"CHECKED","COMMENT":"comment 1"}}]}}