<?php 
global $workflow; 
$origin = "ADMIN"; 
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
                         $_POST['questionnaire_id']
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
            $auth = new SessionAuthentification();
            if(isset($_SESSION['auth'])){
               $auth->status = "OK";
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
            echo json_encode($auth); 
            return;
            break;      
        /**
         * AUTH_CHALLENGE
         */        
        case 'AUTH_CHALLENGE':
            $auth = set_session();
            if($auth == null){
               $auth = new Authentification();
               $auth->status = "KO"; // = array(""Wrong credentials");
            }
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
         * asked for an unknown service
         */         
        default:
            echo "UNKNOWS SERVICE: " . $flag;
    }    
  return;
}