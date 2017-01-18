<?php global $workflow; 
//var_dump($workflow);
//die();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Javascript tree with drag and drop</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/jqtree.css">
        <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/monokai.css">
        <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/css/font-awesome/css/font-awesome.min.css">        
        <link rel="stylesheet" href="<?php echo $workflow->href_public; ?>/jqTree/css/example.css">
        
    </head>
<body>
<div class="container">
<p id="nav">
    <a href="02_load_json_data_from_server.html">&laquo; Example 2</a>
    <a href="04_save_state.html" class="next">Example 4 &raquo;</a>    
</p>

<h1>Example 3 - Drag and drop</h1>

<div id="tree1" data-url="/example_data/"></div>

<p>
   Let's add drag-and-drop support by setting the option <strong>dragAndDrop</strong> to true.
   You can now drag tree nodes to another position.
</p>

<p>
    Other options:
</p>

<ul>
    <li>The option <strong>autoOpen</strong> is set to 0 to open the first level of nodes.</li>
</ul>

<h3>html</h3>

<div class="highlight"><pre><code class="language-html" data-lang="html"><span class="nt">&lt;div</span> <span class="na">id=</span><span class="s">&quot;tree1&quot;</span> <span class="na">data-url=</span><span class="s">&quot;/example_data/&quot;</span><span class="nt">&gt;&lt;/div&gt;</span></code></pre></div>

<h3>javascript<i class="fa fa-graduation-cap"></i></h3><i class="fa fa-graduation-cap"></i>

<div class="highlight"><pre><code class="language-js" data-lang="js"><span class="kd">var</span> <span class="nx">$tree</span> <span class="o">=</span> <span class="nx">$</span><span class="p">(</span><span class="s1">&#39;#tree1&#39;</span><span class="p">);</span>
<span class="nx">$tree</span><span class="p">.</span><span class="nx">tree</span><span class="p">({</span>
  <span class="nx">dragAndDrop</span><span class="o">:</span> <span class="kc">true</span><span class="p">,</span>
  <span class="nx">autoOpen</span><span class="o">:</span> <span class="mi">0</span>
<span class="p">});</span></code></pre></div>


</div>

</body>

<!--[if lt IE 9]>
    <script src="/jqTree/static/jquery-1.11.1.min.js"></script>
<![endif]-->
<!--[if gte IE 9]><!-->
    <!-- <script src="<?php echo $workflow->href_public; ?>/jqTree/js/jquery.min.js"></script>
<!--<![endif]-->
<script src="<?php echo $workflow->href_public; ?>/jqTree/js/tree.jquery.js"></script>
<script src="<?php echo $workflow->href_public; ?>/jqTree/js/bootstrap.min.js"></script>
<script src="<?php echo $workflow->href_public; ?>/jqTree/js/jquery.mockjax.js"></script>
<script src="<?php echo $workflow->href_public; ?>/jqTree/js/example_data.js"></script>


<script src="<?php echo $workflow->href_public; ?>/jqTree/js/icon_buttons.js"></script>


</html>