<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <style type="text/css">
     .ui-drop-hover{border:2px solid #bbb;}
#dragdiv li{border:1px solid #bbb;}
#maindiv{width:500px;height:350px;border:2px solid #bbb;}
#allItems,#Ul1{list-style:none;}
#dragdiv{width:180px;height:250px;float:left;}
    </style>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
    <script type="text/javascript">
        $(function() {
            $("#dragdiv li").draggable({
                appendTo: "body",
                helper: "clone",
                cursor: "move",
                revert: "invalid"
            });
 
            initDroppable($("#dragdiv li"));
            function initDroppable($elements) {
                $elements.droppable({
                    activeClass: "ui-state-default",
                    hoverClass: "ui-drop-hover",
                    accept: ":not(.ui-sortable-helper)",
 
                    over: function(event, ui) {
                        var $this = $(this);
                    },
                    drop: function(event, ui) {
                        var $this = $(this);
                        var li1 = $('<li>' + ui.draggable.text() + '</li>')
                        var linew1 = $(this).after(li1);
 
                        var li2 = $('<li>' + $(this).text() + '</li>')
                        var linew2 = $(ui.draggable).after(li2);
 
                        $(ui.draggable).remove();
                        $(this).remove();
 
                        initDroppable($("#dragdiv li"));
                        $("#dragdiv li").draggable({
                            appendTo: "body",
                            helper: "clone",
                            cursor: "move",
                            revert: "invalid"
                        });
                    }
                });
            }
        });
    </script>
</head>
<body>
<center>
    <div id="maindiv">
     <h3>
                <span>Swap items between lists</span></h3>
        <div id="dragdiv">
           
            <ul id="allItems" runat="server">
                <li id="node1">Item A</li>
                <li id="node2">Item B</li>
                <li id="node3">Item C</li>
            </ul>
        </div>
    </div>
    </center>
</body>
</html>