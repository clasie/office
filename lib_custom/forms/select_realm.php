<?php
//flag form
$showFormFlag = (isset($_POST["f"]))?
   htmlspecialchars($_GET["f"]):'0';
$option_1 = "";
$option_2 = "selected";
$option_3 = "";
?>
<select id="realm" data-theme="a" data-mini="true" name="realm">
<option <?php echo $option_1; ?> value='-1'>Select a realm</option>
<option <?php echo $option_2; ?> value="1">C#</option>
<option <?php echo $option_3; ?> value="2">Linux</option>
</select>
