<?php global $workflow; 
//var_dump($workflow);
//die();
/**
 * build a questionnaire
 */      
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Questionnaires</title>
        <!-- favicon -->
        <link rel="icon" href="<?php echo $workflow->href_public; ?>/jsTree/resources/icons/lol.png" />
        <!-- wysiwyg -->
        <link type="text/css" rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jquery-te/jquery-te-1.4.0.css" />
        <link type="text/css" rel="stylesheet" href="<?php echo $workflow->href_public; ?>/css/jquery-te.css" charset="utf-8" >                 
        <!-- jquery -->    
        <script src="<?php echo $workflow->href_public; ?>/jquery/jquery.js"></script> 
        <!-- Bootstrap Latest compiled and minified CSS -->
        <link rel="stylesheet" type="text/css" href="<?php echo $workflow->href_public; ?>/bootstrap/dist/new-css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $workflow->href_public; ?>/bootstrap/dist/css/bootstrap.css">
        <!-- date picker (prob d icones) -->
        <link href="<?php echo $workflow->href_public; ?>/jquery/ui-lightness/jquery-ui.css" rel="stylesheet">
        <script src="<?php echo $workflow->href_public; ?>/jquery/jquery-ui.js"></script>
        <!-- font-awesome -->
        <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/css/font-awesome/css/font-awesome.min.css">                 
        <!-- jsTree (prob images) -->
        <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jsTree/css/style.min.css" /> 
        <style>
            li.jstree-open > a .jstree-icon {background:url("<?php echo $workflow->href_public; ?>/jsTree/resources/icons/awake.png") 0px 0px no-repeat !important;}
            li.jstree-closed > a .jstree-icon {background:url("<?php echo $workflow->href_public; ?>/jsTree/resources/icons/sleep.png") 0px 0px no-repeat !important;}
            li.jstree-leaf > a .jstree-icon {background:url("<?php echo $workflow->href_public; ?>/jsTree/resources/icons/c.png") 0px 0px no-repeat !important;}
       </style>              
       <!-- colorbox (prob image) -->
       <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/colorbox/css/colorbox.css" />  
       <!-- d&d -->
       <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css"> -->
       <!-- custom + tabs + tree -->
       <link rel="stylesheet" type="text/css" href="<?php echo $workflow->href_public; ?>/css/questionnaires_custom.css">      
       <!-- upload image -->
       <!-- <link rel="stylesheet" type="text/css" href="<?php echo $workflow->href_public; ?>/css/upload_image.css">  -->
    </head>
    <body>
    <div class="main">
         <!-- The Main wrapper div starts -->
         <div class="container">
         <!-- header content -->
         <div class="header_title">
            <h1><a style="color:navy;" href="."><i class="fa fa-cogs"></i> Questionnaires builder </a> </h1>            
         </div>
         <div class="link_login_class"><a  href="#" id="id_log_unlog">Log in</a></div>
         <!-- Navigation -->
         <div class="navbar">
           <div class="navbar-inner">
             <div class="container">
               <ul class="nav">
                   <li class="active"><a href="#"><i class="fa fa-cogs"></i> Build a questionnaire</a></li>
               </ul>
               <ul class="nav">
                 <li><a href="<?php echo $workflow->href_public; ?>/exam/build/edit""><i class="fa fa-rocket"></i> Run a questionnaire</a></li>
               </ul>
               <ul class="nav">
                 <li><a href="#"><i class="fa fa-gavel"></i> View results</a></li>
               </ul>                 
             </div>
           </div>
         </div>
         
         <div id="dialog" title="Who the hell are you? ">   
           <div id="my_dialog" class="auth_box" title="Auth">
               <label class="error_message" id="xxx"></label>
               <div class="login_class" >Login</div>             
               <input type="text" name="login" id="login_dialog_from" />
               <div class="password_class" >Password</div>
               <input type="password" name="pass" id="password_dialog_from"  />
            </div>   
         </div>
         
         <input type="hidden" id="hidden_login_dialog_to" />
         <input type="hidden" id="hidden_password_dialog_to" />
         <input type="hidden" id="hidden_origine_dialog_to" />
         
         <!-- jsTree building -->
         <div class="row"> <!-- new -->
             <div class="span12">
                <div class="qc_tree">
                     <i class="fa fa-user"></i> 
                     Id: 
                     <span id="logged_user_data" class="expli"> </span> 
                </div>
             </div>
         </div>
         <br/>
         <!-- jsTree building -->
         <div class="row"> <!-- new -->
             <div class="span12">
                <div class="qc_tree">
                     <i class="fa fa-tree"></i> Vos questionnaires <span class="expli">  ( <i class="fa fa-info-circle"></i>  Cliquez sur un noeud pour faire apparaître sa définition <i class="fa fa-expand"> ) </i></span> <i class="fa fa-hand-o-down"></i>
                </div>
                <div id="jstree_demo" class="demo" style="margin-top:1em; height: auto">
                    <ul id="jstree_demo_ul" class="ui-sortable">
                    </ul>
                </div>			
             </div>
         </div>
         
         <!-- tree buttons add quest/questionnaire/remove node -->
         <div class="row">
             <div class="span12">
                 <div class="buttons_tree">
                         <button type="button"  class="btn btn-success" id="id_btn_create_questionnaire"><i class="fa fa-hand-o-up"></i> Create a questionnaire</button>
                         <button type="button"  class="btn btn-success" id="id_btn_create_question" ><i class="fa fa-hand-o-up"></i> Create a question</button>
                         <button type="button"  class="btn btn-danger" id="id_btn_delete_node"><i class="fa fa-hand-o-up"></i> Delete</button>
                 </div>				
             </div>
         </div>
         <!-- tab management current questionnaire/question -->
         <div id="tab_wrapper">    
             <div class="row">
                 <div class="span12">         
                     <ul class='tabs'>
                             <li><a id="id_questionnaire" href='#tab1'><i class="fa fa-pencil-square-o"></i> Questionnaire sélectionné <span id="id_questionnaire_label"></span><i class="fa fa-hand-o-down"></i></a></li>
                             <li><a id="id_question" href='#tab2'><i class="fa fa-pencil-square-o"></i> Question sélectionnée <span id="id_question_label"></span><i class="fa fa-hand-o-down"></i></a></li>
                     </ul>
                     <!-- QUESTIONNAIRE -->
                     <div id='tab1'>
                         <!-- USERS CRUDS -->
                         <div class="questionnaire_control_users_title">
                            <b>Configure current questionnaire</b> <br/><br/>
                            Idée<br/><br/>
                            1.0- 'Questionnaire editable' -> 'non editable' => delete all fake responses.<br/>
                            1.1- 'non editable' -> 'Questionnaire editable' => delete all fake responses.<br/><br/>
                            2.0- 'Create a campaign' (clickable si pas de campagne active pour ce questionnaire) -> create new campagne non modifiable.<br/>
                            2.1- 'Seulement une campagne active par questionnaire en même temps.<br/><br/>
                            3.0- 'Report view' 1--n 'Campaign results' .<br/>
                            <label class="error_users_message" id="xxxxx4"></label>
                         </div>                     
                         <div class="questionnaire_controls">
                             <!-- label -->
                             <div class="qc_label"><i class="fa fa-hand-o-up"></i> Tree label</div>
                             <div class="my_icons"><i class="fa fa-tree"></i></div>
                             <div class="qc_input"><input id="id_questionnaire_name" style="height:30px;" type="text"></div>
                             <!-- etat -->
                             <div class="qc_label"><i class="fa fa-hand-o-right"></i> Etat</div>
                             <div class="my_icons"><i class="fa fa-apple"></i></div>
                             <div class="qc_input">
                                 <select class="my_select" id="id_questionnaire_etat" >
                                    <option value="1">Questionnaire editable</option>
                                    <option value="2">Questionnaire read only, simulation exam running</option>
                                 </select>
                             </div>
                             <!-- sauvegarde -->
                             <div class="qc_label"><i class="fa fa-hand-o-right"></i> Sauvegarde</div>
                             <div class="my_icons"><i class="fa fa-floppy-o"></i></div>
                             <div class="qc_input">
                                 <select class="my_select" id="id_questionnaire_sauvegarde">
                                 <option value="1">Defaut</option>
                                 <option value="2">Automatique</option>
                                 <option value="3">Manuelle</option>
                                 <option value="4">Mixte</option>
                                 </select>
                             </div>
                             <!-- examen -->
                             <div class="qc_label"><i class="fa fa-hand-o-right"></i> Examen</div>
                             <div class="my_icons"><i class="fa fa-random"></i></div>
                             <div class="qc_input">
                                 <select class="my_select" id="id_questionnaire_examen">
                                 <option value="1">Ne pas mélanger</option>
                                 <option value="2">Mélanger les questions</option>
                                 <option value="3">Mélanger les réponses</option>
                                 <option value="4">Mélanger les deux</option>
                                 </select>
                             </div>                                   
                             <!-- dates -->
                             <div class="qc_label"><i class="fa fa-hand-o-right"></i> Dates</div>
                             <div class="my_icons"><i class="fa fa-calendar-o"></i></div>
                             <div class="qc_input">
                                <input type="text" id="datepickerstart" style="width:88px;height:30px;">   
                                <span class="perso-arrow"><i class="fa fa-arrows-h"></i></span>
                                <input type="text" id="datepickerstop"  style="width:88px;height:30px;">                                   
                             </div>  
                             <!-- chrono -->
                             <div class="qc_label"><i class="fa fa-hand-o-right"></i> Chrono <i>[sec]</i></div>
                             <div class="my_icons"><i class="fa fa-clock-o"></i></div>
                             <div class="qc_input_label_chrono_cbx">
                                <input type="checkbox" id="id_chrono_chckbox">  
                             </div>
                             <div class="qc_input_label_chrono">
                                <input type="text" id="id_chrono_value" style="height:30px;width:100px;" >                                   
                             </div>                               
                             <!-- autocorrection -->
                             <div class="qc_label"><i class="fa fa-hand-o-right"></i> Auto-correction</div>
                             <div class="my_icons"><i class="fa fa-bolt"></i></div>
                             <div class="qc_input">
                                <input type="checkbox" id="auto_correction" value="auto_correction"> 
                             </div>                                    

                         </div>    
                         <div class="questionnaire_controls">
                              <!-- commentaire --> 
                             <div class="qc_label"><i class="fa fa-hand-o-right"></i> Commentaire</div>
                             <div class="my_icons"><i class="fa fa-comment"></i></div>
                             <div class="qc_input"  ><textarea id="id_questionnaire_comment"></textarea></div>
                         </div>     
                         <div class="save_questionnaire">
                            <!-- btn save --> 
                            <button 
                            type="button"  
                            class="btn btn-success" 
                            id="id_btn_save_questionnaire" >
                            <i class="fa fa-hand-o-left"> </i> Save</button>
                         </div>  
      
                         
                         <!-- CAMPAIGN MANAGEMENT -->
                         <div class="questionnaire_control_users_title">
                            <i class="fa fa-link"></i> Campaign management 
                            <label class="error_users_message" id="campaign_management_Error"></label>
                        </div>
                         <div class="save_questionnaire">
                            <!-- btn archiver --> 
                            <button 
                            type="button"  
                            class="btn btn-warning" 
                            id="id_btn_archive_questionnaire" >
                            <i class="fa fa-archive"> </i> Générer une campagne à partir de ce questionnaire</button>
                         </div>   
                         <div class="qc_input"><input id="id_campaign" style="height:30px;" type="text" /></div>
                         <!-- history -->
                         <div class="camp_main" >
                         
                             <!-- status -->
                             <div class="camp_main_title" >
                                Campaigns
                             </div>    
                             <div class="qc_input_campaigns">
                                 <select class="my_select" id="id_old_campaigns">
                                 </select>
                             </div>                                
                             <div class="camp_main_title" >
                                status
                             </div>    
                             <div class="qc_input_campaign_status">
                                 <select class="my_select_status" id="id_old_campaign_status">
                                    <option value="-1">Select campaign status</option>
                                    <option value="1">Active</option>
                                    <option value="2">Inactive</option>
                                 </select>
                             </div>     
                             <div class="main_btn_update_camp_status" >
                                 <div class="btn_update_camp_status">
                                    <button 
                                        type="button"  
                                        class="btn btn-success" 
                                        id="id_btn_update_campaign" >Update status</button>
                                        
                                    <button 
                                        type="button"  
                                        class="btn btn-danger" 
                                        id="id_btn_delete_campaign" >Delete campaign</button>                                        
                                 </div>                                    
                             </div>                                    
                         </div>
                                                
                         <!-- USERS MANAGEMENT TO TICKET -->
                         <div class="questionnaire_control_users_title">
                            <i class="fa fa-link"></i> Link Users to a Ticket. Refresh 
                            <a href="" id="id_refresh_user_list" ><i class="fa fa-refresh"></i></a>
                            <label class="error_users_message" id="xxxxx2"></label>
                        </div>                         
                         <div class="questionnaire_control_users" >
                            <div class="list_users_left">
                                 <!-- etat -->
                                 <div class="qc_label"><i class="fa fa-users"></i> Users</div>
                                 <!-- <div class="my_icons"><i class="fa fa-apple"></i></div> -->
                                 <div class="qc_input">
                                     <select class="my_select" id="id_list_exam_users" multiple >
                                     </select>
                                 </div>                            
                            </div>
                            <div class="list_users_right">
                                 <!-- etat -->
                                 <div class="qc_label">
                                    <a href="" id="id_push_users_to_right" >
                                       <i class="fa fa-arrow-circle-right"></i>
                                    </a> 
                                 </div>
                                 <div class="my_icons">
                                     <a href="" id="id_push_users_to_left" >
                                        <i class="fa fa-arrow-circle-left"></i>
                                     </a>
                                 </div>
                                 <div class="qc_input">
                                     <select class="my_select" id="id_list_exam_users_linked" multiple >
                                     </select>
                                     <span class="synchro_users_ticket">
                                        <!--
                                           <a id="a_sync_users"><i class="fa fa-bolt"></i> </a> Updade your Users changes
                                        -->
                                        <button 
                                            id="a_sync_users"
                                            type="button"  
                                            class="btn btn-success" >
                                            Update
                                        </button> 
                                     </span>
                                 </div>   
                            </div>                            
                         </div>
                         
                         <!-- USERS CRUDS -->
                         <div class="questionnaire_control_users_title">
                            <i class="fa fa-user"></i> Manage User 
                            <label class="error_users_message" id="xxxxx"></label>
                         </div>
                         <div class="questionnaire_control_users" >
                         
                             <!-- label -->
                             <div class="user_log" >
                                 <div class="qc_label"><i class="fa fa fa-user"></i> Login</div>
                                 <!-- <div class="my_icons"><i class="fa fa-tree"></i></div> -->
                                 <div class="qc_input"><input id="id_user_name" style="height:30px;" type="text"></div>
                             </div>
                             
                             <!-- label -->
                             <div class="user_log_mock" >
                                 <div class="qc_input_mock">
                                    <input type="checkbox" id="id_mock_reader_chckbox"> 
                                 </div>                             
                                 <div class="qc_label"><i class="fa fa-bug"></i> Mock reader</div>
                                 <!-- <div class="my_icons"><i class="fa fa-tree"></i></div> -->

                             </div>       
                             
                             <!-- label -->
                             <div class="user_log" >
                                 <div class="qc_label"><i class="fa fa-eye"></i> Password</div>
                                 <!-- <div class="my_icons"><i class="fa fa-tree"></i></div> -->
                                 <div class="qc_input"><input id="id_questionnaire_pass" style="height:30px;" type="text" /></div>
                             </div>
                             <!-- label -->
                             <div class="user_log" >
                                 <div class="qc_label"><i class="fa fa-envelope-o"></i> Mail</div>
                                 <!-- <div class="my_icons"><i class="fa fa-tree"></i></div> -->
                                 <div class="qc_input"><input id="id_user_mail" style="height:30px;" type="text"/></div>
                             </div>         
                             <!-- save/udate -->
                             <div class="user_log" >
                                 <div class="qc_label"><i class="fa fa-floppy-o"></i> Save/Update</div>
                                 <!-- <div class="my_icons"><i class="fa fa-tree"></i></div> -->
                                 <div class="qc_input_mock_move">    
                                        <!-- update -->
                                        <button 
                                            id="id_btn_user_update"
                                            type="button"  
                                            class="btn btn-success" >
                                            Update
                                        </button> 
                                        <!-- new -->
                                        <button 
                                            id="id_btn_user_new"
                                            type="button"  
                                            class="btn btn-inverse" >
                                            New
                                        </button>    
                                        <!-- delete -->
                                        <button 
                                            id="id_btn_user_delete"
                                            type="button"  
                                            class="btn btn-danger" >
                                            Delete
                                        </button>                                          
                                 </div>
                                 <!-- hidden id user selected id_user_name -->
                                 <input type="hidden" name="hidden_id_selected_user" id="hidden_id_selected_user" value="">
                             </div>                              
                         </div>        
                         
                         <!-- TICKET CRUDS -->
                         <div class="questionnaire_control_users_title">
                            <i class="fa fa-ticket"></i> Manage Ticket
                            <label class="error_users_message" id="xxxxx3"></label>
                         </div>
                         <div class="questionnaire_control_users" >
                             <!-- label -->
                             <!--
                             <div class="user_log_label" >
                                 <div class="qc_label"><i class="fa fa-pencil"></i> Label </div>
                                 <div class="qc_input_label">
                                    <input id="id_questionnaire_ticket_label" style="height:30px;" type="text" />
                                 </div>
                                 <div class="qc_label_button">
                                      <button 
                                        id="id_btn_generate_unique_ticket_label"
                                        type="button"  
                                        class="btn btn-success" >
                                        Save label
                                     </button>                                   
                                 </div>
                             </div>       -->                  
                             <!-- label -->
                             <div class="user_log_ticket" >
                                 <div class="qc_label"><i class="fa fa fa-ticket"></i> Ticket</div>
                                 <!-- <div class="my_icons"><i class="fa fa-tree"></i></div> -->
                                 <div class="qc_input_ticket_generator">
                                    <input disabled id="id_questionnaire_unique_ticket" style="height:30px;" type="text">
                                 </div>
                                 <div class="inside_ticket" >
                                    <!-- <i class="fa fa-arrow-circle-up"></i> -->
                                    <button 
                                        id="id_btn_generate_unique_ticket"
                                        type="button"  
                                        class="btn btn-warning" >
                                        <i class="fa fa-bolt"></i> Update Unique Ticket for this Questionnaire
                                    </button>  
                                    <span id="id_unique_tiket_display"></span>
                                 </div>                                 
                             </div>
                             <!-- label -->
                             <!-- 
                             <div class="qcm_btn_ticket"> &nbsp;
                                <i class="fa fa-arrow-circle-left"></i>
                                <button 
                                    id="id_btn_add_qcm_incl"
                                    type="button"  
                                    class="btn btn-warning" >
                                    <i class="fa fa-bolt"></i> Generate Unique ticket
                                </button>     &nbsp;
                             </div>  -->                        
                         </div> 
                         
                     </div>
                     
                     <!-- QUESTION -->
                     <div id='tab2'>
                         <div class="question_controls">
                              <!-- label --> 
                             <div class="qc_label"><i class="fa fa-hand-o-up"></i> Tree label</div>
                             <div class="my_icons"><i class="fa fa-tree"></i></div>
                             <div class="qc_input"><input id="id_question_name" style="height:30px;" type="text"></div>
                             <!-- intitule question --> 
                             <div class="qc_label"><i class="fa fa-hand-o-down"></i> Question</div>
                             <div class="my_icons"><i class="fa fa-question-circle"></i></div>
                             <div class="qc_input_question">
                                <textarea class="editor" name="id_question_intitule" id="id_question_intitule"></textarea>
                             </div>
                             <!-- images liees a la question -->
                             <!-- type de reponse --> 
                             <div class="qc_label"><i class="fa fa-hand-o-right"></i> Type réponse</div>
                             <div class="my_icons"><i class="fa fa-sitemap"></i></div>
                             <div class="qc_input">
                                 <select class="my_select" id="id_question_type_reponse">
                                     <option value="1">Texte</option>
                                     <option value="2">Nombre</option>
                                     <option value="3">Date</option>
                                     <option value="4">QCM exclusives</option>
                                     <option value="5">QCM inclusives</option>
                                     <option value="6">Conditionnelles</option>
                                 </select>
                             </div>  
                             
                             <!-- text good response --> 
                             <div class="good_answer_text_wrapper">
                                <div class="qc_label_good_reponse"><i class="fa fa-hand-o-right"></i> Good answer</div>
                                <div class="my_icons_good_response"><i class="fa fa-pencil"></i></div>
                                <div class="qc_good_response">
                                   <textarea class="editor" name="id_good_answer" id="id_good_answer"></textarea>
                                </div>   
                                <!-- weight -->
                                <div class="qc_label_good_reponse_points"><i class="fa fa-hand-o-right"></i> Points réponse</div>
                                <div class="my_icons_good_response_points"><i class="fa fa-gavel"></i></div>
                                <div class="qc_input">
                                   <input type="text" id="weight_good_answer_text" style="width:88px;height:30px;">                                    
                                </div>                                 
                             </div>
                             
                             <!-- dates -->
                             <div class="good_answer_date_wrapper">
                                <div class="qc_label_good_reponse"><i class="fa fa-hand-o-right"></i> Good answer</div>
                                <div class="my_icons_good_response"><i class="fa fa-calendar-o"></i></div>
                                <div class="qc_input">
                                   <input type="text" id="date_picker_good_answer" style="width:88px;height:30px;">                                    
                                </div>    
                                <!-- weight -->
                                <div class="qc_label_good_reponse_points"><i class="fa fa-hand-o-right"></i> Points réponse</div>
                                <div class="my_icons_good_response_points"><i class="fa fa-gavel"></i></div>
                                <div class="qc_input">
                                   <input type="text" id="weight_good_answer_date" style="width:88px;height:30px;">                                    
                                </div>                                   
                             </div>
                             
                             <!-- number -->
                             <div class="good_answer_number_wrapper">
                                <div class="qc_label_good_reponse"><i class="fa fa-hand-o-right"></i> Good answer</div>
                                <div class="my_icons_good_response"><i class="fa fa-calculator"></i></div>
                                <div class="qc_input">
                                   <input type="text" id="number_good_answer" style="width:88px;height:30px;">                                    
                                </div>    
                                <!-- weight -->
                                <div class="qc_label_good_reponse_points"><i class="fa fa-hand-o-right"></i> Points réponse</div>
                                <div class="my_icons_good_response_points"><i class="fa fa-gavel"></i></div>
                                <div class="qc_input">
                                   <input type="text" id="weight_good_answer_number" style="width:88px;height:30px;">                                    
                                </div>                                 
                             </div>
                             
                             <!-- INCLUSIVE MULTIPLE qcm d&d --> 
                             <div class="good_answer_qcm_cbx_wrapper">
                                <div class="qc_label_response_qcm_inclusive"><i class="fa fa-hand-o-right"></i> Multiple answers</div>
                                <div class="my_icons_good_response"><i class="fa fa-check-square-o"></i></div>
                                <div class="q_qcm">
                                    <div class="qcm_incl">
                                       <ul id="sortable-multiple">
                                          <!-- 
                                          <li class="ui-state-default">
                                              <div class="qcm_inside_li_cbox">
                                              <input type="checkbox" name="option1" value="Milk"> 
                                              </div>
                                               <textarea class="txta_custom" >QCM inc</textarea>
                                               <div class="delete_btn">
                                                    <button 
                                                         type="button"  
                                                         class="btn btn-danger delete_btn_class" >
                                                         <i class="fa fa-times"></i>
                                                    </button>  
                                               </div>                                              
                                          </li>  -->                                                
                                       </ul>
                                    </div>
                                    <div class="qcm_btn">
                                        <button 
                                            id="id_btn_add_qcm_incl"
                                            type="button"  
                                            class="btn btn-inverse" >
                                            <i class="fa fa-check-square-o"></i>
                                        </button>    
                                    </div>
                                </div>  
                             </div>
                             
                             <!-- EXCLUSIVE UNIQUE radio qcm d&d --> 
                             <div class="good_answer_qcm_radio_wrapper">
                                <div class="qc_label_response_qcm_exclusives"><i class="fa fa-hand-o-right"></i> Unique answer</div>
                                <div class="my_icons_good_response"><i class="fa fa-dot-circle-o"></i></div>
                                <div class="q_qcm">
                                    <div class="qcm_exc" >
                                       <ul id="sortable-mono" >
                                          <!--
                                          <li class="ui-state-default">
                                              <div class="qcm_inside_li_cbox">
                                              <input type="radio" name="option1" value="Milk"> 
                                              </div>
                                              <textarea class="txta_custom" >QCM exc</textarea>
                                               <div class="delete_btn">
                                                    <button 
                                                         type="button"  
                                                         class="btn btn-danger delete_btn_class" >
                                                         <i class="fa fa-times"></i>
                                                    </button>  
                                               </div>                                              
                                          </li>     -->                                                                          
                                       </ul>
                                    </div>
                                    <div class="qcm_btn">
                                        <button 
                                            id="id_btn_add_qcm_excl"
                                            type="button"  
                                            class="btn btn-inverse" >
                                            <i class="fa fa-dot-circle-o"></i>
                                        </button>    
                                    </div>                                    
                                </div>          
                            </div>   
                             
                             <!-- conditionnal -->
                            <div class="good_answer_conditionnal_wrapper">
                                <div class="qc_label_good_reponse"><i class="fa fa-hand-o-right"></i> Good answer</div>
                                <div class="my_icons_good_response"><i class="fa fa-plug"></i></div>
                                <div class="qc_input">
                                   <input type="text" id="conditionnal_good_answer" style="width:88px;height:30px;">                                    
                                </div>                                
                            </div>                             
                         </div>
                         
                         <div class="save_question">
                            <!-- btn save --> 
                            <button type="button"  class="btn btn-success" id="id_btn_save_question" ><i class="fa fa-hand-o-left"></i> Save </button>
                         </div>
                     </div>

                     <!-- UPLOAD IMAGES -->
                      <div class="qc_image">
                         <!-- title -->
                         <i class="fa fa-camera"></i> 
                         <span class="expli" > 
                             Associer des images 
                         </span> 
                         <i class="fa fa-hand-o-up"></i>
                     </div>
                     
                     <div id='tab3' >
                         <div class="question_controls">   
                            <div class="image_container">
   
                            </div>
                            <div id="id_image_boxes" class="image_boxes" >                            
                                <form id="uploadimage" action="" method="post" enctype="multipart/form-data" >
                                    <div id="selectImage" class="selectImage">
				                        <div class="text_image" >
                                            <div class="text_image_b1">Select Your Image </div>
                                            <div class="text_image_b2"><input type="file" name="file" id="file" required=""/></div>
				                            <div class="text_image_b3"><input class="btn btn-success" type="submit" value="Upload" class="submit"/></div>
                                        </div> 
                                    </div>
                                    <div id="image_preview" class="image_preview" >
                                       <img id="previewing" src="noimage.png" />
                                    </div>                                 
                                    
                                    <input type="hidden" name="upload_image" value="upload_image_ok" />
                                    <input type="hidden" id="id_current_node_type" name="current_node_type" value="" />
                                    <input type="hidden" id="id_current_node_id"   name="current_node_id"   value="" />
			                   </form>	
                               <div class="temp_message" id="temp_message"></div>
                               <div class="temp_message2" id="temp_message2"></div>
                          </div>
                          
                       </div>       
                   </div>
                   
             </div> 
         </div>
         <!-- Footer Section -->
         <div class="row">
             <div class="trans_letters">
                     <!-- meet our clients --> 
                     <div class="span4">
                             <h4 class="muted text-center"><i class="fa fa-thumb-tack"></i> Meet Our Clients</h4>
                             <div class="under_text">
                                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>						
                                <a href="#" class="btn"><i class="icon-user"></i> Our Clients</a>
                             </div>
                     </div>
                     <!-- Know Our Employees --> 
                     <div class="span4">
                             <h4 class="muted text-center"><i class="fa fa-thumb-tack"></i> Know Our Employees</h4>
                             <div class="under_text">
                                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>

                                <a href="#" class="btn btn-success"><i class="icon-star icon-white"></i> Our Employees</a>
                             </div>
                     </div>
                     <!-- Reach Us --> 
                     <div class="span4">
                             <h4 class="muted text-center"><i class="fa fa-thumb-tack"></i> Reach Us</h4>
                             <div class="under_text">
                                <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>
                                <a href="#" class="btn btn-info">Contact Us</a>
                             </div>
                     </div>
             </div>
         </div>
         <!-- Copyright Area -->
         <hr>
         <div class="footer">
             <span class="expli"><p>&copy; Open Web 2015</p></span>
         </div>
     </div>     
    </body> <!-- end class main -->
    
    <!-- jsTree -->
    <script src="<?php echo $workflow->href_public; ?>/jsTree/dist/jstree.min.js"></script>     
    <!-- wysywig -->
    <script src="<?php echo $workflow->href_public; ?>/jquery-te/jquery-te-1.4.0.min.js"></script>        
    <!-- colorbox -->
    <script src="<?php echo $workflow->href_public; ?>/colorbox/jquery.colorbox.js"></script>  
    
    <!-- perso -->
    <script src="<?php echo $workflow->href_public; ?>/js/modules/questionnaires/questionnaires1.js"></script>
    <!-- upload image -->
    <script src="<?php echo $workflow->href_public; ?>/js/modules/questionnaires/upload_image.js"></script>
</html>
