<?php global $workflow; 
//var_dump($workflow);
//die();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Claude Siefers</title>
		
                <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/bootstrap-theme.min.css">
                <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/jqtree.css">
                <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/monokai.css">
                <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/css/font-awesome/css/font-awesome.min.css">        
                <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/example.css"> 
                
		<link rel="stylesheet" type="text/css" href="http://www.jeasyui.com/easyui/themes/black/easyui.css">
		<link rel="stylesheet" type="text/css" href="http://www.jeasyui.com/easyui/themes/icon.css">
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.1.min.js"></script>
		<script type="text/javascript" src="http://www.jeasyui.com/easyui/jquery.easyui.min.js"></script>
	
		<link rel="stylesheet" type="text/css"
                      href="//www.claude-siefers.com/public/bootstrap/dist/css/bootstrap.css">
                <link rel="stylesheet" type="text/css" 
                      href="//www.claude-siefers.com/public/css/questionnaires_custom.css">
	</head>
	<body>
		<!-- The Main wrapper div starts -->
		<div class="container">
			<!-- header content -->
			<div class="header_title">
			   <h1><a style="color:orange;" href=".">Questionnaires builder</a></h1>
			</div
			<!-- Navigation -->
			<div class="navbar">
	          <div class="navbar-inner">
	            <div class="container">
	              <ul class="nav">
	                <li class="active"><a href="#">Home</a></li>
	              </ul>
	            </div>
	          </div>
	        </div>
	        <div class="row">
                    <div class="span12">
                        <div id="tree1" data-url="/example_data/"></div>	
                    </div>                        
                </div>
                        

                        
	        <div class="row">
                <div class="span12">
					<table id="dg" title="Questionnaires" class="easyui-treegrid" style="width:auto"
							url="//www.claude-siefers.com/public/ajax/json/readquestionnaires"
							rownumbers="true" showFooter="true"
							idField="id" treeField="region">
						<thead frozen="true">
							<tr>
								<th field="region" width="auto">Region</th>
							</tr>
						</thead>
						<thead>
							<tr>
								<th colspan="4">2009</th>
								<th colspan="4">2010</th>
							</tr>
							<tr>
								<th field="f1" width="auto" align="right">Pierre</th>
								<th field="f2" width="auto" align="right">Jean</th>
								<th field="f3" width="auto" align="right">Anne</th>
								<th field="f4" width="auto" align="right">Paul</th>
								<th field="f5" width="auto" align="right">Serge</th>
								<th field="f6" width="auto" align="right">Kazimir</th>
								<th field="f7" width="auto" align="right">Ben Laeden</th>
								<th field="f8" width="auto" align="right">Oups</th>
							</tr>
						</thead>
					</table>					
				</div>
				
			    <div id="id_wrap_panel" class="wrap_panel" >
					<div class="span12">
	
						 <div class="close_Arrow">
						 </div>		
						 
						 <!-- the pane with questionnaire/question config -->					 
						 <div id="tabs_1" class="easyui-tabs" style="width:auto;height:auto">

								<!-- QUESTIONNAIRE TAB -->
								<div id="tab_1" title="Config questionnaire" 
									data-options="" 
									style="">	
									
									<div class="cfg_quest_items">
										
										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Coverage 
										</div>									
										<div class="cfg_quest_item_component">
										  <input class="easyui-numberbox" value="100"></input> %
										</div>		
										
										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Automatic save 
										</div>									
										<div class="cfg_quest_item_component">
										   <div class="container_row">
											  <div class="radio_input"><input type="radio" name="group1" value="no" checked></div><div class="radio_input_text">Yes</div>
											  <div class="radio_input"><input type="radio" name="group1" value="no" checked></div><div class="radio_input_text">No</div>
										   </div>
										</div>
										
										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Config level 
										</div>									
										<div class="cfg_quest_item_component">
										   <div class="container_row">
											  <div class="radio_input"><input type="radio" name="group1" value="no" checked></div><div class="radio_input_text">Basic</div>
											  <div class="radio_input"><input type="radio" name="group1" value="no" checked></div><div class="radio_input_text">Advanced</div>
											  <div class="radio_input"><input type="radio" name="group1" value="no" checked></div><div class="radio_input_text">Expert</div>
										   </div>
										</div>		
										
										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Chrono 
										</div>									
										<div class="cfg_quest_item_component">
										   <div class="container_row">
											  <div class="radio_input"><input type="radio" name="group1" value="no" checked></div><div class="radio_input_text">Yes</div>
											  <div class="radio_input"><input type="radio" name="group1" value="no" checked></div><div class="radio_input_text">No</div>
										   </div>
										</div>		
										
										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Randomize questions
										</div>									
										<div class="cfg_quest_item_component">
										   <div class="container_row">
											  <div class="radio_input"><input type="radio" name="group1" value="no" checked></div><div class="radio_input_text">Yes</div>
											  <div class="radio_input"><input type="radio" name="group1" value="no" checked></div><div class="radio_input_text">No</div>
										   </div>
										</div>	
										
										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Randomize responses
										</div>									
										<div class="cfg_quest_item_component">
										   <div class="container_row">
											  <div class="radio_input"><input type="radio" name="group1" value="no" checked></div><div class="radio_input_text">Yes</div>
											  <div class="radio_input"><input type="radio" name="group1" value="no" checked></div><div class="radio_input_text">No</div>
										   </div>
										</div>	
										
										<div class="cfg_quest_item_label">
										   <i class="icon-leaf"></i> Comment
										</div>									
										<div  class="cfg_quest_item_component">
										  <textarea id="45" class="questionnaire_comment"></textarea>
										</div>
									</div>
								</div>				
								<!-- QUESTION TAB -->
								<div id="tab_2" title="Config question" data-options="" style="">								
																
									<div class="cfg_quest_items">								
										
										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Question type
										</div>									
										<div class="cfg_quest_item_component">
											<input type="radio" name="group1" value="yes"> Conditionnal 
											<input type="radio" name="group1" value="no" checked> Not conditionnal 
										</div>	
										
										<div  class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Question text 
										</div>									
										<div class="cfg_quest_item_component">
										  <textarea id="88" class="questionnaire_comment">Ma question</textarea>
										</div>	
										
										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Response type 
										</div>									
										<div class="cfg_quest_item_component">
										  <select class="" name="state" 
										  style="width:200px;">
												<option value="">Simple text</option>
												<option value="">Number</option>
												<option value="">Date</option>
												<option value="">QCM exclusive</option>
												<option value="">QCM inclusive</option>
											</select>
										</div>																		

										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Keys 
										</div>									
										<div class="cfg_quest_item_component">
										  <select class="" name="state" style="width:200px;" multiple >
												<option value="AL">Alabama</option>
												<option value="AK">Alaska</option>
												<option value="AZ">Arizona</option>
												<option value="AR">Arkansas</option>
												<option value="CA">California</option>
												<option value="CO">Colorado</option>
												<option value="CT">Connecticut</option>
											</select>
										</div>	
										
										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Weight of this question
										</div>									
										<div class="cfg_quest_item_component">
										  <input class="" value="45"></input>
										</div>	
										
										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Difficulty for creator 
										</div>									
										<div class="cfg_quest_item_component">
										  <input class="" value="45"></input>
										</div>										
										
										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Difficulty for answerer 
										</div>									
										<div class="cfg_quest_item_component">
										  <input class="" value="45"></input>
										</div>	
										
										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Covering ratio 
										</div>									
										<div class="cfg_quest_item_component">
										  <input class="" value="45"></input>
										</div>										
										
										<div class="cfg_quest_item_label">
										   <span class="icon-leaf"></span> Comment 
										</div>	
										<div class="cfg_quest_item_component">
										  <textarea class="questionnaire_comment"></textarea>
										</div>											
										
									</div>	
									
								</div>
						
						</div>
						 <!-- button close panel -->
						 <div class="btn_close_panel">
							 <a  class="easyui-linkbutton" id="close_panel">Close</a>
						 </div>							
					</div>
				</div><!-- wrap panel end -->
		   
		   </div> <!-- ? -->

	        <!-- Footer Section -->
	        <hr>
	        <div class="row">
				<div class="trans_letters">
				   <div class="span4">
						<h4 class="muted text-center">Meet Our Clients</h4>
						<div class="under_text">
						   <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>						
						   <a href="#" class="btn"><i class="icon-user"></i> Our Clients</a>
						</div>
					</div>
					<div class="span4">
						<h4 class="muted text-center">Know Our Employees</h4>
						<div class="under_text">
						   <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>
						
						   <a href="#" class="btn btn-success"><i class="icon-star icon-white"></i> Our Employees</a>
						</div>
					</div>
					<div class="span4">
						<h4 class="muted text-center">Reach Us</h4>
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
				<p>&copy; 2014</p>
			</div>
         </div>
        <script type="text/javascript" src="http://www.jeasyui.com/easyui/jquery.easyui.min.js"></script>       
		
		<script src="//www.claude-siefers.com/public/js/modules/questionnaires/questionnaires1.js"></script>
	</body>
</html>
