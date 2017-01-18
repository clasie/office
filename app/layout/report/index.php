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
       <!-- custom + tabs + tree -->
       <link rel="stylesheet" type="text/css" href="<?php echo $workflow->href_public; ?>/css/report_custom.css">      
       <!-- upload image -->
       <!-- <link rel="stylesheet" type="text/css" href="<?php echo $workflow->href_public; ?>/css/upload_image.css">  -->
    </head>
    <body>
    <div class="main">
         <!-- The Main wrapper div starts -->
         <div class="container">
         <!-- header content -->
         <div class="header_title">
            <h1><a style="color:navy;" href="."><i class="fa fa-pie-chart"></i> View results</a> </h1>            
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
                 <li><a href="<?php echo $workflow->href_public; ?>/exam/build/edit"><i class="fa fa-rocket"></i> Run a questionnaire</a></li>
               </ul>
               <ul class="nav">
                 <li class="active"><a href="<?php echo $workflow->href_public; ?>/exam/report/edit"><i class="fa fa-pie-chart"></i> View results</a></li>
               </ul>        
               <ul class="nav">
                 <li ><a href="<?php echo $workflow->href_public; ?>/exam/admin/edit"><i class="fa fa-key"></i> Admin</a></li>
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
                         
                         <!-- TARGET -->
                         <div class="questionnaire_control_users_title">
                            <i class="fa fa-users"></i>  Targets
                            <label class="error_users_message" id="target_error_message_id"></label>
                        </div>             
                        
                        <div class="questionnaire_control_users" >
                        
                            <div class="list_users_left">
                                 <!-- AUTHOR -->
                                 <div class="qc_label"><i class="fa fa-users"></i> Authors</div>
                                 <!-- <div class="my_icons"><i class="fa fa-apple"></i></div> -->
                                 <div class="qc_input">
                                     <select class="my_select_authors" id="id_list_author_users"  >                                  
                                     </select>
                                 </div>                            
                            </div>                             
    
                            <div class="list_users_left">
                                 <!-- QUESTIONNAIRES -->
                                 <div class="qc_label"><i class="fa fa-bar-chart"></i> Questionnaires</div>
                                 <!-- <div class="my_icons"><i class="fa fa-apple"></i></div> -->
                                 <div class="qc_input">
                                     <select class="my_select_authors" id="id_list_questionnaires"  >                                   
                                     </select>
                                 </div>                            
                            </div>    
                            
                            <div class="list_users_left">
                                 <!-- EXAM USERS -->
                                 <div class="qc_label"><i class="fa fa-users"></i> Users</div>
                                 <!-- <div class="my_icons"><i class="fa fa-apple"></i></div> -->
                                 <div class="qc_input">
                                     <select class="my_select_authors" id="id_list_exam_users" multiple >                                   
                                     </select>
                                 </div>                            
                            </div>    
                            
                        </div>
                        
                         <!-- REPORT -->
                         <div class="questionnaire_control_users_title">
                            <i class="fa fa-pie-chart"></i>  Report
                            <label class="error_users_message" id="report_error_message_id"></label>
                        </div> 
                         
                         <!-- tree buttons add quest/questionnaire/remove node -->
                         <div class="row">
                             <div class="span12">
                                 <div class="buttons_tree_report">
                                         <button 
                                         type="button"  
                                         class="btn btn-success" 
                                         id="id_btn_show_report1"><i class="fa fa-pie-chart"></i> Show results
                                         </button>
                                         <button 
                                         type="button"  
                                         class="btn btn-warning" 
                                         id="id_btn_cleanse_report1" >
                                         <i class="fa fa-paint-brush"></i> Clear
                                         </button>
                                 </div>				
                             </div>
                         </div>
                         
                         <!-- QUESTIONNAIRE -->
                         <div class="questionnaire_wrapper">
                            <label 
                            class="questionnaire_label" 
                            id="questionnaire_label_id"> </label>
                            <label 
                            class="questionnaire_label_content" 
                            id="questionnaire_label_id_content"></label>  
                            
                             <!-- REPORT js -->
                             <div 
                                id="id_js_report" 
                                class="questionnaire_control_users_title_js_report">
                            
                             </div>
                         
                         </div> 
                        
                         <!-- QUESTION -->
                         <div class="questionn_wrapper" id="questionn_wrapper_id" >
                            <label 
                                class="question_label_content" 
                                id="question_label_id_content">
                            </label>                          
                         </div>          
                         
                         <!-- tree buttons add quest/questionnaire/remove node -->
                         <div class="row">
                             <div class="span12">
                                 <div class="buttons_tree_report">
                                         <button 
                                         type="button"  
                                         class="btn btn-success" 
                                         id="id_btn_show_report"><i class="fa fa-pie-chart"></i> Show results
                                         </button>
                                         <button 
                                         type="button"  
                                         class="btn btn-warning" 
                                         id="id_btn_cleanse_report" >
                                         <i class="fa fa-paint-brush"></i> Clear
                                         </button>
                                 </div>				
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
    <script src="<?php echo $workflow->href_public; ?>/js/modules/report/report.js"></script>
</html>
