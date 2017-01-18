<?php
//var_dump($workflow);die(__FILE__);
	/*
	SELECT * FROM 	  qcms_response_correction WHERE 
	fr LIKE "%RootWeb%"
	*/

	/**
	 * question
	 */
	$question = (isset($_POST["question"]))?
	   $_POST["question"]:'';

	/**
	 * response
	 */
	$response = (isset($_POST["response"]))?
	   $_POST["response"]:'';

	/**
	 * Search
	 */
	$search = (isset($_POST["search"]))?
	   $_POST["search"]:'';

	if(isset($search)){
	   $search = trim($search);
	}else{
           echo $search;
        }

        if(strlen($search) == 0){
		/**
		 * record
		 */
		$db = $workflow->modelInstance; //new Crud();
		$db->insertQuestionResponse($question,$response);
        }else{ 
		/**
		 * search
		 */
		$db = $workflow->modelInstance; //new Crud();
		$db->searchQuestionResponse($search);
        }
?>

<?php

	$option_1 = "";
	$option_2 = "";
	$option_3 = "";
        $option_4 = "";
	$option_5 = "";
	$option_6 = "";
	$option_7 = "";
        $option_8 = "";

        $realm = "-1";
	$realm = (isset($_POST["realm"]))?
	   htmlspecialchars($_POST["realm"]):'-1';
        //c#
        if("-1" == $realm){
	   $option_1 = "selected";
        //linux 
        }else if("1" == $realm){
           $option_3 = "selected"; 
        }else if("2" == $realm){
           $option_2 = "selected"; 
        }else if("3" == $realm){
           $option_4 = "selected"; 
        }else if("4" == $realm){
           $option_5 = "selected"; 
        }else if("5" == $realm){
           $option_6 = "selected"; 
        }else if("6" == $realm){
           $option_7 = "selected"; 
        }else if("7" == $realm){
           $option_8 = "selected"; 
        }

?>


<form name="input" action="" method="POST">

	<select id="realm" data-theme="a" data-mini="true" name="realm">
		<option <?php echo $option_1; ?> value='-1'>Select a realm</option>
		<option <?php echo $option_3; ?> value="1">Linux</option>
		<option <?php echo $option_2; ?> value="2">C#</option>
		<option <?php echo $option_4; ?> value="3">PHP</option>
	        <option <?php echo $option_5; ?> value="4">Javascript</option>      
	        <option <?php echo $option_6; ?> value="5">ICMIT</option>   
	        <option <?php echo $option_7; ?> value="6">HTML</option>        
                <option <?php echo $option_8; ?> value="7">Cuisine</option>  
	</select>
        <div class="label_Hide_reponses">Hide responses</div>
<?php
	$hide_response_checked = (isset($_POST["check_toggle"]))?
	   "checked":"";
?>
        <div class="check_toggle">        
          <input type="checkbox" <?php echo $hide_response_checked;?> name="check_toggle" id="check_toggle" >
        </div>  
	<hr>

	</br>
	Keyword &nbsp;
<?php
	$search = (isset($_POST["search"]))?
	   $_POST["search"]:'';
?>
	<input type="text" name="search" value="<?php echo $search; ?>"></br>
	<input type="submit" value="Search">
</form>

<hr>

<form name="input" action="" method="POST">
	Question &nbsp;
        <input type="text" name="question" value=""></br>
	Response
	<textarea name="response" rows="10" cols="10"></textarea></br>
	Realm &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <select id="realm_insert" data-theme="a" data-mini="true" name="realm">
		<option <?php echo $option_1; ?> value='-1'>Select a realm</option>
		<option <?php echo $option_3; ?> value="1">Linux</option>
		<option <?php echo $option_2; ?> value="2">C#</option>
	        <option <?php echo $option_4; ?> value="3">PHP</option>
	        <option <?php echo $option_5; ?> value="4">Javascript</option>      
	        <option <?php echo $option_6; ?> value="5">ICMIT</option>   
	        <option <?php echo $option_7; ?> value="6">HTML</option>   
                <option <?php echo $option_8; ?> value="7">Cuisine</option> 
	</select>
</br>
	<input type="submit" value="Record">

</form>

