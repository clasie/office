<?php global $workflow; ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">

		<!-- tree stuffs start -->
	<link rel="stylesheet" type="text/css" href="http://www.jeasyui.com/easyui/themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="http://www.jeasyui.com/easyui/themes/icon.css">
	<link rel="stylesheet" type="text/css" href="http://www.jeasyui.com/easyui/demo/demo.css">
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.1.min.js"></script>
	<script type="text/javascript" src="http://www.jeasyui.com/easyui/jquery.easyui.min.js"></script>
                <!-- tree stuffs stop -->

		<title>Claude Siefers</title>
		<link rel="stylesheet" type="text/css"
                      href="<?php echo $workflow->href_public; ?>/bootstrap/dist/css/bootstrap.css">
                <link rel="stylesheet" type="text/css" 
                      href="<?php echo $workflow->href_public; ?>/css/storage_custom.css">
	</head>
	<body>
		
		<!-- The Main wrapper div starts -->
		<div class="container">
			<!-- header content -->
			<h1><a href=".">Questionnaires</a></h1>
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
	        	<!-- Left Side Vertical Bar -->
	        	<div class="span4">

	        		<ul class="nav nav-list">
					  <li class="nav-header">Sample of question/answer?</li>
					  <li><a href="<?php echo $workflow->href_public; ?>/storage/show/resp">Questions</a></li>
                 		</ul>
<hr>
			<?php //form input
			if($workflow->mvcInstance->action == 'all'){
			   include_once '../lib_custom/forms/raw_input.php';
			}?>
	        	</div>
	        	<!-- Right side Content Vertical Area -->
	        	<div class="span8">

                               <h3>Question/responses</h3>
                        
<?php 
	//include_once 'lib_custom/Crud.class.php';
	//db connection
        if(!isset($db)){
	   $db = new Crud();
        }
        $res = $db->getAllQuestions((isset($realm))? $realm : null);
        $counter = 0;
        $tmp ="";

	$hide_show_class= (isset($_POST["check_toggle"]))?
	   "hide_class":"show_class";

	while($info = mysqli_fetch_array($res, MYSQLI_ASSOC)){
            //var_dump($info); //continue;
            $id_question = $info['id_question'];
	    $tmp = '<p class="question"><b>' . 
            ++$counter . ' &rarr; ' 
              . stripslashes($info['question']) .
                 ' ?</b><i>(ID:' . $id_question . ')</i> </p>';
            if($workflow->mvcInstance->action == 'resp' || $workflow->mvcInstance->action == 'all' ){
                $tmp .= '<p class="' . $hide_show_class 
                     .  '" >' .stripslashes($info['response']) . '</p>';
            }
            echo '<pre>';
            echo $tmp;
            echo '</pre>';
        }
?>
	        	</div>
	        </div>
<?php 
   //if(!$showResp){ 
?>
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
               
		<script src="<?php echo $workflow->href_public; ?>bootstrap/dist/js/jquery.js"></script>
		<script src="<?php echo $workflow->href_public; ?>bootstrap/dist/js/bootstrap.js"></script>
	</body>
</html>
