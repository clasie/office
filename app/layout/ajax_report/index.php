<?php 
global $workflow; 
global $origin; 
$origin = "REPORT"; 
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
        $origine  = $_POST['origin'];
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
        if($auth->origine != $_POST['origin']){
           set_session_here(); //renew session
        }else{
           //keep the session likr it is
        }
    }
    return (isset($_SESSION['auth'])? unserialize($_SESSION['auth']):null);
}
function get_questionnaire($id_author, $id_questionnaire, $users){

    global $workflow;
    global $origin;
    
    $origQuestionnaire = 
       $workflow->modelInstance->getQuestionnaireIdExamCorr
          ($id_author,$id_questionnaire);
       
    //var_dump(
    //    $id_author, 
    //    $id_questionnaire, 
    //    $origQuestionnaire, 
    //    $users
    //);
    
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
         public   $points;
         public   $tot_points;
         
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
            $points  = ""
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
             $this->points              = $points;
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
         public $tot_points;
         function __construct($id,$parent,$text,$tot_points,$data){        
             $this->id = $id;
             $this->parent = $parent;
             $this->text = $text;
             $this->data = $data;
             $this->tot_points = $tot_points;
         }     
     }
     class Nodes{
         public $nodes;      
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
     //$id_author, $id_questionnaire, $users
     $resutls 
        = $workflow->modelInstance->geTreeForReport(
              $id_author,
              $origQuestionnaire //$id_questionnaire
        ); //$workflow->mvcInstance->params["id_user"]);
        
     //var_dump($resutls);
     
     $id_author_new = $id_author; //$workflow->modelInstance->getAuthorId($id_questionnaire);
     $TotPointsForAQuestionnaire = 0;
     /**
      * feed instances
      */
     while($resQuestionnaire = mysqli_fetch_array($resutls["questionnaires"], MYSQLI_ASSOC))
     {
        /**
         * catch possible answers for the current question
         */
        $poss_answ_resutls = 
                $workflow->modelInstance->getPossibleAnswersForAQuestion(
                        $id_author_new, //"44", //$auth2->id,
                        //$workflow->mvcInstance->params["id_user"],
                        $resQuestionnaire["id"]);
    
        //var_dump($poss_answ_resutls);
        $exclusiveResponsesArray = array();
        $inclusiveResponsesArray = array();
    
        if($resQuestionnaire["node_type"] == "QUESTION"){
        
            while($resPossAnsw = mysqli_fetch_array($poss_answ_resutls["possible_answers"], MYSQLI_ASSOC))
            {
                 //disp($resPossAnsw,__FILE__);
                 
                 //EXCLUSIVE
                 if($resPossAnsw['answ_type'] == '4'){
                     //var_dump("111112");
                     $tmpBool = ($resPossAnsw['is_good_response'] == '1')? true:false;
                     $exclusiveResponsesArray[] =  
                             new QCMResponse(
                                $resPossAnsw['content'],
                                $resPossAnsw['is_good_response'] = $tmpBool, //'1')? true:false,
                                $resPossAnsw['id'],
                                $resPossAnsw['q_points']
                             ); 
                             if($tmpBool){
                                 $TotPointsForAQuestionnaire += 
                                    $resPossAnsw['q_points'];
                             }
                 }
                 //INCLUSIVE 
                 else if($resPossAnsw['answ_type'] == '5'){
                     $tmpBool = ($resPossAnsw['is_good_response'] == '1')? true:false;
                     $inclusiveResponsesArray[] =  
                             new QCMResponse(
                                 $resPossAnsw['content'],
                                 $resPossAnsw['is_good_response'] = $tmpBool, //= '1')? true:false,
                                 $resPossAnsw['id'],
                                 $resPossAnsw['q_points']
                             );   
                             if($tmpBool){
                                 $TotPointsForAQuestionnaire += 
                                    $resPossAnsw['q_points'];
                             }                             
                 }else{
                    //$TotPointsForAQuestionnaire += 
                    //                $resQuestionnaire['q_points'];
                 }    
            }
            //var_dump($exclusiveResponsesArray);
            if($resPossAnsw['answ_type'] != '4' || $resPossAnsw['answ_type'] != '5'){
               $TotPointsForAQuestionnaire += 
                                    $resQuestionnaire['q_points'];
            }
        }
        /**
         * catches all the images link to this node
         */
        $image_group = array();
        $auth2 = unserialize($_SESSION['auth']);
        //var_dump($auth2);
        $id_author = $workflow->modelInstance->getAuthorId($resQuestionnaire["id"]);
        $res_images = 
                $workflow->modelInstance->getImages(
                        //$auth2->id,
                        $id_author,
                        //$workflow->mvcInstance->params["id_user"],
                        $resQuestionnaire["id"]);
        //var_dump("-----> IMAGES");
        //var_dump($res_images);
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
        //var_dump("dddddddddddddd");
        //var_dump($exclusiveResponsesArray);
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
                    $resQuestionnaire["q_points"]
                );
                
                if($resQuestionnaire["node_type"] == "QUESTION"){
                    $TotPointsForAQuestionnaire += 
                       $resPossAnsw['q_points'];
                }                  
                //$dataQuestionnaire->tot_points = $TotPointsForAQuestionnaire;
                
        $nodeQuestionnaire = new Node( //$id,$parent,$text,$data){
                $resQuestionnaire["id"], //$id,
                $resQuestionnaire["parent"], //$parent,
                $resQuestionnaire["tree_label"], //$text,
                $resQuestionnaire["tot_points"] = $TotPointsForAQuestionnaire, //$text,
                $dataQuestionnaire);
        $tree_nodes->nodes[] = $nodeQuestionnaire;
    
        //if($resQuestionnaire["node_type"] == "QUESTION"){
        //    //disp($tree_nodes->nodes,__FILE__);
        //}
     }
     $auth = unserialize($_SESSION['auth']);
     //var_dump($auth);
     //var_dump($origin);
     //var_dump($auth->origine);
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
    return new App2($tree_nodes); 
}
/******************************************************************
 * CUD
 ******************************************************************/
