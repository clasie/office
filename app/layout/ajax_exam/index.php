<?php 
//l authentif se fait ici: 
//   C:\projets\PHP\office\app\layout\ajax_exam\index.php

global $workflow; 
$origin = "EXAMEN"; 
class Authentification {
   public $status = null;
   public $id     = null;
   public $log    = null;
   public $mail   = null;   
   public $ticket = null; 
   public $mock   = null;
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
}

/******************************************************************
 * R
 ******************************************************************/
class QCMResponse {
    public $text; // = "Mon test response";  
    public $isGood; // = true;
    public $isSelectedAsAnswer; // = true;
    /**
            var TEXT = '1';
            var NUMBER = '2';
            var DATE = '3';
            var QCM_EXCLU = '4';
            var QCM_INCLU = '5';
            var CONDITIONNAL = '6';
     */    
    public $type; // = true;
    function __construct($text,$isGood,$id_qcm_response,$isSelectedAsAnswer){
        $this->text = $text;
        $this->isGood = $isGood;        
        $this->id_qcm_response = $id_qcm_response;
        $this->isSelectedAsAnswer = $isSelectedAsAnswer;
    }
 }
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
     public   $ANSWERED_RESPONSE_TXT;
     public   $ANSWERED_RESPONSE_NUMBER;
     public   $ANSWERED_RESPONSE_DATE;
     //images array
     public   $IMAGES;
     public   $isChrono;
     public   $seconds;     
     
     function __construct(
        $type,$etat,$sauvegarde,$examen,$date_start,
        $date_stop,$auto_correction,$comment,$intitule,$type_response,
        $good_response_txt,
             
        $exclusiveResponsesArray,
        $inclusiveResponsesArray,
             
        $good_response_date,
        $good_response_number,
        $IMAGES,
        $isChrono = "",
        $seconds  = ""        
     ){
        $this->TYPE              = $type;
        $this->ETAT              = $etat;
        $this->SAUVEGARDE        = $sauvegarde;
        $this->EXAMEN            = $examen;
        $this->DATE_START        = $date_start;
        $this->DATE_STOP         = $date_stop;
        $this->AUTO_CORRECTION   = $auto_correction;
        $this->COMMENT           = $comment;      
        $this->INTITULE          = $intitule;
        $this->TYPE_REPONSE      = $type_response;    
        $this->GOOD_RESPONSE_TXT = $good_response_txt;
        $this->GOOD_RESPONSE_DATE = $good_response_date;
        $this->GOOD_RESPONSE_NUMBER = $good_response_number;
        $this->INCLUSIVE_TYPE_VALUES = $inclusiveResponsesArray;
        $this->EXCLUSIVE_TYPE_VALUES = $exclusiveResponsesArray;       
        $this->IMAGES               = $IMAGES;
        $this->isChrono            = $isChrono;
        $this->seconds             = $seconds;        
     }       
 }
 class Node{
     public $id;
     public $parent;
     public $text;
     public $data;
     public $isChrono;
     public $seconds;
     function __construct($id,$parent,$text,$data,$isChrono,$seconds){        
         $this->id = $id;
         $this->parent = $parent;
         $this->text = $text;
         $this->data = $data;
         $this->isChrono            = $isChrono;
         $this->seconds             = $seconds;         
     }     
 }
 class Nodes{
     public $nodes;      
 }
