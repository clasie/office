<?php global $workflow; ?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Claude Siefers</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $workflow->href_public; ?>/bootstrap/dist/css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="<?php echo $workflow->href_public; ?>/bootstrap/dist/css/bootstrap-responsive.css">
		<link rel="stylesheet" type="text/css" href="<?php echo $workflow->href_public; ?>/bootstrap/dist/css/bootstrap.icon-large.min.css">
                <link href="http://twitter.github.com/bootstrap/assets/css/docs.css" rel="stylesheet">
                <link rel="stylesheet" type="text/css" href="<?php echo $workflow->href_public; ?>/css/calculateur.css">
	</head>
	<body>
		
		<!-- The Main wrapper div starts -->
		<div class="container">
			<!-- header content -->
			<h4><a href=".">La ferme du bonheur...naturel</a></h4>
<i class="icon-large icon-pencil"></i>                       
  <a target="_blank" href="https://drive.google.com/?authuser=0#folders/0B8iaW3d-HmoqdGhZOFR1WWlYT1E" >Google documentation</a>

			<!-- Navigation -->
			<div class="navbar">
	          <div class="navbar-inner">
	            <div class="container">
	              <ul class="nav">
	                <li class="active"><a href="<?php echo $workflow->href_public; ?>/ferme/calculateur/userform"">Calculateur</a></li>
<!--
	                <li><a href="">Liens</a></li>
	                <li><a href="">Fermes</a></li>
-->
	              </ul>
	            </div>
	          </div>
	        </div>

	        <!-- Marketing area -->
	        <div class="hero-unit">
                        <p><img src="<?php echo $workflow->href_public; ?>/images/ferme8.jpg"></p>
	        </div>

	        <!-- Content Sections -->
	        <div class="row">                        
	        	<!-- Right side Content Vertical Area -->
	        	<div class="span8">
                                <fieldset class="fs_custom"> 
				    <legend class="fs_legend_custom">Calculateur rapide coût traveaux </legend>
				        <!-- beton -->
					<div class="glob_line">
					   <div class="glob_label">Béton</div>
					   <div class="sub_line">
						<?php //txt
                                                   $db = $workflow->modelInstance;
						   include_once '../lib_custom/forms/HtmlComponents.class.php';
					           if(!isset($db)){
						      $db = $workflow->modelInstance; // new CalculateurModel();
						   }
                                                   //$res = $db->getTypeBeton();
						   $hc = new HtmlComponents();
						   echo $hc->getInputFieldText("type_beton_metre_carre","field_1", "");
						?>
					       <label class="label_1"> m² X </label>
						<?php //txt
						   include_once '../lib_custom/forms/HtmlComponents.class.php';
					           if(!isset($db)){
						      $db = $workflow->modelInstance; // new CalculateurModel();
						   }
                                                   //$res = $db->getTypeBeton();
						   $hc = new HtmlComponents();
						   echo $hc->getInputFieldText("type_beton_metre","field_1", "");
						?>
					       <label class="label_1"> m X </label>
						<?php //select
						   include_once '../lib_custom/forms/HtmlComponents.class.php';
					           if(!isset($db)){
						      $db = $workflow->modelInstance; // new CalculateurModel();
                                                   }
                                                   $res = $db->getTypeBeton();
						   $hc = new HtmlComponents();
                                                   //var_dump($res);                                                   
                                                   //die(__FILE__ . __LINE__);
						   echo $hc->getSelect("type_beton_select","select_1",$res,"id_type_beton");
						?>
						<label class="label_1"> € / m³ </label>
						<?php //btn
						   include_once '../lib_custom/forms/HtmlComponents.class.php';
					           if(!isset($db)){
						      $db = $workflow->modelInstance; // new CalculateurModel();
						   }
                                                   //$res = $db->getTypeBeton();
						   $hc = new HtmlComponents();
						   echo $hc->getButton("type_btn_beton_equal","btn_1", "");
						?>
						<?php //txt
						   include_once '../lib_custom/forms/HtmlComponents.class.php';
					           if(!isset($db)){
						      $db = $workflow->modelInstance; // new CalculateurModel();
						   }
                                                   //$res = $db->getTypeBeton();
						   $hc = new HtmlComponents();
						   echo $hc->getInputFieldText("type_beton_prix","field_1_total", "disabled");
						?>
					       <label class="label_1"> € </label>
					   </div>
					</div>

				        <!-- beton feraillage-->
					<div class="glob_line">
					   <div class="glob_label">Béton Féraillage</div>
					   <div class="sub_line">
						<?php //txt
						   include_once '../lib_custom/forms/HtmlComponents.class.php';
					           if(!isset($db)){
						      $db = $workflow->modelInstance; // new CalculateurModel();
						   }
                                                   //$res = $db->getTypeBeton();
						   $hc = new HtmlComponents();
						   echo $hc->getInputFieldText("type_beton_feraillage_metre_carre","field_1", "");
						?>
					       <label class="label_1"> m² X </label>
						<?php //select
						   include_once '../lib_custom/forms/HtmlComponents.class.php';
					           if(!isset($db)){
						      $db = $workflow->modelInstance; // new CalculateurModel();
						   }
                                                   $res = $db->getTypeFeraillage();
						   $hc = new HtmlComponents();
						   echo $hc->getSelect("type_beton_feraillage_select","select_1",$res,"id_type_feraillage");
						?>
                                                <label class="label_1"> € / m² </label>
						<?php //btn
						   include_once '../lib_custom/forms/HtmlComponents.class.php';
					           if(!isset($db)){
						      $db = $workflow->modelInstance; // new CalculateurModel();
						   }
                                                   //$res = $db->getTypeBeton();
						   $hc = new HtmlComponents();
						   echo $hc->getButton("type_btn_beton_feraillage_equal","btn_1", "");
						?>
						<?php //txt
						   include_once '../lib_custom/forms/HtmlComponents.class.php';
					           if(!isset($db)){
						      $db = $workflow->modelInstance; // new CalculateurModel();
						   }
                                                   //$res = $db->getTypeBeton();
						   $hc = new HtmlComponents();
						   echo $hc->getInputFieldText("type_beton_feraillage_prix","field_1_total", "disabled");
						?>
					       <label class="label_1"> € </label>
					   </div>
					</div>
