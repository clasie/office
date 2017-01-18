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
       <!-- colorbox (prob image) -->
       <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/colorbox/css/colorbox.css" />    
       <!-- d&d -->
       <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css"> -->
       <!-- custom + tabs + tree -->
       <link rel="stylesheet" type="text/css" href="<?php echo $workflow->href_public; ?>/css/admin_custom.css">      
       <!-- upload image -->
       <!-- <link rel="stylesheet" type="text/css" href="<?php echo $workflow->href_public; ?>/css/upload_image.css">  -->
    </head>
    <body>
    <div class="main">
         <!-- The Main wrapper div starts -->
         <div class="container">
         <!-- header content -->
         <div class="header_title">
            <h1><a style="color:navy;" href="."><i class="fa fa-key"></i> Questionnaires Admin </a> </h1>            
         </div>
         <div class="link_login_class"><a  href="#" id="id_log_unlog">Log in</a></div>
         
         <!-- Navigation -->
         <div class="navbar">
           <div class="navbar-inner">
             <div class="container">
               <ul class="nav">
                   <li ><a href="#"><i class="fa fa-cogs"></i> Build a questionnaire</a></li>
               </ul>
               <ul class="nav">
                 <li><a href="<?php echo $workflow->href_public; ?>/exam/build/edit""><i class="fa fa-rocket"></i> Run a questionnaire</a></li>
               </ul>
               <ul class="nav">
                 <li><a href="#"><i class="fa fa-gavel"></i> View results</a></li>
               </ul>        
               <ul class="nav">
                 <li class="active"><a href="#"><i class="fa fa-key"></i> Admin</a></li>
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

         <!-- tab management current questionnaire/question -->
         <div id="tab_wrapper">    
             <div class="row">
                 <div class="span12">         
                     
                     <!-- ADMIN -->
                     <div id='tab1' >
                     
                         <!-- USERS MANAGEMENT TO TICKET -->
                         <div class="questionnaire_control_users_title">
                            <i class="fa fa-users"></i>  Authors
                            <a href="" id="id_refresh_user_list" ><i class="fa fa-refresh"></i></a>
                            <label class="error_users_message" id="xxxxx2"></label>
                        </div>                         
                        <div class="questionnaire_control_users" >
                            <div class="list_users_left">
                                 <!-- etat -->
                                 <div class="qc_label"><i class="fa fa-users"></i> Users</div>
                                 <!-- <div class="my_icons"><i class="fa fa-apple"></i></div> -->
                                 <div class="qc_input">
                                     <select class="my_select_authors" id="id_list_author_users" multiple >
                                     </select>
                                 </div>                            
                            </div>
                            
                            <!-- 
                            <div class="list_users_right">
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
                                        <button 
                                            id="a_sync_users"
                                            type="button"  
                                            class="btn btn-success" >
                                            Update
                                        </button> 
                                     </span>
                                 </div>   
                            </div>  -->        
                            
                            
                            
                            
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
                                     <div class="qc_input">    
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
                                     <input type="hidden" name="hidden_id_selected_user" id="hidden_id_selected_user" value="" />                            
                                </div>
                             </div>  
                             <!-- save/udate -->
                             <div class="user_log" >
                                 <!-- log -->
                                 <div class="questionnaire_controls_log">
                                     <div class="qc_label"><i class="fa fa-hand-o-right"></i> Log</div>                           
                                     <div class="qc_input_log"  >
                                        <textarea readonly
                                        class="qc_input_log_txtarea" 
                                        id="id_questionnaire_log_crud">
                                        </textarea>
                                     </div>
                                 </div>  
                             </div>                               
                       
                             
                         <!-- USERS CONFIG -->
                         <div class="questionnaire_control_users_title">
                            <i class="fa fa-info"></i> Configure limits
                            <label class="error_users_message" id="xxxxx4"></label>
                         </div>       
                      
                         <div class="questionnaire_controls">
                             <!-- label -->
                             <div class="qc_label"><i class="fa fa-hand-o-up"></i> To do</div>
                             <div class="my_icons"><i class="fa fa-tree"></i></div>
                             <div class="qc_input"><input id="id_questionnaire_name" style="height:30px;" type="text" /></div>
                         </div>  
                         <div class="save_questionnaire">
                              <!-- btn save --> 
                            <button type="button"  class="btn btn-success" id="id_btn_save_questionnaire" ><i class="fa fa-hand-o-left"> </i> Save</button>
                         </div>  

       
                         
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
         
    <!-- colorbox -->
    <script src="<?php echo $workflow->href_public; ?>/colorbox/jquery.colorbox.js"></script>  
    
    <!-- perso -->
    <script src="<?php echo $workflow->href_public; ?>/js/modules/admin/admin.js"></script>
</html>