/***********************************************************************
 * functions 
 /***********************************************************************/
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
    $tmp = substr($tmp[2], 0, 2) . '/' . $tmp[1] . '/' . $tmp[0];
    return $tmp;    
}
function my_addslashes($val){
    if(!get_magic_quotes_gpc())
        $val = addslashes($val);
    return $val;
}
function cleansePostedArrayVals($postedArrayVals){
    //var_dump($postedArrayVals);
    $newArray = array();
    foreach($postedArrayVals as $key => $value){
       $newArray[my_addslashes(trim($key))] = my_addslashes(trim($value));
    }
    return $newArray;
}
function cleansePostedArrayValsAndConcatRespIdIntoString($postedArrayVals){

    $newConcatIds = "";
    if($postedArrayVals['isSelectedAsAnswer'] == 'true'){
        $newConcatIds = my_addslashes(trim($postedArrayVals['id_qcm_response']));
    }
          
    return $newConcatIds;
}
function sessionExists($origin){

   if(!isset($_SESSION['auth'])){
      return false;
   }else{
      $auth = unserialize($_SESSION['auth']);
         //var_dump($auth->origine);
      if($auth->origine == $origin){ //prevent cross session
         return true;
      }else{
         $_SESSION['auth'] = null; //destroy it
         return false;
      }
   }
}
function buildNodes($service) { //123
    global $workflow;
    //var_dump("eeeeee- buildNodes");die();
    //var_dump("track 1");
    $filterOnIdQuestionnaire = false;
    $idQuestionnaire = "";
    //var_dump($service);die();
    if("GET_SELECTED_QUESTIONNAIRE" == $service){
        $filterOnIdQuestionnaire = true;
        $idQuestionnaire = $_POST['id_questionnaire'];
    }
    if("GET_SELECTED_QUESTIONNAIRE_TO_RUN" == $service){
    
        //var_dump("track 1");
    
        $filterOnIdQuestionnaire = true;
        $idQuestionnaire = $_POST['id_questionnaire'];
        /**
         * save the timestamp
         */
         //Faire la function util....qui sauve le timestamp dans la session
         $auth = unserialize($_SESSION['auth']); 
         $idAuthorArray = $workflow->modelInstance->getTicketData($auth->ticket);
         $idAuthor = $idAuthorArray['id_author'];        
         $timestamp 
            = $workflow->modelInstance->getQuestionnaireTimestamp($idAuthor,$idQuestionnaire);
         //store the ref timestamp
         $auth->timestamp        = $timestamp;
         $auth->id_author        = $idAuthor;
         $auth->id_questionnaire = $idQuestionnaire;
         
         $_SESSION['auth'] = serialize($auth);
         $auth = unserialize($_SESSION['auth']); 
         //Faire la function util....qui compare les timestamps 
         //injecter un test à chaque save......
         //var_dump("auth");
         //var_dump($auth);
    }    
    
    //the mocker
    $isMocker = false;
    /**
     * call for tree data
     */
    if( //will disapear ...
        count($workflow->mvcInstance->params) > 0                          && 
        (array_key_exists("id_user", $workflow->mvcInstance->params))      && //param must exists
        intval($workflow->mvcInstance->params["id_user"]) > 0                 //param must be > 0
     )
    {//if 1 ->
        //var_dump("track 3");
        //var_dump("eeeeeeeeeeeeeeeeeeeeeeee");
        //$resutls = $workflow->modelInstance->geTree($workflow->mvcInstance->params["id_user"]);
        /**
         * build questionnaires nodes
         */
        $tree_nodes = new Nodes();
        /**
         * to do do this from DB
         */
        //build exclu possible answers
        $exclusiveResponsesArray = array();
        //build inclu possible answers
        $inclusiveResponsesArray = array();
        
        /**
         * ask DB
         */
        $auth = unserialize($_SESSION['auth']);
        //var_dump("t1");
        //var_dump($auth->ticket);
        //detrminer en fonction du ticket le questionnaire dispo...
        //mais où est ce ticket
        //capturer :
        // 1- id author
        // 2- id questionnaire
        //capturer ces info au moment du login 
        //keys <- capturer l id du ticket
        
        if($idQuestionnaire == null || $idQuestionnaire == ""){
        
            $ticketData = $workflow->modelInstance->getTicketData($auth->ticket);
            $ticket_id = $ticketData['id_ticket'];
            $questionaireId 
               = $workflow->modelInstance
                  ->getTicketedQuestionnaires($ticket_id);
            $idQuestionnaire = $questionaireId[0]; //['id_questionnaire'];
        }
        
        //q_key2questionnaire <- avc l id du ticket g celui du questionnaire
        //q_users_examens2key <- je verifie si le user est bien lié au ticket
        
        
        
        $id_author = $workflow->modelInstance->getAuthorId($idQuestionnaire);
        //var_dump("id quest");
        //var_dump($idQuestionnaire);
        //var_dump("id author");
        //var_dump($id_author);
        //We want just questionnaire(s) stuff
        if("GET_SELECTED_QUESTIONNAIRE" == $service){
            $resutls = $workflow->modelInstance->geTree(
               $id_author,
               //$workflow->mvcInstance->params["id_user"], //4
               $idQuestionnaire,
               $auth->ticket);
            
        //We want one questionnaire and all questions associated
        }else if("GET_SELECTED_QUESTIONNAIRE_TO_RUN" == $service)
        { //momomock
            
            //var_dump("track 1.1");
            //var_dump($auth);
            // LFG-gpN-s8o-xuy-90e-B7n6
            // u1
            
            //var_dump("---> momomock");
            
            /**
             * determine if is a mocker reader
             */
            $resutls = null;
            $isMocker = false;
            if($auth->mocker == null){
               $isMocker = false;
            }else{
               if($auth->mocker == "on"){
                  $isMocker = true;
               }
            }
            
            if(!$isMocker){
               $id_author = 
	              $workflow->modelInstance->
		               getAuthorIdFromExamKey($auth->ticket);
            }    
            
            if($isMocker)
            {
                //var_dump("A MOCKER");
                $resutls = 
                    $workflow->modelInstance->geTreeAndQuestions(
                       $id_author, //$workflow->mvcInstance->params["id_user"],
                       $idQuestionnaire);
                       //var_dump($workflow->mvcInstance->params["id_user"]);
                       //var_dump($idQuestionnaire);
                       //var_dump($auth->ticket);  
                       //var_dump($resutls);
            }else{
            //get an exam and NOT a questionnaire
                //var_dump("NOT A MOCKER");
                $resutls = 
                    $workflow->modelInstance->geTreeAndQuestions(
                       $id_author, //$workflow->mvcInstance->params["id_user"],
                       $idQuestionnaire,
                       false
                       //...
                       // <- find the corresponding exam (id is the parent)
                       //...
                       );               
            }
            /*  20160316csi 
            GARDER en session le type de exam(mock/Exam) + id camp
            histoire de pouvoir sauvegarder correctement les responses...
            detemine 
            */
            $_SESSION['auth'] = serialize($auth);
            
            /*
               //to do: modify struct users pour
               //       ajouter mock_reader
               //add in gui this field
               // 654321
               si user can read mock exam
               {
                  si mok exam exists for this questionnaire
                  {
                     display Mok
                     $workflow->modelInstance->geTreeAndQuestions + paramsnew1
                     STOP ==  fill up results1
                        $resutls = 
                        $workflow->modelInstance->geTreeAndQuestions(
                           $id_author, //$workflow->mvcInstance->params["id_user"],
                           $idQuestionnaire);                     
                  }
               }
               si runnable Exam exists
               {
                  display Exam
                  $workflow->modelInstance->geTreeAndQuestions + paramsnew2
                  STOP ==  fill up results2
                    $resutls = 
                    $workflow->modelInstance->geTreeAndQuestions(
                       $id_author, //$workflow->mvcInstance->params["id_user"],
                       $idQuestionnaire);                  
                       }
               STOP
            */
            //$resutls = 
            //$workflow->modelInstance->geTreeAndQuestions(
            //   $id_author, //$workflow->mvcInstance->params["id_user"],
            //   $idQuestionnaire);
               //var_dump($workflow->mvcInstance->params["id_user"]);
               //var_dump($idQuestionnaire);
               //var_dump($auth->ticket);  
               //var_dump($resutls);
            
        //get all questionnaires:  'GET_ALL_QUESTIONNAIRES'
        }else{
        
            $resutls = null;
            $isMocker = false;
            if($auth->mocker == null){
               $isMocker = false;
            }else{
               if($auth->mocker == "on"){
                  $isMocker = true;
               }
            }
            
            $resutls = 
            $workflow->modelInstance->geTree(
               $id_author,
               //$workflow->mvcInstance->params["id_user"], //<--- replace by ticket + current user id
               $idQuestionnaire, //<--- replace by ticket + current user id
               $auth->ticket, //007,
               $isMocker
               );
        }       
        
        //because... no ticket ?
        if($resutls == null){
           return null;
        }  
        //$auth = unserialize($_SESSION['auth']);
        //var_dump("toto");
        //var_dump($resutls);
        //var_dump($resutls["questionnaires"]);
        
        /**
         * feed instances
         */
        while($resQuestionnaire = mysqli_fetch_array($resutls["questionnaires"], MYSQLI_ASSOC))
        {
            //var_dump("rrrrrrrrrrrr");
            
            /**
             * catch possible answers for the current question
             */
            $poss_answ_resutls = 
                    $workflow->modelInstance->getPossibleAnswersForAQuestion(
                            $id_author,
                            //$workflow->mvcInstance->params["id_user"],
                            $resQuestionnaire["id"],
                            $isMocker);
                               
            $exclusiveResponsesArray = array();
            $inclusiveResponsesArray = array();
            
            $answeResutls = null;   
            $answeResutl = null;
            if($resQuestionnaire["node_type"] == "QUESTION"){
            
                /**
                 * install un array of selected ids, si il est dedans on selectionns
                 * to do ...
                 */
                $id_questionnaire  = $resQuestionnaire['parent'];
                $id_question       = $resQuestionnaire['id'];
                $id_answering_user = $auth->id; //"2";    
                
                $answeResutls = 
                    $workflow->modelInstance->getExistingAnswer(
                       $id_questionnaire,
                       $id_question,
                       $id_answering_user,
                       $id_author,
                       $isMocker);
                    
                $answeResutl = mysqli_fetch_array($answeResutls, MYSQLI_ASSOC);
                //var_dump($answeResutl);    
                //build the array to content the ids...        
                $concatIds = $answeResutl['rcm'];
                //ce tableau d ids repondus servira a reselectionner les check/radio accordingly
                $concatIdsArray = explode(';',$concatIds);
                while($resPossAnsw = mysqli_fetch_array($poss_answ_resutls["possible_answers"], MYSQLI_ASSOC)){
                    //disp($resPossAnsw,__FILE__);
                    if($id_question == "160" && false){
                       var_dump("BLANK3");
                       var_dump($concatIds);
                       var_dump($resPossAnsw['id']);
                       if(in_array($resPossAnsw['id'], $concatIdsArray)){
                           var_dump("IN");
                       }else{
                           var_dump("OUT");
                       }
                    }
                
                    //EXCLUSIVE
                    if($resPossAnsw['answ_type'] == '4'){
                        $exclusiveResponsesArray[] =  
                                new QCMResponse(
                                    $resPossAnsw['content'],
                                    ($resPossAnsw['is_good_response'] == '1')? true:false,
                                    $resPossAnsw['id'],
                                    //ici je teste si l id est deja sauve dans les reponses du user
                                    (in_array($resPossAnsw['id'], $concatIdsArray)?true:false) //false
                                ); 
                    }
                    //INCLUSIVE 
                    if($resPossAnsw['answ_type'] == '5'){
                        $inclusiveResponsesArray[] =  
                                new QCMResponse(
                                    $resPossAnsw['content'],
                                    ($resPossAnsw['is_good_response'] == '1')? true:false,
                                    $resPossAnsw['id'],
                                    (in_array($resPossAnsw['id'], $concatIdsArray)?true:false) //false
                                );   
                    }         
                }
            }
            
            /**
             * catches all the images link to this node
             */
            $image_group = array();
            $id_questionnaireCorrOriginal = "-1";
            
            $auth = unserialize($_SESSION['auth']);
            //var_dump("session---------->");
            //var_dump($auth);
            //var_dump($isMocker);
            
            if(!$isMocker){
               $id_author = 
                  $workflow->modelInstance->
                       getAuthorIdFromExamKey($auth->ticket);
            }
            
            /*$id_author = 
               $workflow->modelInstance->
                  getAuthorId(
                     ($isMocker)?
                        $resQuestionnaire["id"]:
                        $id_questionnaireCorrOriginal
                  )*/
                  
            //var_dump("id_author---------->");
            //var_dump($id_author);
            
            $res_images = 
                    $workflow->modelInstance->getImages(
                            $id_author,
                            //$workflow->mvcInstance->params["id_user"],
                            $resQuestionnaire["id"],
                            $isMocker);
                            
             //var_dump(    $id_author );
             //var_dump(    $resQuestionnaire["id"] );
             //var_dump(    $res_images );
             
            //extract imgs from db
            while($image = mysqli_fetch_array($res_images, MYSQLI_ASSOC)){
               //var_dump("IN WHILE");
               $uploadedFile = new UploadedFile();
               $uploadedFile->file_url = '../../../upload/' . $id_author . '/' . $image['image_name'];
               $image_group[] = $uploadedFile;
               
            } 
            //var_dump($image_group);
            
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
                            $resQuestionnaire["q_is_chrono"],
                            $resQuestionnaire["q_seconds"]                            
                   );
                            
            /*********************************
            *
            * if type node == QUESTION
            * => 
            * entrer les data answered deja existantes 
            *
            *********************************/
			//die(7);
			//return "---debug---> " . $answeResutl['resp_text'];
			//var_dump($answeResutl);
            if($dataQuestionnaire->TYPE == "QUESTION"){
               if($answeResutl['resp_type'] == 1){
                   $dataQuestionnaire->ANSWERED_RESPONSE_TXT = $answeResutl['resp_text'];
                }else{
                   $dataQuestionnaire->ANSWERED_RESPONSE_NUMBER = $answeResutl['resp_text'];
                }
                $dataQuestionnaire->ANSWERED_RESPONSE_DATE 
                    = convertDateDBToJs($answeResutl['resp_datetime']);//008               
            }
     
            $nodeQuestionnaire = new Node( //$id,$parent,$text,$data){
                    $resQuestionnaire["id"], //$id,
                    $resQuestionnaire["parent"], //$parent,
                    $resQuestionnaire["tree_label"], //$text,
                    $dataQuestionnaire,
                    $resQuestionnaire["q_is_chrono"],
                    $resQuestionnaire["q_seconds"]
                    );
            $tree_nodes->nodes[] = $nodeQuestionnaire;
        }
    }//if 1 <-
    else{
       var_dump("track 4 out");
    }
    //var_dump($tree_nodes);
    return $tree_nodes;
}//fin function

