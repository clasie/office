<?php global $workflow; ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Claude Siefers</title>
		
	<link rel="stylesheet" type="text/css" href="http://www.jeasyui.com/easyui/themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="http://www.jeasyui.com/easyui/themes/icon.css">
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.1.min.js"></script>
	<script type="text/javascript" src="http://www.jeasyui.com/easyui/jquery.easyui.min.js"></script>
	
		<link rel="stylesheet" type="text/css"
                      href="<?php echo $workflow->href_public; ?>/bootstrap/dist/css/bootstrap.css">
                <link rel="stylesheet" type="text/css" 
                      href="<?php echo $workflow->href_public; ?>/css/storage_custom.css">
	</head>
	<body>
		
		<!-- The Main wrapper div starts -->
		<div class="container">
			<!-- header content -->
			<h1><a href=".">Questionnaires builder</a></h1>
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
	        <!-- Content Sections -->
	        <div class="row">
               <div class="span12">
			   
					<h4>Questionnaires</h4>
					
					<div style="margin:10px 0">
						<a href="#" class="easyui-linkbutton" onclick="getSelected()">GetSelected</a>

					</div>

	
					<table id="dg" title="Questionnaires" class="easyui-treegrid" style="width:auto"
							url="<?php echo $workflow->href_public; ?>/ajax/json/readquestionnaires"
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
			  </div>
			 			  
	
	        <!-- Footer Section -->
	        <hr>
	        <div class="row">
				<div class="span4">
					<h4 class="muted text-center">Meet Our Clients</h4>
					<p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>
					<a href="#" class="btn"><i class="icon-user"></i> Our Clients</a>
				</div>
				<div class="span4">
					<h4 class="muted text-center">Know Our Employees</h4>
					<p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>
					<a href="#" class="btn btn-success"><i class="icon-star icon-white"></i> Our Employees</a>
				</div>
				<div class="span4">
					<h4 class="muted text-center">Reach Us</h4>
					<p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>
					<a href="#" class="btn btn-info">Contact Us</a>
				</div>
			</div>
<?php 
   //}
 ?>
			<!-- Copyright Area -->
			<hr>
			<div class="footer">
				<p>&copy; 2014</p>
			</div>
		</div>
        <script type="text/javascript" src="http://www.jeasyui.com/easyui/jquery.easyui.min.js"></script>       
		
		<script src="<?php echo $workflow->href_public; ?>/js/modules/questionnaires/questionnaires.js"></script>
	</body>
</html>