if (isset($_POST['service'])) 
{
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
                   $workflow->modelInstance->newAuthUser(
                       //$user_id,
                       //$auth_2->id,
                       $user_login,
                       $user_mail,
                       $questionnaire_pass                   
                   );    
                   
               $res ['res']     = 'EXAM_USERS_CRUDS_NEW OK';                   
               $res ['error']   = $results['error'];
               $res ['message']   = $results['message'];
               
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
               $res ['message']   = "";
               
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
            //return;
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

               $results = 
                   $workflow->modelInstance->updateAuthUser(
                       $user_id,
                       $user_login,
                       $user_mail,
                       $questionnaire_pass                   
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
            
        case 'GET_ALL_QUESTIONNAIRES_FOR_ONE_AUTHOR':
            $service = "";
            if(isset($_POST['service'])){
                $service = $_POST['service'];
            }
            $author_id = "";               
            if(isset($_POST['id_author'])){
                $id_author = $_POST['id_author'];
            }        
            $questionnaires = 
                $workflow->modelInstance
                   ->getQuestionnaireListForOneAuthor($id_author);  
                   
            echo json_encode($questionnaires); 
            
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

               $results = 
                   $workflow->modelInstance->deleteAuthUser(
                       $user_id,
                       $user_login,
                       $user_mail,
                       $questionnaire_pass                   
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
               $res['topic'] = 'GET_LINKED_USERS OK';
               
               if(isset($_POST['questionnaire_id'])){
                   $questionnaire_id = $_POST['questionnaire_id'];
                   $res ['id'] = 'id found';
                   $res ['users'] = 
                      $workflow->modelInstance->getLinkedUsers(
                         $_POST['questionnaire_id'],
                         $_POST['id_author']
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
         * GET_REPORT_4_USERS
         */        
        case 'GET_REPORT_4_USERS':   
        
            //TO DO
            //we will return report for the selected user ACCORDING to the:
            //- questionnaire selected
            //- only no mock user
            //--> display this mock status...
            
            $auth = new SessionAuthentification();
            $return_res = null; //array();
            //var_dump($_SESSION);
            //echo 1;
            if(isset($_SESSION['auth'])){    
                //echo 7;
                //echo 2;
                /**
                * build questionnaire
                */
                $id_author  
                    = isset($_POST['id_author'])?$_POST['id_author']:"-1";
                $id_questionnaire  
                    = isset($_POST['id_questionnaire'])?$_POST['id_questionnaire']:"-1";                   
                $users  
                    = isset($_POST['users'])?$_POST['users']:"array()"; 
                    
                $id_author = 
                   $workflow->modelInstance->getAuthorId($id_questionnaire);
                //$users 
                $res_questionnaire = get_questionnaire(
                    $id_author, 
                    //$id_author,
                    $id_questionnaire, 
                    $users
                );                  
                $return_res['questionnaire'] = $res_questionnaire;
                //var_dump($res_questionnaire);
                
                /**
                * build users responses
                */
                $id_author = 
                   $workflow->modelInstance->getAuthorId($id_questionnaire);
                   
                $origQuestionnaire = 
                   $workflow->modelInstance->getQuestionnaireIdExamCorr
                      ($id_author,$id_questionnaire);
                //var_dump($origQuestionnaire);
                //$users 
                $res_responses = 
                   $workflow->modelInstance->getResponses(
                      $id_author, 
                      $origQuestionnaire, //$id_questionnaire,
                      $users
                );  
                //var_dump("---------------> response: oups");
                //var_dump($res_responses);
                $return_res['responses'] = $res_responses;
               /**
                * liste 1
                */
               //$auth->status = "GET_AUTHOR_USERS OK";
               //$auth_2 = unserialize($_SESSION['auth']);
               //$res = 
               //   $workflow->modelInstance->getAuthorUsers();

            }else{
               //echo 3;
               //$auth->status = "GET_AUTHOR_USERS KO";
            }
            echo json_encode($return_res); 
            return;
            break;          
       
            
        /**
         * GET_AUTHOR_USERS
         */        
        case 'GET_AUTHOR_USERS':      
            $auth = new SessionAuthentification();
            $res = array();
            if(isset($_SESSION['auth'])){
               /**
                * liste 1
                */
               $auth->status = "GET_AUTHOR_USERS OK";
               $auth_2 = unserialize($_SESSION['auth']);
               $res = 
                  $workflow->modelInstance->getAuthorUsers();

            }else{
               //$auth->status = "GET_AUTHOR_USERS KO";
            }
            echo json_encode($res); 
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
            //var_dump("rrrrrrrrrrrrrrrrrr");
            
            //if($_POST['origin'] != $origin){
            //   $auth->status = "KO";
            //   echo json_encode($auth); 
            //   return;               
            //}
            
            $auth = new SessionAuthentification();
            if(isset($_SESSION['auth'])){
                $auth = unserialize($_SESSION['auth']);
                if($_POST['origin'] != $auth->origine){
                  $auth->status = "KO";
                  echo json_encode($auth); 
                  return;                 
                }else{
                   $auth->status = "OK";
                }
                //var_dump($auth);
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
            if(isset($_SESSION['auth'])){
               $_SESSION['auth'] = null;
            }        
            $auth = new SessionAuthentification();
            $auth->status = "KO";
            echo json_encode($auth); 
            return;
            break;      
        /**
         * AUTH_CHALLENGE
         */        
        case 'AUTH_CHALLENGE':
            //var_dump(8);
            $auth = set_session();
            if($auth == null){
               $auth = new Authentification();
               $auth->status = "KO"; // = array(""Wrong credentials");
            }else{
               if($origin != $_POST['origin']){
                  $auth->status = "KO";
               }
            }
            echo json_encode($auth); 
            return;
            break;      
        /**
         * ORIGIN_CHALLENGE
         */        
        case 'ORIGIN_CHALLENGE':
            $origin = $_POST['origin'];
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
         * asked for an unknown service
         */         
        default:
            echo "UNKNOWS SERVICE: " . $flag;
    }    
  return;
}