class App2{
    public $jsTreeNodes;
    function __construct($tree_nodes){
        $this->jsTreeNodes = $tree_nodes;
    }     
}
// ask to safe data -> to do  -> make a switch for all of that
// OR implement methods in your framework
// 
class ID{
    public $id;
}
/******************************************************************
 * CUD
 ******************************************************************/
//session 
if(!sessionExists($origin)){
    echo json_encode("session is null");//
    return;
} 
if (isset($_POST['service'])) {
    $flag = $_POST['service'];
    //$auth = unserialize($_SESSION['auth']);
    switch ($flag) {
        /**
         * a question has been d&dropped 
         * => we have to update the position values accordingly
         */
        case 'GET_SELECTED_QUESTIONNAIRE':  //007
            $service = "";
            if(isset($_POST['service'])){
                $service = $_POST['service'];
            }
            echo json_encode(
               new App2(
                 buildNodes((( $service != null)? $service :""))
            ));            
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
        case 'GET_SELECTED_QUESTIONNAIRE_TO_RUN':  //007
            $service = "";
            if(isset($_POST['service'])){
                $service = $_POST['service'];
            }
            
            //var_dump("7.1");
            //var_dump($_POST);
            
            echo json_encode(new App2(
                buildNodes((( $service != null)? $service :""))
            ));            
            break;             
        case 'GET_ALL_QUESTIONNAIRES':
            $service = "";
            if(isset($_POST['service'])){
                $service = $_POST['service'];
            }
            //var_dump("eeeeeeeeeeeeeeeeeeeeeeee");
			//var_dump($service);
			//die("eeeeeeeee");
            echo json_encode(new App2(buildNodes(( //csi4
                ( $service != null)? $service :"" 
            ))));               
            break;  
        case 'SAVE_RESPONSE':  
        
            //test timestamp comparison
            $auth = unserialize($_SESSION['auth']);
            
            $isMocker = false;
            if($auth->mocker == null){
               $isMocker = false;
            }else{
               if($auth->mocker == "on"){
                  $isMocker = true;
               }
            }      
            
            //var_dump("ORIGINAL TIMESTAMP");
            //var_dump($auth->timestamp);
            //$timestamp = date('Y-m-d G:i:s');
            //var_dump($timestamp);
            
            $service = "";
            if(isset($_POST['service'])){
                $service = $_POST['service'];
            }
            echo json_encode(new App2(
                buildNodes((( $service != null)? $service :""))
            ));   
            //$text = $_POST['text'];
            //$intitule = $_POST['intitule'];
            //$auth = unserialize($_SESSION['auth']);
            
            $responsetype = $_POST['responsetype'];  
            $id_question = $_POST['id_question']; 
            $answered_response_txt = $_POST['answered_response_txt']; 
            $answered_response_date = convertDateJsToDB($_POST['answered_response_date']); 
            $answered_response_number = $_POST['answered_response_number']; 
            $qcm_exclu = (isset($_POST['qcmExclu']))?$_POST['qcmExclu']:array();
            $qcm_inclu = (isset($_POST['qcmInclu']))?$_POST['qcmInclu']:array(); 
            $id_user_answering = $auth->id; //'2';
            $id_questionnaire = $_POST['id_questionnaire']; 
      
            /**
             * cleanse EXCLU vals in array
             */
            $globArray = array();
            foreach($qcm_exclu AS $value){
               if($value != ""){
                  $tmp = cleansePostedArrayValsAndConcatRespIdIntoString($value);
                  if($tmp != ""){
                     $globArray[] = $tmp;
                  }
               }
            }
            $qcm_exclu = implode(";", $globArray);
            /**
             * cleanse INCLU vals in array
             */
            $globArray = array();
            foreach($qcm_inclu AS $value){
               if($value != ""){
                  $tmp = cleansePostedArrayValsAndConcatRespIdIntoString($value);
                  if($tmp != ""){
                     $globArray[] = $tmp;
                  }
               }
            }
            $qcm_inclu = implode(";", $globArray);
            $idAuthorArray = $workflow->modelInstance->getTicketData($auth->ticket);
            $idAuthor = $idAuthorArray['id_author'];
            
            //test if questionnaire is activated
            // add the test to see if session tstamp == db timestamp too
            if(!$isMocker){
               $idAuthor = 
	              $workflow->modelInstance->
		               getAuthorIdFromExamKey($auth->ticket);
            }
            //var_dump("-------->idAuthor");
            //var_dump($idAuthor);
            //die();
            
            if($isMocker){
                if
                (
                  !$workflow->modelInstance->isQuestionnaireActivated
                      ($idAuthor,$id_questionnaire,$auth->timestamp)
                )
                {
                   $err_message['error'] = -8;
                   echo json_encode($err_message); 
                   //var_dump("here1");
                   return -1;
                }else{
                   //var_dump("here2");
                }
            }
            //var_dump("tutu");
            //var_dump($id_user_answering);
            $resutls = $workflow->modelInstance->saveResponse(
                $id_user_answering,
                $responsetype,  
                $id_question, 
                $answered_response_txt,
                $answered_response_date, 
                $answered_response_number,
                $qcm_exclu,
                $qcm_inclu,
                $id_questionnaire,
                $idAuthor,
                $isMocker
            );
            //var_dump("BLANK");
            //var_dump($_POST);
            //var_dump($qcm_exclu);
            //var_dump($qcm_inclu);
            //disp("FIN",__FILE__);   
            break;            
        /**
         * asked for an unknown service
         */         
        default:
            echo "UNKNOWS3 SERVICE: " . $flag;
    }    
  return 1;
}