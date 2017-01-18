$(document).ready(function (e) {
    $("#uploadimage").on('submit', (function (e) {
        isStillUsingGoodSession();
        e.preventDefault();

        //alert(current_node_type + " " + current_node_id);
        //$("#message").empty();
        //$('#loading').show();
        $('#temp_message').show();
        $('#temp_message').html("Loading...");
        //"/public/ajax_questionnaire/json/readquestionnaires/id_user/1",
        $.ajax({
            url: "/public/ajax_questionnaire/json/readquestionnaires/id_user/1",   	// Url to which the request is send
            type: "POST",      				// Type of request to be send, called as method
            dataType: "json",
            data: new FormData(this), 		// Data sent to server, a set of key/value pairs representing form fields and values 
            contentType: false,       		// The content type used when sending data to the server. Default is: "application/x-www-form-urlencoded"
            cache: false,					// To unable request pages to be cached
            processData: false,  			// To send DOMDocument or non processed data file it is set to false (i.e. data should not be in the form of string)
            success: function (data)  		// A function to be called if request succeeds
            {
                //$('#loading').hide();
                //$("#message").html(data);
                if (data.error == "" && data.file_error == "") {
                    $('#temp_message').empty();
                    $('#temp_message').hide(1500);
                    //$('#b').attr('src', data.file_url);
                    console.log(data);
                    console.log(data.file_url);
                    $('#image_preview').hide(1500);
                    addImage(data.file_url, data.file_id);
                    var node = $('#jstree_demo').jstree(true).get_node(sel);

                        var obj = {
                            file_url: data.file_url,
                            file_id : data.file_id
                        };
                        theLength = node.data.IMAGES.length;
                        node.data.IMAGES[theLength] = obj;
                        //$('#jstree_demo').jstree(true).get_node(sel) = node;

                } else {
                    $('#temp_message').empty();
                    $('#temp_message').html(data.error + " " + data.file_error);
                    //$('#b').attr('src', data.file_url);
                    //console.log(data);
                    //console.log(data.file_url);
                    $('#image_preview').hide(1500);
                    //test4(data.file_url);
                }
            }
        });
    }));

    // Function to preview image
    $(function () {
        $("#file").change(function () {
            $('#temp_message2').empty();
            $('#temp_message2').hide(1500);
            // $("#message").empty();         // To remove the previous error message
            var file = this.files[0];
            var imagefile = file.type;
            var match = ["image/jpeg", "image/png", "image/jpg"];
            //error
            if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2]))) {
                //$('#previewing').attr('src', 'noimage.png');
                $('#temp_message2').show(1500);
                console.log("<p id='error'>Please Select A valid Image File</p>" + "<h4>Note</h4>" + "<span id='error_message'>Only jpeg, jpg and png Images type allowed</span>");
                $("#temp_message2").html("<p id='error'>Please Select A valid Image File</p>" + "<h4>Note</h4>" + "<span id='error_message'>Only jpeg, jpg and png Images type allowed</span>");
                return false;
            }
            //ok
            else {
                $('#temp_message2').hide(1500);
                var reader = new FileReader();
                reader.onload = imageIsLoaded;
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    function imageIsLoaded(e) {
        $("#file").css("color", "green");
        //$('#image_preview').css("display", "block");
        $('#previewing').attr('src', e.target.result);
        //$('#previewing').attr('width', '250px');
        //$('#previewing').attr('height', '230px');
        $('#image_preview').show(1500);
    };
});
