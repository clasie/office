<?php global $workflow; 
//var_dump($workflow);
//die();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>jstree basic demos</title>
	<style>
	html { margin:0; padding:0; font-size:62.5%; }
	body { max-width:800px; min-width:300px; margin:0 auto; padding:20px 10px; font-size:14px; font-size:1.4em; }
	h1 { font-size:1.8em; }
	.demo { overflow:auto; border:1px solid silver; min-height:100px; }
	</style>
	<link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jsTree/dist/themes/default/style.min.css" >
        <style>
           li.jstree-open > a .jstree-icon {background:url("http://localhost/public/jsTree/resources/icons/a.png") 0px 0px no-repeat !important;}
            li.jstree-closed > a .jstree-icon {background:url("http://localhost/public/jsTree/resources/icons/b.png") 0px 0px no-repeat !important;}
            li.jstree-leaf > a .jstree-icon {background:url("http://localhost/public/jsTree/resources/icons/b.png") 0px 0px no-repeat !important;}
        </style>        
</head>
<body>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="<?php echo $workflow->href_public; ?>/jsTree/dist/jstree.min.js"></script>
        <img src="<?php echo $workflow->href_public; ?>/jsTree/resources/icons/a.png" />
        <button id="actionButton">Do it!</button>
        
        <div id="selector">
          <ul>
            <li rel="team"><a>Team A's Projects</a>
              <ul>
                <li rel="iteration"><a>Iteration 1</a>
                      <ul>
                        <li><a>Story A</a></li>
                        <li id="1" ><a>Story B</a></li>
                        <li id="2"><a>Story C</a></li>
                      </ul>
                </li>
                <li rel="iteration"><a>Iteration 2</a>
                      <ul>
                         <li><a>Story D</a></li>
                      </ul>
                </li>
              </ul>
            </li>
          </ul>
        </div>	
	<script>
            $("#selector").jstree({
                    "core" : {
                            "check_callback" : true
                    },                
                    "plugins" : [ "html_data", "types", "themes","ui" ]
            });            
            $("#actionButton").click(function() {
                console.log(5);
                var ref = $('#selector').jstree(true),
                        sel = ref.get_selected();
                console.log(ref);
                if(!sel.length) { 
                    console.log(6);
                    return false; 
                }
                sel = sel[0];
                sel = ref.create_node(sel, "new node");
                if(sel) {
                    //console.log(7);
                    //ref.edit(sel);
                }
            });      
            
                     
	</script>
</body>
</html>