<hr>
                                        <!-- total-->
					<div class="glob_line">
					   <div class="glob_label"></div>
					   <div class="sub_line">
                                                <label class="label_1"></label>
						<?php //btn
						   include_once '../lib_custom/forms/HtmlComponents.class.php';
					           if(!isset($db)){
						      $db = $workflow->modelInstance; // new CalculateurModel();
						   }
                                                   //$res = $db->getTypeBeton();
						   $hc = new HtmlComponents();
						   echo $hc->getButtonTotal("type_btn_total","btn_1", "");
						?>
						<?php //txt
						   include_once '../lib_custom/forms/HtmlComponents.class.php';
					           if(!isset($db)){
						      $db = $workflow->modelInstance; // new CalculateurModel();
						   }
                                                   //$res = $db->getTypeBeton();
						   $hc = new HtmlComponents();
						   echo $hc->getInputFieldText("type_total_prix","field_1_total", "disabled");
						?>
					       <label class="label_1"> € </label>
					   </div>
					</div>
				</fieldset>

	        	</div>
	        </div>

	        <!-- Footer Section -->

	        <div class="row">
				<div class="span4">
					<h4 class="muted text-center">Culture</h4>
					<p><img src="<?php echo $workflow->href_public; ?>/images/ferme6.jpg"></p>
				</div>
				<div class="span4">
					<h4 class="muted text-center">Nature</h4>
					<p><img src="<?php echo $workflow->href_public; ?>/images/ferme3.jpg"></p>
				</div>
				<div class="span4">
					<h4 class="muted text-center">Terroir</h4>
					<p><img src="<?php echo $workflow->href_public; ?>/images/ferme8.jpeg"></p>
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
               
		<script src="<?php echo $workflow->href_public; ?>/bootstrap/dist/js/jquery.js"></script>
		<script src="<?php echo $workflow->href_public; ?>/bootstrap/dist/js/bootstrap.js"></script>
                <script src="<?php echo $workflow->href_public; ?>/js/calculateur.js"></script>
	</body>
</html>
