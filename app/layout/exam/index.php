<?php 
global $workflow; 
//var_dump($workflow);
//die();
/**
* run a questionnaire
* 
 * <link rel="stylesheet" href="jquery.progressbar.css">

<script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="jquery.progressbar.js"></script>

*/
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Examen</title>
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
       <!-- custom + tabs + tree -->
       <link rel="stylesheet" type="text/css" href="<?php echo $workflow->href_public; ?>/css/exam_custom.css?v=1">       
                    
                                   
    </head>
    <body>
    <div class="main">
         <!-- The Main wrapper div starts -->
         <div class="container">
         <!-- header content -->
         <div class="header_title">
            <h1><a style="color:navy;" href=".">
                    <i id="titlerocket1" class="fa fa-rocket"></i>
                 Run a questionnaire </a>                 
            </h1>
         </div>
         <div class="link_login_class"><a  href="#" id="id_log_unlog">Log in</a></div>         
         <!-- Navigation -->
         <div class="navbar">
           <div class="navbar-inner">
             <div class="container">
               <ul class="nav">
                   <li><a href="<?php echo $workflow->href_public; ?>/questionnaires/build/edit""><i class="fa fa-cogs"></i> Build a questionnaire</a></li>
               </ul>
               <ul class="nav">
                 <li class="active"><a href="#"><i id="titlerocket2" class="fa fa-rocket"></i> Run a questionnaire</a></li>
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
               <div class="login_class" >Ticket number</div>             
               <input type="text" name="ticket_number" id="ticket_number_dialog_from" />               
            </div>   
         </div>
         
         <input type="hidden" id="hidden_login_dialog_to" />
         <input type="hidden" id="hidden_ticket_dialog_to" />
         <input type="hidden" id="hidden_password_dialog_to" />
         <input type="hidden" id="hidden_origine_dialog_to" />
         
         <!-- timer movement  -->  
		 <div class="progress_questions">
            <div id="id_left_part" class="left_part"></div>
            <div id="id_right_part" class="right_part">
                <i class="fa fa-rocket"></i>
            </div>
            <div id="id_end_part" class="end_part"><i class="fa fa-ambulance"></i></div>
            <div id="id_text_chrono" class="text_chrono" ></div>
		 </div>
         <!-- select building -->
         <div class="row"> <!-- new -->
             <div class="span12">
			    <div class="exam_header">
                   <select id="id_select_a_questionnaire" class="selectpicker">
                      <option value="-1">Select a questionnaire</option>
                  </select>
                </div>
             </div>
         </div>
         <!-- tree buttons add quest/questionnaire/remove node -->
         <div class="row">
             <div class="span12">
             </div>
         </div>

         <!-- tab management current questionnaire/question -->
         <div id="tab_wrapper">    
             <div class="row">
                 <div class="span12">         
                    <div class="exam_questions">
                        <!-- question -->
                        <h4 class="h4-class">Question <span id="counter-quest"></span></h4>
                        <!-- the question itself -->
                        <div class="alert alert-info">
						    <p id="id_question_text"></p>
					    </div>
                        <!-- images if any -->
                        <div id="id_container_images_exam" class="alert alert-info" >
                            <div class="image_container" >   
                               <!-- images -->                               
                            </div>
					    </div>                        
                        <!-- responses start -->
                           <!-- text good response --> 
                             <div class="good_answer_text_wrapper">
                                <div class="qc_label_good_reponse"><i class="fa fa-hand-o-right"></i> Answer</div>
                                <div class="qc_good_response">
                                   <textarea class="editor" name="id_good_answer" id="id_good_answer"></textarea>
                                </div>    
                             </div>
                            <!-- dates -->
                             <div class="good_answer_date_wrapper">
                                <div class="qc_label_good_reponse"><i class="fa fa-hand-o-right"></i> Answer</div>
                                <div class="qc_input">
                                   <input type="text" id="date_picker_good_answer" style="width:88px;height:30px;">                                    
                                </div>       
                             </div>
                             
                             <!-- number -->
                             <div class="good_answer_number_wrapper">
                                <div class="qc_label_good_reponse"><i class="fa fa-hand-o-right"></i> Answer</div>
                                <div class="qc_input">
                                   <input type="text" id="number_good_answer" style="width:88px;height:30px;">                                    
                                </div>                                
                             </div>

                             <!-- INCLUSIVE MULTIPLE qcm d&d --> 
                             <div class="good_answer_qcm_cbx_wrapper">
                                <div class="qc_label_response_qcm_inclusive"><i class="fa fa-hand-o-right"></i> Answer(s)</div>
                                <div class="q_qcm">
                                    <div class="qcm_incl">
                                       <ul id="sortable-multiple">
                                          <!-- 
                                          <li class="ui-state-default">
                                              <div class="qcm_inside_li_cbox">
                                              <input type="checkbox" name="option1" value="Milk"> 
                                              </div>
                                               <textarea class="txta_custom" >QCM inc</textarea>                                             
                                          </li>   -->                                                
                                       </ul>
                                    </div>
                                </div>  
                             </div>

                             <!-- EXCLUSIVE UNIQUE radio qcm d&d --> 
                             <div class="good_answer_qcm_radio_wrapper">
                                <div class="qc_label_response_qcm_exclusives"><i class="fa fa-hand-o-right"></i> Answer</div>
                                <div class="q_qcm">
                                    <div class="qcm_exc" >
                                       <ul id="sortable-mono" >
                                          <!-- 
                                          <li class="ui-state-default">
                                              <div class="qcm_inside_li_cbox">
                                              <input type="radio" name="option1" value="Milk"> 
                                              </div>
                                              <textarea class="txta_custom" >QCM exc</textarea>                                              
                                          </li>     -->                                                                          
                                       </ul>
                                    </div>                                  
                                </div>          
                            </div>   


                        <!-- responses stop -->
                        <div class="next_prev_btns">
                            <button type="button"  class="btn btn-success" 
                                id="id_previous_question">
                                Previous question</button>

                            <button type="button"  class="btn btn-success" 
                                id="id_next_question">
                                Next question</button>
                        </div>
                        
                        <!-- Save and quit the exam -->
                        <div class="save_and_quit_Exam">
                            <button type="button"  class="btn btn-primary" 
                                id="id_save_and_quit_the_exam">
                                Save and quit the exam</button>

                        </div>
                        
                    </div>
                 </div>
             </div> 
         </div>

         <!-- description questionnaire -->
         <div id="Div1">    
             <div class="row">
                 <div class="span12">         
                    <div class="questionnaire_description">
                        <!-- label -->
                        <div id="id_quest_desc_label" class="quest_desc_label">
                            <div class="thumbnail">
                                <!-- <img src="..." alt="..."> -->
                                <div class="caption">
                                    <div class="alert alert-success" >
			    	                    <h4><i class="icon-large icon-question-sign"></i> Questionnaire</h4>
				                        <p id="id_questionnaire_label"></p>
				                    </div>

                                    <div class="alert alert-success" >
			    	                    <h4><i class="icon-large icon-pencil"></i> Comments</h4>
				                        <p id="id_questionnaire_comment"></p>
				                    </div>

                                    <div class="alert alert-success" >
			    	                    <h4><i class="icon-large icon-calendar"></i> Dates</h4>
				                        <p id="id_questionnaire_dates"></p>
				                    </div>

                                <button type="button"  class="btn btn-success" 
                                    id="id_run_questionnaire">
                                    Run me!</button>
                                <button type="button"  class="btn btn-info" 
                                    id="id_cancel_questionnaire">
                                    Cancel</button>
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
     </div>     
    </body> <!-- end class main -->
    
    <!-- jsTree -->
    <script src="<?php echo $workflow->href_public; ?>/jsTree/dist/jstree.min.js"></script>     
    <!-- wysywig -->
    <!-- <script src="<?php echo $workflow->href_public; ?>/jquery-te/jquery-te-1.4.0.min.js"></script>     -->    
    <!-- colorbox -->
    <script src="<?php echo $workflow->href_public; ?>/colorbox/jquery.colorbox.js"></script>  
    
    <!-- perso -->
    <script src="<?php echo $workflow->href_public; ?>/js/modules/exam/exam.js?v=1""></script>
</html>
