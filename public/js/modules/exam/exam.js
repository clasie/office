var x = "";
/**
 * response types
 */
var TEXT = '1';
var NUMBER = '2';
var DATE = '3';
var QCM_EXCLU = '4';
var QCM_INCLU = '5';
var CONDITIONNAL = '6';
var origin = "EXAMEN";
//var chronoTimeMax = "25";
var max_time = 150; //360;
var isChrono = "off";

$(document).ready(function () {
    openColorBox();
    hideResponsesQuick();
    hideTopImagesContainer();
    $('body').on('click', 'img.color_box_crochet', function () {
        //console.log("ouuuuuuuuups");
        //console.log($(this).attr('src'));
        openColorBoxImage($(this).attr('src'));
    });
    var data = null;
    var questionCounter = null;
    var displayQuestionCounter = null;
    var storedData = null;
    hideOnLoad();
    var tid = null;
    var TYPE_QUESTIONNAIRE = "QUESTIONNAIRE";
    var TYPE_QUESTION = "QUESTION";
    console.log('Exam');    
    /**
     * select questionnaire
     */    
    $("#id_select_a_questionnaire").change(function () {
        //return;
        //isStillUsingGoodSession();
        openColorBox();
        hideOnLoad();
        reset();
        //get data for this questionnaire and display it
        var dataToSave = {
            service: 'GET_SELECTED_QUESTIONNAIRE', // 3
            id_questionnaire: $(this).val()
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_exam/json/readquestionnaires/id_user/1",
            dataType: "json",
            async: false,
            data: dataToSave,
            success: function (response) {
                //console.log("QUESTIONNAIRE SAVE CHANGES remotely: OK");
                fill_in_questionnaire_description_data(response);
                closeColorBox();
            },
            error: function (response) {
                closeColorBox();
                //console.log("QUESTIONNAIRE SAVE CHANGES remotely: KO!");
            }
        });        
    });
    function reset() {
        //cleanse all data
        data = null;
        questionCounter = null;
        displayQuestionCounter = null;
    }
    function fill_in_questionnaire_description_data(response) {
        //isStillUsingGoodSession();
        console.log(response); //.jsTreeNodes[0].text);
        if ($("#id_select_a_questionnaire").val() != "-1") {
            $(".questionnaire_description").show(1000);
            $("#id_questionnaire_label").html(response.jsTreeNodes.nodes[0].text);
            //$("#id_questionnaire_state").html(response.jsTreeNodes.nodes[0].text);
            //$("#id_questionnaire_random").html(response.jsTreeNodes.nodes[0].text);
            $("#id_questionnaire_dates").html(
                response.jsTreeNodes.nodes[0].data.DATE_START + " - " + response.jsTreeNodes.nodes[0].data.DATE_STOP);
            $("#id_questionnaire_comment").html(response.jsTreeNodes.nodes[0].data.COMMENT);
            console.log("--------------->fill_in_questionnaire_description_data");
            console.log(response.jsTreeNodes.nodes[0].data.isChrono);
            console.log(response.jsTreeNodes.nodes[0].data.seconds);
            //chrono management
            if (response.jsTreeNodes.nodes[0].data.isChrono == "on") {
                isChrono = response.jsTreeNodes.nodes[0].data.isChrono;
                max_time = response.jsTreeNodes.nodes[0].data.seconds;
            } else {
                isChrono = "off";
                max_time = 0;
            }
        } else {
            hideOnLoad();
        }
    }
    /**
     * run a questionnaire
     */
    $("#id_run_questionnaire").click(function () {
        //isStillUsingGoodSession();
        openColorBox();
        console.log('id_run_questionnaire');
        showRunExam();
        runTheExam($("#id_select_a_questionnaire").val());
        //closeColorBox();
    });
    //get all data for a questionnaire
    function runTheExam(idQuestionnaire) {
        //alert("IN runTheExam");
        //get questionnaire data
        var dataToSave = {
            service: 'GET_SELECTED_QUESTIONNAIRE_TO_RUN', //1
            id_questionnaire: idQuestionnaire
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_exam/json/readquestionnaires/id_user/1",
            dataType: "json",
            async: false,
            data: dataToSave,
            success: function (response) {
                //alert("runTheExam ok");
                console.log(response);
                //console.log("QUESTIONNAIRE SAVE CHANGES remotely: OK");
                data = response;
                run_questionnaire()
            },
            error: function (response) {
                //alert("runTheExam ko");
                //console.log("QUESTIONNAIRE SAVE CHANGES remotely: KO!");
            }
        });
    }
    function run_questionnaire() {
        console.log("run_questionnaire");
        console.log(data);
        //console.log(data.jsTreeNodes.nodes);
        if (data.jsTreeNodes.nodes != null) {
            //console.log(1);
            fillAQuestionOffSet();
            //console.log(1);
            fillNextQuestion(data);
        } else {
            console.log(data);
            alert('All questionnaires have been deactivated, see your admin(1)');
            //unlog();
        }
    }
    function fillAQuestionOffSet(offsetVal) {
        if (questionCounter == null) {
            questionCounter = 0;
            displayQuestionCounter = 0;
        } else {
            questionCounter = questionCounter + offsetVal;
            displayQuestionCounter = displayQuestionCounter + offsetVal;
        }
        var displayCounter = displayQuestionCounter + " / " + (data.jsTreeNodes.nodes.length - 1);
        $("#counter-quest").html(displayCounter);
        console.log("questionCounter: " + questionCounter);
        console.log("displayQuestionCounter: " + displayQuestionCounter);
        console.log("size: " + data.jsTreeNodes.nodes.length);
        testNextQuestionButton();
    }

    /****************************
     * AUTH -->
     ***************************/

    /** 
     * the entry point of the display entry
     */
    $("#dialog").dialog({
        autoOpen: false,
        height: 350,
        width: 350,
        modal: true,
        buttons: {
            "Go!": function () {
                $('#hidden_login_dialog_to')
                    .val($('#login_dialog_from').val());
                //$('#hidden_origine_dialog_to')
                //    .val('EXAMEN');
                $('#hidden_ticket_dialog_to')
                    .val($('#ticket_number_dialog_from').val());
                $('#hidden_password_dialog_to')
                    .val($('#password_dialog_from').val()).trigger('change');
            }//,
            //"Reset": function () {
            //    reset_session();
            //}
        },
        close: function () {
        }
    });
    /**
     * Log in/Unlog
     */
    $('a#id_log_unlog').click(function (event) {
        event.preventDefault();
        if ($('a#id_log_unlog').text() == "Unlog") {
            //buildJsTree(null);
            destroySessionRunningAuthentificationTest();
            emptyTheTree();
            location.reload();
            // $("#dialog").dialog('open');
        }
        if ($('a#id_log_unlog').text() == "Log in") {
            //buildJsTree(null);
            //emptyTheTree();
            $("#dialog").dialog('open');
        }
    });
    //here make one ajax call to test session
    //destroySessionRunningAuthentificationTest();
    setSessionRunningAuthentificationTest();
    //.... make a global button to delog!!!!!!

    if (displayStuffs) {//the auth has already been made
        //isStillUsingGoodSession();
        //show unlock
        console.log('in displayStuffs ok');
        $('a#id_log_unlog').text('Unlog');
        $("#tab_wrapper").show(1000);
        //ajax();
        console.log('ok logged');
    } else {
        console.log('in displayStuffs ko');
        //show Log in
        $('a#id_log_unlog').text('Log in');
        //clease session
        reset_session();
        //lauches dialog
        $("#dialog").dialog('open');//->will update the hidden field accordingly
    }
    //input hidden has been changed
    $("#hidden_password_dialog_to").bind("change", function () {
        console.log('in near -> authentificationTest');
        authentificationTest(); //synch
    });
    function destroySessionRunningAuthentificationTest() {
        var dataToChallenge = {
            service: 'DISTROY_SESSION_AUTH_CHALLENGE'
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log("DISTROY_SESSION_AUTH_CHALLENGE AUTH OK!");
            },
            error: function (response) {
                console.log("DISTROY_SESSION_AUTH_CHALLENGE AUTH KO!");
            }
        });
    }
    function setSessionRunningAuthentificationTest() {
        console.log('in setSessionRunningAuthentificationTest');
        var dataToChallenge = {
            service: 'SESSION_AUTH_CHALLENGE'
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                if (response.status == "OK") {
                    console.log("OK setSessionRunningAuthentificationTest");
                    displayStuffs = true;
                } else {
                    console.log("KO setSessionRunningAuthentificationTest");
                    displayStuffs = false;
                }
            },
            error: function (response) {
                console.log("SESSION AUTH KO!");
            }
        });
    }
    function authentificationTest() {
        var dataToChallenge = {
            service: 'AUTH_CHALLENGE',
            log: $("#hidden_login_dialog_to").val(),
            pw: $("#hidden_password_dialog_to").val(),
            ticket: $("#hidden_ticket_dialog_to").val(),
            origine: 'EXAMEN'
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log("Auth: OK");
                console.log(response);
                if (response.status == "OK") {
                    $("#xxx").html("");
                    $("#dialog").dialog('close');
                    displayStuffs = true; // 654987
                    $('a#id_log_unlog').text('Unlog');
                    $("#jstree_demo").show();
                    //cloclo
                    $("#tab_wrapper").show(1000);
                    //ajax();
                    location.reload();
                } else {
                    displayStuffs = false;
                    $("#jstree_demo").hide();
                    $('a#id_log_unlog').text('Log in');
                    $("#dialog").dialog('close');
                    $("#dialog").dialog('open');
                    $("#xxx").html("Wrong creds. Double check Log/pw/ticket");
                }
            },
            error: function (response) {
                console.log("AUTH KO!");
            }
        });
    }
    function reset_session() {
        var dataToChallenge = {
            service: 'RESET_AUTH',
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log("RESET_AUTH: OK");
            },
            error: function (response) {
                console.log("RESET_AUTH  KO!");
            }
        });
    }
    function emptyTheTree() {
        //$("#jstree_demo").empty();
        //console.log("qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq");
        //buildJsTree(null);
        return;
        $("#jstree_demo").hide();
        hideAllQuestionsQuestionnaires();
    }
    function hideAllQuestionsQuestionnaires() {
        return;
        //alert('fuck');
        hideResponsesQuick();
        //$("#tab_wrapper").hide();
        $("#id_btn_create_question").hide(1000);
        $("#id_btn_create_questionnaire").hide(1000);
        $("#id_btn_delete_node").hide(1000);
        $("#id_questionnaire").hide(1000);
        $("#id_question").hide(1000);
        $("#id_btn_save_questionnaire").attr("disabled", "disabled");
        $("#tab_wrapper").hide(1000);

    }
    /****************************
     * <-- AUTH
     **************************/
    function fillNextQuestion() {

        if (data.jsTreeNodes.nodes[questionCounter].data.TYPE != TYPE_QUESTION) {
            console.log("inside 1");
            fillAQuestionOffSet(1);
            fillNextQuestion(data);
        } else {
            //$(".exam_questions").toggle(50);

            $("#id_question_text").stop().css({ display: 'none', opacity: 1 }).fadeIn(1000);

            $("#id_question_text").html(data.jsTreeNodes.nodes[questionCounter].data.INTITULE);
            //$(".exam_questions").toggle(1000);
            //if (storedData == null) {
            //    storedData = data.jsTreeNodes.nodes[questionCounter].data;
            //}
            setQuestion(data.jsTreeNodes.nodes[questionCounter].data);
            console.log(data.jsTreeNodes.nodes[questionCounter]);
        }
        
    }
    function testNextQuestionButton() {
        /**
         * NEXT QUESTION
         */
        try {
            if(null == data.jsTreeNodes.nodes[questionCounter + 1]){
                $("#id_next_question").prop('disabled', true);
            }else{
                $("#id_next_question").prop('disabled', false);
            }
        } catch (err) {
            $("#id_next_question").prop('disabled', true);
        }
        /**
         * PREVIOUS QUESTION
         */
        try {
            if (null == data.jsTreeNodes.nodes[questionCounter - 1]) {
                console.log("negatif 1: questionCounter - 1 : " + (questionCounter - 1));
                $("#id_previous_question").prop('disabled', true);
            } else {
                console.log("negatif 2: questionCounter - 1 : " + (questionCounter - 1));
                $("#id_previous_question").prop('disabled', false);
            }
        } catch (err)
        {
            $("#id_previous_question").prop('disabled', true);
        }
        try {

            if (data.jsTreeNodes.nodes[questionCounter - 1].data.TYPE != TYPE_QUESTION) {
                $("#id_previous_question").prop('disabled', true);
            } else {
                $("#id_previous_question").prop('disabled', false);
            }
        } catch (err) {
            $("#id_previous_question").prop('disabled', true);
        }
    }
    $("#id_next_question").click(function () {
        isStillUsingGoodSession();
        console.log('id_next_question');
        saveCurrentAnswers();
        fillAQuestionOffSet(1);
        fillNextQuestion(data);
    });
    $("#id_save_and_quit_the_exam").click(function () {
        isStillUsingGoodSession();
        console.log('id_save_and_quit_the_exam');
        saveCurrentAnswers();
        //todo unlog.....
        destroySessionRunningAuthentificationTest();
        emptyTheTree();
        location.reload();
    });
    $("#id_previous_question").click(function () {
        isStillUsingGoodSession();
        console.log('id_previous_question');
        saveCurrentAnswers();
        fillAQuestionOffSet(-1);
        fillNextQuestion(data);
    });
    /**
     * cancel a questionnaire
     */
    $("#id_cancel_questionnaire").click(function () {// 007
        isStillUsingGoodSession();
        openColorBox();
        $("#id_select_a_questionnaire").val("-1");
        console.log('id_cancel_questionnaire');
        hideOnLoad();
        reset();
        closeColorBox();
    });
    /**
     * fill in select questionnaires
     */
    //alert(5);
    if (displayStuffs) {
        //alert(4);
        ajax_fill_in_questionnaire_choices();
    }
    /**
     * fill in select questionnaires
     */    
    function ajax_fill_in_questionnaire_choices() {
        console.log('in ajax_fill_in_questionnaire_choices');
        //alert(7);
        var dataToSave = {
            service: 'GET_ALL_QUESTIONNAIRES'//, //2  <------------ add id questionnaire
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_exam/json/readquestionnaires/id_user/1",
            //contentType: "application/json",
            dataType: "json",
            data: dataToSave,
            async: false,
            success: function (response) {
                if (response.jsTreeNodes == null) {
                    console.log("jtree est null");
                    alert('All questionnaires have been deactivated, see your admin(0)');
                    unlog();
                } else {
                    console.log("go to fill_in_questionnaire_choices");
                    fill_in_questionnaire_choices(response);
                }
                //alert(5);
            },
            error: function (response) {
                //alert(response);
                console.log("ajax 3 -------------------------");
                console.log(response);
            }
        });
    }
    function fill_in_questionnaire_choices(data) {

        console.log('in fill_in_questionnaire_choices');
        console.log(data.jsTreeNodes.nodes);

        if (data.jsTreeNodes.nodes != null) {

            for (i = 0, j = data.jsTreeNodes.nodes.length; i < j; i++) {
                //r.push(data.instance.get_node(data.selected[i]).data.TYPE);
                //console.log(data.jsTreeNodes.nodes[i]);
                //console.log(data.jsTreeNodes.nodes[i].data);
                //fill select with questionnaires
                var options = $("#id_select_a_questionnaire");
                if (data.jsTreeNodes.nodes[i].data.TYPE == TYPE_QUESTIONNAIRE) {
                    options.append($("<option />")
                        .val(data.jsTreeNodes.nodes[i].id)
                        .text(data.jsTreeNodes.nodes[i].text));
                }
            }
        } else {
            alert('All questionnaires have been deactivated, see your admin(3)');
            unlog();
        }
    }
    function emptyImages() {
        $(".image_container").empty();
    }
    function addImage(url) {
        console.log(url);
        html = "   <div id='1' class='image_loaded_exam' >          " +
               "         <img class='color_box_crochet' src='" + url + "' />           " +
               "   </div>  ";
        $(".image_container").append(html);
    }
    function addImages(images) {
        showTopImagesContainer(images);
        console.log("addImages -----------> ");
        console.log(images);
        for (i = 0; i < images.length; ++i) {
            addImage(images[i].file_url);
        }
    }
    /**
     * Rotrate
     */
    function rotate($el, degrees) {
        $el.css({
            '-webkit-transform': 'rotate(' + degrees + 'deg)',
            '-moz-transform': 'rotate(' + degrees + 'deg)',
            '-ms-transform': 'rotate(' + degrees + 'deg)',
            '-o-transform': 'rotate(' + degrees + 'deg)',
            'transform': 'rotate(' + degrees + 'deg)',
            'zoom': 1
        });
    }
    /**
     * chrono stuffs start --->
     */    
    //var max_time = 15; //360;
    var counter = 1;
    var amount = 0;
    var totamount = $(".progress_questions").width();
    function chronoPreStart() {
        tid = setTimeout(chronoStart, 25);
    }
    function chronoStart() {
        tid = setTimeout(chronoStart, 25); // repeat myself
        amount = (counter++ * ( totamount / max_time )) / 40;
        $("#id_left_part").width(amount);
        var seconds = max_time - (counter / (40));
        //rotate
        //rotate($("#titlerocket1"), amount);
        rotate($("#titlerocket2"), amount);
        if (seconds >= 0) {
            // multiply by 1000 because Date() requires miliseconds
            var date = new Date(seconds * 1000);
            var hh = date.getUTCHours();
            var mm = date.getUTCMinutes();
            var ss = date.getSeconds();
            // This line gives you 12-hour (not 24) time
            if (hh > 12) { hh = hh - 12; }
            // These lines ensure you have two-digits
            if (hh < 10) { hh = "0" + hh; }
            if (mm < 10) { mm = "0" + mm; }
            if (ss < 10) { ss = "0" + ss; }
            // This formats your string to HH:MM:SS
            var t = hh + "<b> H </b>" + mm + "<b> M </b>" + ss;
            $("#id_text_chrono").html("<b></b> " + t);
        } else {
            $("#id_text_chrono").html("<b></b> " + "<b>Exam closed</b>");
        }
        if (amount >= totamount) {            
            abortTimer();
            $("#id_save_and_quit_the_exam").click();
        }
    }
    function abortTimer() { // to be called when you want to stop the timer
        if (tid != null) {
            clearTimeout(tid);
            amount = 0;
            counter = 0;
        }
    }
    /**
     * chrono stuffs stop <---
     */
    function hideOnLoad() {
        $(".exam_questions").hide(1000);
        $(".questionnaire_description").hide(1000);
        $(".progress_questions").hide(1000);
        abortTimer();
    }
    function showRunExam() {
        closeColorBox();
        $(".exam_questions").show(1000);
        $(".questionnaire_description").hide(1000);
        $(".progress_questions").show(1000);
        //closeColorBox();
        if (isChrono == "on") {
            chronoPreStart();//csi 002
        }
    }
    function openColorBoxImage(url) {
        targetImage = "<img src='" + url + "' />";
        $.colorbox({
            escKey: true,
            overlayClose: false,
            width: "auto",
            height: "auto",
            html: targetImage,
            closeButton: true,
            fadeOut: 1000,
            opacity: 0.2
        });
    }
    function openColorBox() {
        $.colorbox({
            escKey: false,
            overlayClose: false,
            width: "290px",
            height: "200px",
            html: "<h6><span style='color: rgba(-31,49,102, 0.5);margin-left:18px'>Working on it...</span> <img src='/public/images/loader_gif2.gif' style='width:109px;height:96px'></h6>",
            closeButton: false,
            fadeOut: 1000,
            opacity: 0.2
        });
    }
    function closeColorBox() {//return;
        setTimeout('$.colorbox.close()', 1500);
    }
    /**
     * QUESTION SAVE CHANGES locally csi
     */
    function saveCurrentAnswers() 
    {
        console.log('9');
        console.log(data.jsTreeNodes.nodes[questionCounter]); //.data); 
        /**
         * test the response type
         */
        var selectedVal = data.jsTreeNodes.nodes[questionCounter].data.TYPE_REPONSE; //$("#id_question_type_reponse").val();
        console.log('TYPE_RESPONSE: ' + selectedVal);
        switch (selectedVal) {
            case TEXT:
                data.jsTreeNodes.nodes[questionCounter].data.ANSWERED_RESPONSE_TXT
                    = $("#id_good_answer").val();
                console.log('TEXT from save');
                break;
            case NUMBER:
                data.jsTreeNodes.nodes[questionCounter].data.ANSWERED_RESPONSE_NUMBER
                    = $("#number_good_answer").val();
                console.log('NUMBER from save');
                break;
            case DATE:
                data.jsTreeNodes.nodes[questionCounter].data.ANSWERED_RESPONSE_DATE
                    = $("#date_picker_good_answer").val();
                console.log('DATE from save');
                break;
            case QCM_INCLU:
                runThrougAlllQCMIncl(".qcm_incl ul li");
                console.log('QCM_INCLU from save ---in----> ');
                break;
            case QCM_EXCLU: //csi1
                runThrougAlllQCMExcl(".qcm_exc ul li");
                console.log('QCM_EXCLU from save ---ex----> ');
                break;
            case CONDITIONNAL:
                console.log('CONDITIONNAL from save');
                break;
            default:
                console.log('UNKNOWN response type selected!  from save');
        }
        /**
         * QUESTION SAVE CHANGES remotely   //  007
         */
        var dataToSave = {
            service: 'SAVE_RESPONSE',
            //text: node.text,
            //intitule: node.data.INTITULE,
            answered_response_txt: data.jsTreeNodes.nodes[questionCounter].data.ANSWERED_RESPONSE_TXT,
            answered_response_date: data.jsTreeNodes.nodes[questionCounter].data.ANSWERED_RESPONSE_DATE,
            answered_response_number: data.jsTreeNodes.nodes[questionCounter].data.ANSWERED_RESPONSE_NUMBER,
            responsetype: data.jsTreeNodes.nodes[questionCounter].data.TYPE_REPONSE,
            qcmExclu: data.jsTreeNodes.nodes[questionCounter].data.EXCLUSIVE_TYPE_VALUES,
            qcmInclu: data.jsTreeNodes.nodes[questionCounter].data.INCLUSIVE_TYPE_VALUES,
            id_question: data.jsTreeNodes.nodes[questionCounter].id, //node.id //
            id_questionnaire: $("#id_select_a_questionnaire").val()
        }
        console.log("-------------> data to save");
        console.log(dataToSave);
        $.ajax({
            type: "POST",
            url: "/public/ajax_exam/json/readquestionnaires/id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToSave,
            success: function (response) {
                console.log("SAVE_RESPONSE: OK");
                console.log(response);
            },
            error: function (response) {
                console.log("SAVE_RESPONSE: KO!");
                console.log(response);
                //unlog
                alert("All questionnaires have been deactivated/modified, see your admin(2) or relog to upload the last questionaire's version");
                unlog();

            }
        });
    }
    function unlog() {
        destroySessionRunningAuthentificationTest();
        emptyTheTree();
        location.reload();
    }
    function runThrougAlllQCMExcl(id) {// 007    perte de l id csi 007
        console.log("||||||||||||||||||||||||| runThrougAlllQCMExcl");
        console.log(data.jsTreeNodes.nodes[questionCounter].data.EXCLUSIVE_TYPE_VALUES);
        //data.jsTreeNodes.nodes[questionCounter].data.EXCLUSIVE_TYPE_VALUES = [];
        var counter = 0;
        $(id).each(function () { // txta_custom
            var obj = {
                id_qcm_response:
                    data.jsTreeNodes.nodes[questionCounter].data.EXCLUSIVE_TYPE_VALUES[counter].id_qcm_response,
                text: $(this).children("textarea").val(),
                isSelectedAsAnswer: $(this).children(".qcm_inside_li_cbox").children(".xxx").is(':checked'),
                //isGood: $(this).children(".qcm_inside_li_cbox").children(".xxx").is(':checked'),
                type: ""
            };
            data.jsTreeNodes.nodes[questionCounter].data.EXCLUSIVE_TYPE_VALUES[counter] = obj;
            counter++
        });
        console.log(data.jsTreeNodes.nodes[questionCounter].data.EXCLUSIVE_TYPE_VALUES);
    }
    function runThrougAlllQCMIncl(id, node) {// 007
        //data.jsTreeNodes.nodes[questionCounter].data.INCLUSIVE_TYPE_VALUES = [];
        var counter = 0;
        $(id).each(function () { // txta_custom
            var obj = {
                id_qcm_response:
                    data.jsTreeNodes.nodes[questionCounter].data.INCLUSIVE_TYPE_VALUES[counter].id_qcm_response,
                text: $(this).children("textarea").val(),
                isSelectedAsAnswer: $(this).children(".qcm_inside_li_cbox").children(".xxx").is(':checked'),
                //isGood: $(this).children(".qcm_inside_li_cbox").children(".xxx").is(':checked'),
                type: ""
            };
            console.log("QCMMMMM val");
            console.log(obj);
            data.jsTreeNodes.nodes[questionCounter].data.INCLUSIVE_TYPE_VALUES[counter] = obj;
            counter++
        });
    }
    function hideTopImagesContainer() {
        $("#id_container_images_exam").hide(1000);
    }
    function showTopImagesContainer(images) {
        if (images.length > 0) {
            hideTopImagesContainer();
            $("#id_container_images_exam").show(1500);
        } else {
            hideTopImagesContainer();
        }
    }
    function setQuestion(datas) { //csi new
        console.log('xxxxx');
        console.log(datas);
        console.log('setQuestion ----> ' + datas.TYPE);
        console.log('type response ' + datas.TYPE_REPONSE);
        hideResponsesQuick();
        emptyImages();
        addImages(datas.IMAGES);
        switch (datas.TYPE_REPONSE) {
            case TEXT:
                $(".good_answer_text_wrapper").show(1000);
                $("#id_good_answer").val(datas.ANSWERED_RESPONSE_TXT);
                console.log('TEXT');
                break;
            case NUMBER:
                $(".good_answer_number_wrapper").show(1000);
                $("#number_good_answer").val(datas.ANSWERED_RESPONSE_NUMBER);
                console.log('NUMBER');
                break;
            case DATE:
                $(".good_answer_date_wrapper").show(1000);
                $("#date_picker_good_answer").val(datas.ANSWERED_RESPONSE_DATE);
                console.log('DATE');
                break;
            case QCM_INCLU:
                $(".good_answer_qcm_cbx_wrapper").show(1000);
                /**
                 * inclusive qcm 
                 */
                console.log('QCM_INCLU -> ' + datas.INCLUSIVE_TYPE_VALUES.length);
                createQCMInclu(datas.INCLUSIVE_TYPE_VALUES);
                console.log('QCM_INCLU');
                break;
            case QCM_EXCLU:
                $(".good_answer_qcm_radio_wrapper").show(1000);
                /**
                 * exclusive qcm 
                 */
                console.log('QCM_EXCLU -> ' + datas.EXCLUSIVE_TYPE_VALUES.length);
                createQCMExclu(datas.EXCLUSIVE_TYPE_VALUES);
                console.log('QCM_EXCLU');
                break;
            case CONDITIONNAL:
                $(".good_answer_conditionnal_wrapper").show(1000);
                console.log('CONDITIONNAL');
                break;
            default:
                console.log('UNKNOWN response type selected!');
        }
        console.log('oups1');
    }
    function hideResponsesQuick() {
        $(".good_answer_text_wrapper").hide(500);
        $(".good_answer_date_wrapper").hide(500);
        $(".good_answer_number_wrapper").hide(500);
        $(".good_answer_qcm_cbx_wrapper").hide(500);
        $(".good_answer_qcm_radio_wrapper").hide(500);
        $(".good_answer_conditionnal_wrapper").hide(500);
    }
    function createQCMInclu(incluValues) {

        if (incluValues == null) {
            console.log('createQCMInclu: incluValues sont nulles');
            cboxValue = "";
            var x =
            '    <li class="ui-state-default">' +
            '        <div class="qcm_inside_li_cbox">' +
            '        <input ' + cboxValue + 'type="checkbox" class="xxx" name="option1" value="Milk">' +
            '        </div>' +
            '        <textarea readonly class="txta_custom" >' + ' New incl possible answer' + '</textarea>' +
            '    </li>    ';
            $(".qcm_incl ul").append(x);
            return;
        } else {
            console.log('createQCMInclu: incluValues NON nulles');
            console.log(incluValues);
            $(".qcm_incl ul li").remove();
        }
        console.log(incluValues);

        for (i = 0; i < incluValues.length; ++i) {
            console.log("the value: " + incluValues[i].text);
            var cboxValue = "";
            if (incluValues[i].isSelectedAsAnswer == true) {
                console.log(' incluValues is TRUE ' + incluValues[i].isSelectedAsAnswer);
                cboxValue = " CHECKED ";
            } else {
                console.log(' incluValues is FALSE ' + incluValues[i].isSelectedAsAnswer);
                cboxValue = "  ";
            }
            var x =
            '    <li class="ui-state-default">' +
            '        <div class="qcm_inside_li_cbox">' +
            '        <input ' + cboxValue + 'type="checkbox" class="xxx" name="option1" value="Milk">' +
            '        </div>' +
            '        <textarea readonly class="txta_custom" >' + incluValues[i].text + '</textarea>' +
            '    </li>    ';
            $(".qcm_incl ul").append(x);
        }
    }
    function createQCMExclu(excluValues) { //<input type="radio" name="option1" value="Milk">' + 
        console.log('createQCMExclu');
        console.log(excluValues);
        if (excluValues == null) {
            cboxValue = "";
            var x =
            '    <li class="ui-state-default">' +
            '        <div class="qcm_inside_li_cbox">' +
            '        <input ' + cboxValue + 'type="radio" class="xxx" name="option1" value="Milk">' +
            '        </div>' +
            '        <textarea readonly class="txta_custom" >' + ' New excl possible answer' + '</textarea>' +
            '    </li>    ';
            $(".qcm_exc ul").append(x);
            return;
        } else {
            $(".qcm_exc ul li").remove();
        }
        $(".qcm_exc ul li").remove();

        for (i = 0; i < excluValues.length; ++i) {

            var cboxValue = "";
            if (excluValues[i].isSelectedAsAnswer == true) {
                console.log(' excluValues is TRUE ' + excluValues[i].isSelectedAsAnswer);
                cboxValue = " CHECKED ";
            } else {
                console.log(' excluValues is FALSE ' + excluValues[i].isSelectedAsAnswer);
                cboxValue = " ";
            }

            console.log("the value: " + excluValues.text);
            var x =
            '    <li class="ui-state-default">' +
            '        <div class="qcm_inside_li_cbox">' +
            '        <input ' + cboxValue + ' type="radio" class="xxx" name="option1" value="Milk">' +
            '        </div>' +
            '        <textarea readonly class="txta_custom" >' + excluValues[i].text + '</textarea>' +
            '    </li>    ';
            $(".qcm_exc ul").append(x);
        }

    }
    /** 
     * date good response 
     */
    $("#date_picker_good_answer").datepicker({
        dateFormat: 'dd/mm/yy'
    });
    closeColorBox();
});
function isStillUsingGoodSession() {
    console.log('isStillUsingGoodSession');
    var dataToChallenge = {
        service: 'ORIGIN_CHALLENGE',
        origine: origin
    }
    $.ajax({
        type: "POST",
        url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
        //contentType: "application/json",
        dataType: "json",
        async: false,
        data: dataToChallenge,
        success: function (response) {
            console.log("EXAMEN: OK");
            if (response['error'] == '1') {
                location.reload();
            }
        },
        error: function (response) {
            console.log("EXAMEN KO!");
        }
    });
}
