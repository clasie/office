<?php global $workflow; 
//var_dump($workflow);
//die();
?>
<!DOCTYPE html>
<html>
        <head>
                <meta charset="utf-8">
                <title>Claude Siefers</title>
                
                <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/sbootstrap.min.css">
                <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/bootstrap-theme.min.css">
                <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/jqtree.css">
                <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/monokai.css">
                <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/css/font-awesome/css/font-awesome.min.css">        
                <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/example.css"> 

                <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

                <link rel="stylesheet" type="text/css"
                      href="<?php echo $workflow->href_public; ?>/bootstrap/dist/css/bootstrap.css">
                <link rel="stylesheet" type="text/css" 
                      href="<?php echo $workflow->href_public; ?>/css/questionnaires_custom.css">
                
		<style>


			#tab1, #tab2, #tab3 {
				padding:10px;
				background:rgba(239,235,210, 0.8);
			}

			.tabs li {
				list-style:none;
				display:inline;
			}

			.tabs a {
				padding:5px 10px;
				display:inline-block;
				background:#666;
				color:#fff;
				text-decoration:none;
			}

			.tabs a.active {
				background:#fff;
				color:#000;
			}

		</style>
                
        </head>
        <body>
            <div class="main">
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
                        
                        <ul class='tabs'>
                                <li><a href='#tab1'>Tab 1</a></li>
                                <li><a href='#tab2'>Tab 2</a></li>
                                <li><a href='#tab3'>Tab 3</a></li>
                        </ul>
                        <div id='tab1'>
                                <h3>Section 1</h3>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec lobortis placerat dolor id aliquet. Sed a orci in justo blandit commodo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae.</p>
                        </div>
                        <div id='tab2'>
                                <h3>Section 2</h3>
                                <p>Aenean et est tortor. In pharetra pretium convallis. Mauris sollicitudin ligula non mi hendrerit varius. Fusce convallis hendrerit mauris, eu accumsan nisl aliquam eu.</p>
                        </div>
                        <div id='tab3'>
                                <h3>Section 3</h3>
                                <p>Suspendisse potenti. Morbi laoreet magna vitae est mollis ultricies. Mauris eget enim ac justo eleifend malesuada. Proin non consectetur est. Integer semper laoreet porta. Praesent facilisis leo nec libero tincidunt blandit.</p>
                        </div>

                    </div>
                </div> 
                
                <!-- Footer Section -->
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
         </div>
    </body>

        <script src="<?php echo $workflow->href_public; ?>/jqTree/js/tree.jquery.js"></script>
        <script src="<?php echo $workflow->href_public; ?>/jqTree/js/jquery.mockjax.js"></script>
        <script src="<?php echo $workflow->href_public; ?>/jqTree/js/example_data.js"></script>
        <script src="<?php echo $workflow->href_public; ?>/jqTree/js/icon_buttons.js"></script>  
        <!-- perso -->
        <script src="<?php echo $workflow->href_public; ?>/js/modules/questionnaires/questionnaires1.js"></script>
</html>
