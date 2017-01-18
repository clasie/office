/*
Response
   questionnaire-------------------------------------
      jstreenodes
         nodes
            [0]object
               data
                  -
                  - id question 1
                  - QUESTIONNAIRE: type
                  -
            [1]object
               data
                  -
                  -id question 2
                  - QUESTION: type
                  -
            ...

            [n]object
               data
                  -
                  -
                  - QUESTION: type
                  -

   responses-------------------------------------
      [id_question 1]
         [id_user1]
            [0]object
               -
               - id question 1
               -
               - points
               -

         ...

         [id_user j]
            [0]object
               -
               - id question 1
               -
               - points
               -
      ...

      [id_question n]
         [id_user1]
            [0]object
               -
               - id question n
               -
               - points
               -

         ...

         [id_user j]
            [0]object
               -
               - id question n
               -
               - points
               -

*/
var displayStuffs = false;
var origin = "REPORT";
var examUsersForOneAuthor = null;
var QUESTIONNAIRE = "QUESTIONNAIRE";
var QUESTION = "QUESTION";
var EXAM_USERS_CRUDS_UPDATE = "EXAM_USERS_CRUDS_UPDATE";
var EXAM_USERS_CRUDS_NEW = "EXAM_USERS_CRUDS_NEW";
var EXAM_USERS_CRUDS_DELETE = "EXAM_USERS_CRUDS_DELETE";
var users = [];

var question_types = {};
question_types[0] = "IS A QUESTIONNAIRE NODE!";
question_types[1] = "TEXT";
question_types[2] = "NUMBER";
question_types[3] = "DATE";
question_types[4] = "QCM_EXCLU";
question_types[5] = "QCM_INCLU";
question_types[6] = "CONDITIONNAL";

var question_types_to_response_fields = {};
question_types_to_response_fields[0] = "IS A QUESTIONNAIRE NODE!";
question_types_to_response_fields[1] = "resp_text";
question_types_to_response_fields[2] = "resp_text";
question_types_to_response_fields[3] = "resp_datetime";
question_types_to_response_fields[4] = "rcm";
question_types_to_response_fields[5] = "rcm";
question_types_to_response_fields[6] = "CONDITIONNAL";
/**
 * objects to build the top result before ed detailled version
 */
var objUserLines = [];
var tot_amount = 0;
var objQuestionnaire = {
    "Name": "",
    "TotPoints": 0,
}
var objUserLinesHeader = {
    "Title1": "User",
    "Title2": "PointsAmount",
    "Title3": "Percentage",
    "Title4": "Order",
}


$(document).ready(function () {

    openColorBox();

    $('body').on('click', 'img.color_box_crochet', function () {
        //console.log("ouuuuuuuuups");
        //console.log($(this).attr('src'));
        openColorBoxImage($(this).attr('src'));
    });

    /****************************
     * AUTH --> start
     ***************************/

    /** 
     * log/pw
     */
    $("#dialog").dialog({
        autoOpen: false,
        height: 300,
        width: 350,
        modal: true,
        buttons: {
            "Go!": function () {
                $('#hidden_login_dialog_to')
                    .val($('#login_dialog_from').val());
                $('#hidden_password_dialog_to')
                    .val($('#password_dialog_from').val()).trigger('change');
            }
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
            destroySessionRunningAuthentificationTest();
            location.reload();
        }
        if ($('a#id_log_unlog').text() == "Log in") {
            $("#dialog").dialog('open');
        }
    });
    //here make one ajax call to test session
    setSessionRunningAuthentificationTest();
    if (displayStuffs) {//the auth has already been made
        //show unlock
        $('a#id_log_unlog').text('Unlog');
        $("#tab_wrapper").show(1000);
        closeColorBox();
    } else {
        //show Log in
        $('a#id_log_unlog').text('Log in');
        //clease session
        reset_session();
        //lauches dialog
        closeColorBox();
        $("#dialog").dialog('open');//->will update the hidden field accordingly
    }
    //input hidden has been changed
    $("#hidden_password_dialog_to").bind("change", function () {
        authentificationTest(); //synch
    });
    function destroySessionRunningAuthentificationTest() {
        console.log("->destroySessionRunningAuthentificationTest");
        var dataToChallenge = {
            service: 'DISTROY_SESSION_AUTH_CHALLENGE',
            origin: origin
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_report/json/readquestionnaires", ///id_user/1",
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
        console.log("->setSessionRunningAuthentificationTest");
        var dataToChallenge = {
            service: 'SESSION_AUTH_CHALLENGE',
            origin: origin
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_report/json/readquestionnaires", ///id_user/1",
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
    function authentificationTest() {
        console.log("->authentificationTest");
        var dataToChallenge = {
            service: 'AUTH_CHALLENGE',
            log: $("#hidden_login_dialog_to").val(),
            pw: $("#hidden_password_dialog_to").val(),
            origin: origin
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_report/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log("Auth: OK");
                console.log(response);
                if (response.status == "OK") {
                    $("#xxx").html("");
                    console.log("Auth: OK");
                    $("#dialog").dialog('close');
                    console.log("Auth: OK");
                    displayStuffs = true; // 654987
                    console.log("Auth: OK");
                    $('a#id_log_unlog').text('Unlog');
                    console.log("Auth: OK");
                    getAuthorUsers();
                } else {
                    displayStuffs = false;
                    //$("#jstree_demo").hide();
                    $('a#id_log_unlog').text('Log in');
                    $("#dialog").dialog('close');
                    $("#dialog").dialog('open');
                    $("#xxx").html("Wrong creds!");
                    //$(".buttons_tree").hide(1000);
                }
            },
            error: function (response) {
                console.log("AUTH KO!");
            }
        });
    }
    function reset_session() {
        console.log("->reset_session");
        var dataToChallenge = {
            service: 'RESET_AUTH',
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_report/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log("RESET_AUTH: OK");
            },
            error: function (response) {
                console.log("RESET_AUTH  KO!");
                $(".buttons_tree").hide(1000);
            }
        });
    }
    /****************************
     * AUTH <-- Stop
     ***************************/

    /****************************
     * COLOR BOX Start -->
     ***************************/
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
    /****************************
     * COLOR BOX <- Stop
     ***************************/


    /**
     * functionnals...
     */
    getAuthorUsers();

    function setExUsers(setExUsers) {
        examUsersForOneAuthor = setExUsers;
        var list = $("#id_list_author_users");
        list.empty();
        //set top choice label
        list.append(
            new Option(
               "Select one questionnaire's author",
               "-1"
            ));
        for (i = 0; i < examUsersForOneAuthor.length; ++i) {
            list.append(
                new Option(
                   examUsersForOneAuthor[i].user_log,
                   examUsersForOneAuthor[i].user_id
                ));
        }
        //getLinkedUsers();
    }
    
    function setAuthorQuestionnaires(questionnaires) {
        //examUsersForOneAuthor = setExUsers;
        var list = $("#id_list_questionnaires");
        list.empty();
        //set top choice label
        list.append(
            new Option(
               "Select one questionnaire",
               "-1"
            ));
        for (i = 0; i < questionnaires.length; ++i) {
            list.append(
                new Option(
                   questionnaires[i].tree_label,
                   questionnaires[i].id
                ));
        }
    }
    function setExamUsers(users) {
        //examUsersForOneAuthor = setExUsers;
        var list = $("#id_list_exam_users");
        list.empty();
        for (i = 0; i < users.length; ++i) {
            list.append(
                new Option(
                   users[i].user_log,
                   users[i].user_id
                ));
        }
    }
    function getSelectedUsers() {
        //examUsersForOneAuthor = setExUsers;
        var list = $("#id_list_exam_users");
        var selected_users = [];
        for (i = 0; i < users.length; ++i) {
            selected_users[users[i].user_id] = users[i].user_log;
        }
        return selected_users;
    }
    function cleanseLists(level) {
        if (1 == level) {
            $("#id_list_questionnaires").empty();
            $("#id_list_exam_users").empty();
            $("#questionn_wrapper_id").html("");
            $("#questionnaire_label_id").html("");
            $("#questionnaire_label_id_content").html("");

        } else if (2 == level) {
            $("#id_list_questionnaires").empty();
            $("#id_list_exam_users").empty();
            $("#questionn_wrapper_id").html("");
            $("#questionnaire_label_id").html("");
            $("#questionnaire_label_id_content").html("");
        }
    }
    /**
     * Author changed
     */
    $("#id_list_author_users").change(function () {

        $("#questionn_wrapper_id").html("");
        $("#questionnaire_label_id").html("");
        $("#questionnaire_label_id_content").html("");

        var id_author = $("#id_list_author_users option:selected").val();
        if ("-1" != id_author) {
            console.log("changed != -1, : id_list_author_users ok ");
            getAuthorQuestionnaires(id_author);
        } else {
            console.log("changed == -1, : id_list_author_users ok ");
            $("#id_list_questionnaires").empty();
            $("#id_list_exam_users").empty();
        }
    });
    /**
     * Questionnaire changed
     */
    $("#id_list_questionnaires").change(function () {

        $("#questionn_wrapper_id").html("");
        $("#questionnaire_label_id").html("");
        $("#questionnaire_label_id_content").html("");

        var id_author =
            $("#id_list_author_users option:selected").val();
        var id_questionnaire =
            $("#id_list_questionnaires option:selected").val();

        if ("-1" != id_questionnaire){ // && "-1" != id_author) {
            console.log("changed == -1, : id_list_questionnaires CHANGED ok");
            getAuthorQuestionnaireExamUsers(id_questionnaire, id_author);
        } else {
            console.log("id_list_questionnaires == -1 CHANGED ko");
            $("#id_list_exam_users").empty();
        }
    });
    /**
     * show the report(s)
     */
    $('#id_btn_show_report').click(function (event) {
        showReport();
    });
    $('#id_btn_show_report1').click(function (event) {
        showReport();
    });
    function showReport() {
        $("#questionn_wrapper_id").html("");
        $("#questionnaire_label_id").html("");
        $("#questionnaire_label_id_content").html("");

        console.log('id_btn_show_report');
        //check if  author not empty
        var error_message = "";
        var error = false;
        var id_author;
        var id_questionnaire;

        //AUTHOR
        if ($("#id_list_author_users :selected").length == 0) {
            error = true;
            error_message += " Select one author ";
        }
        id_author = $("#id_list_author_users").val();
        if ($("#id_list_author_users").val() == "-1") {
            error = true;
            error_message += " Select one author ";
        }

        //QUESTIONNAIRE
        if ($("#id_list_questionnaires :selected").length == 0) {
            error = true;
            error_message += " Select one questionnaire ";
        }
        id_questionnaire = $("#id_list_questionnaires").val();
        if ($("#id_list_questionnaires").val() == "-1") {
            error = true;
            error_message += " Select one questionnaire ";
        }

        //EXAM USERS
        if ($("#id_list_exam_users :selected").length == 0) {
            error = true;
            error_message += " Select user(s) ";
        }

        //ERRORS
        if (error) {
            $('#report_error_message_id').hide(1000);
            $("#report_error_message_id").html(error_message);
            $('#report_error_message_id').show(1000);
            console.log("ERROR " + error_message);
            return;
        } else {
            $('#report_error_message_id').hide(1000);
            $("#report_error_message_id").html("");
            console.log("NO ERROR " + error_message);
        }

        //lauches the process
        users = [];
        var counter = 0;
        $("#id_list_exam_users :selected").map(function (i, el) {
            console.log("in map");
            users.push($(el).val()); //[] = $(el).text();
        });
        //console.log(users);
        setReport(id_author, id_questionnaire, users)
    }
    /**
     * set report
     */
    function setReport(id_author, id_questionnaire, users) {
        var dataToChallenge = {
            service: 'GET_REPORT_4_USERS',
            id_author: id_author,
            id_questionnaire: id_questionnaire,
            users: users,
            origin: origin
        }
        console.log(users);
        $.ajax({
            type: "POST",
            url: "/public/ajax_report/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log("-----------> GLOBAL Response");
                console.log(response);
                setUserReport(response);
                //build the report
                builReport();
            },
            error: function (response) {
                console.log("setReport KO!");
                return null;
            }
        });
    }
    function setUserReport(response) {

        $("#questionn_wrapper_id").html("");
        $("#questionnaire_label_id").html("");
        tot_amount = 0;
        for (i = 0; i < response.questionnaire.jsTreeNodes.nodes.length; ++i)
        {
            
            /**
             * QUESTIONNAIRE
             */
            if (response.questionnaire.jsTreeNodes.nodes[i].data.TYPE == QUESTIONNAIRE) {
                console.log(QUESTIONNAIRE);

                $("#questionnaire_label_id")
                    .html("Questionnaire");

                $("#questionnaire_label_id_content")
                    .html(response.questionnaire.jsTreeNodes.nodes[i].text);

            /**
             * QUESTION
             */
            } else if (response.questionnaire.jsTreeNodes.nodes[i].data.TYPE == QUESTION) {
                
                console.log(QUESTION); //  INTITULE
                console.log(response.questionnaire.jsTreeNodes.nodes[i]);
                //continue;
                $("#questionn_wrapper_id").append(
                   getQuestionMatrice(
                       response,
                       response.questionnaire.jsTreeNodes.nodes[i]
                   )
                 );
                //response
                console.log("---------------|||TOTAMOUNT||||&------------------------> js tot"); //8888
                console.log(response.questionnaire.jsTreeNodes.nodes[i].tot_points);
                tot_amount = response.questionnaire.jsTreeNodes.nodes[i].tot_points;
                console.log(tot_amount);
            }
        }
        console.log("---------------|||||||||||&------------------------> js tot");
        //console.log(response.questionnaire.jsTreeNodes.nodes[i].data.Tot_points);
        console.log(objUserLines);
    }
    /**
     * clear the report(s)
     */
    $('#id_btn_cleanse_report').click(function (event) {
        cleanseReport();
    });
    $('#id_btn_cleanse_report1').click(function (event) {
        cleanseReport();
    });
    function cleanseReport() {
        console.log('id_btn_cleanse_report');
        $("#questionn_wrapper_id").html("");
        $("#questionnaire_label_id").html("");
        $("#questionnaire_label_id_content").html("");
        $("#id_js_report").html("");
    }
    function getCompareResponseResultLabel(
        response_type,
        current_questionnaire,
        current_reponse)
    {
        var tesponseJs = {label_response:"", boolean_response:"white"};
        //question_types[0] = "IS A QUESTIONNAIRE NODE!";
        if (question_types[response_type] == question_types[0]) {
            console.log("IS A QUESTIONNAIRE NODE");

            tesponseJs.boolean_response = false;
            tesponseJs.label_response = "Error, it s a questionnaire";

            return tesponseJs;

            //question_types[1] = "TEXT";
        } else if (question_types[response_type] == question_types[1]) {
            console.log("IS A TEXT NODE");
            if (current_reponse.trim() == current_questionnaire.data.GOOD_RESPONSE_TXT) {
                tesponseJs.boolean_response = true;
                tesponseJs.label_response = current_questionnaire.data.GOOD_RESPONSE_TXT;
                return tesponseJs; //true;
            } else {
                tesponseJs.boolean_response = false;
                tesponseJs.label_response = current_questionnaire.data.GOOD_RESPONSE_TXT;
                return tesponseJs; //false;
            }

            //question_types[2] = "NUMBER";
        } else if (question_types[response_type] == question_types[2]) {
            console.log("IS A NUMBER NODE");
            if (current_reponse.trim() == current_questionnaire.data.GOOD_RESPONSE_NUMBER) {
                tesponseJs.boolean_response = true;
                tesponseJs.label_response = current_questionnaire.data.GOOD_RESPONSE_NUMBER;
                return tesponseJs; //true;
            } else {
                tesponseJs.boolean_response = false;
                tesponseJs.label_response = current_questionnaire.data.GOOD_RESPONSE_NUMBER;
                return tesponseJs; //false;
            }

            //question_types[3] = "DATE";
        } else if (question_types[response_type] == question_types[3]) {
            console.log("IS A DATE NODE");
            if (current_reponse.trim() == current_questionnaire.data.GOOD_RESPONSE_DATE) {
                tesponseJs.boolean_response = true;
                tesponseJs.label_response = current_questionnaire.data.GOOD_RESPONSE_DATE;
                return tesponseJs; //true;
            } else {
                tesponseJs.boolean_response = false;
                tesponseJs.label_response = current_questionnaire.data.GOOD_RESPONSE_DATE;
                return tesponseJs; //false;
            }

            //question_types[4] = "QCM_EXCLU";
        } else if (question_types[response_type] == question_types[4]) {
            console.log("IS A QCM_EXCLU NODE");
            var vals = current_questionnaire.data.EXCLUSIVE_TYPE_VALUES
            console.log(vals);

            var googds = [];
            for (j = 0; j < vals.length; j++) {
                if (vals[j].isGood) {
                    if (current_reponse == vals[j].id_qcm_response) {
                        tesponseJs.boolean_response = true;
                        tesponseJs.label_response = vals[j].text;
                        console.log("tttttttttttttexte: " + vals[j].text);
                        return tesponseJs; //true;
                        //console.log("is good");
                        //console.log(vals[j]);
                        //googds[j] = vals[j].id_qcm_response;
                    }
                } else {
                    console.log("NOT is good");
                    //console.log(vals[j]);
                }
            }
            tesponseJs.boolean_response = false;
            console.log("1");
            tesponseJs.label_response = "NOT good";
            console.log("2");
            return tesponseJs; //false; //"QCM_EXCLU"; //googds.join(); //"QCM_EXCLU";

            //question_types[5] = "QCM_INCLU";
        } else if (question_types[response_type] == question_types[5]) {// <---------------
            console.log("IS A QCM_INCLU NODE<--------------------");
            var vals = current_questionnaire.data.INCLUSIVE_TYPE_VALUES
            var googds = [];
            var counterGoods = 0;
            for (j = 0; j < vals.length; j++) {
                if (vals[j].isGood) {
                    console.log("is good");
                    console.log(vals[j]);
                    googds[counterGoods++] = vals[j].id_qcm_response;
                } else {
                    console.log("NOT is good");
                    console.log(vals[j]);
                }
            }
            console.log("1  &&&&&&&&&&&&&googds");
            console.log(googds);
            var responses = [];
            responses = current_reponse.split(";");
            console.log(responses);
            /**
             * test if good responses == users responses
             */
            if (googds.length != responses.length) {
                tesponseJs.boolean_response = false;
                tesponseJs.label_response = "No good";
                return tesponseJs; //false;
            }
            /**
             * test if there is a good response not found in the users responses
             * if so => no need to go further, we retrn false
             */
            var counter = 0;
            var counterReference = googds.length;
            console.log("2  &&&&&&&&&&&&&googds");
            console.log(googds);
            console.log(responses);
            for (k = 0; k < googds.length; k++) {  // id_qcm_response
                //console.log(googds);
                if (jQuery.inArray(googds[k], responses) != -1) {
                    counter++
                }
            }
            console.log(counter);
            if (counter == counterReference) {
                tesponseJs.boolean_response = true;
                tesponseJs.label_response = "Good";
                return tesponseJs; //true;
            } else {
                tesponseJs.boolean_response = false;
                tesponseJs.label_response = "Not Good";
                return tesponseJs; //false;
            }
            //return false; //"QCM_INCLU"; //googds; //.join(); //"QCM_INCLU";

            //question_types[6] = "CONDITIONNAL";
        } else if (question_types[response_type] == question_types[6]) {
            console.log("IS A CONDITIONNAL NODE");
            tesponseJs.boolean_response = false;
            tesponseJs.label_response = "IS A CONDITIONNAL NODE";
            return tesponseJs; //false; //"CONDITIONNAL";

        } else {
            tesponseJs.boolean_response = false;
            tesponseJs.label_response = "NO GOOD RESPONSE TYPE FOUND";
            return tesponseJs; //false; //"NO GOOD RESPONSE TYPE FOUND";
        }
    }
    function getResponseLabel(
        id_response,
        id_user,
        type_response,
        user_response,
        good_response,
        result,
        rcm_txt,
        points) {

        user_response = (rcm_txt == "") ? user_response : rcm_txt;
        
        var user_log = $("#id_list_exam_users option[value='" + id_user + "']").text();
        var class_Result = "";
        var message = "";
        if (result.boolean_response) {
            class_Result = "resp_special_left_result_true";
            class_Resul_border = "response_label_content_x_true";
            message = "True";
            //addNewUserLine(id_user, user_log, points);
        } else {
            class_Result = "resp_special_left_result_false";
            class_Resul_border = "response_label_content_x_false";
            message = "False";
        }        
        console.log("--------------------CALCUL ------------------");
        if (type_response == '4' || type_response == '5') {            
            console.log("inQCM points: " + points + " User resonse: " + user_response);
            addNewUserLine(id_user, user_log, points);
        } else if (result.boolean_response) {
            console.log("No QCM points: " + points + " User resonse: " + user_response);
            addNewUserLine(id_user, user_log, points);
        } else {
            console.log("OUT ");
        }
        //csi -> record here the data for sj report
        //addNewUserLine(id_user, user_log, points);
        var matrice =

             //response
             "   <div  class='" + class_Resul_border + "' >  " +

                 "   <label class=''>   " +
                 "       <div class='resp_left'> User name </div> <div class='resp_right'> " + user_log + "</div>" +
                 "   </label>   " +
                 "   <label class='' >   " +
                 "       <div class='resp_left'> User id </div> <div class='resp_right'>" + id_user + "</div>" +
                 "   </label>   " +
                 "   <label class=''>   " +
                 "       <div class='resp_left'>Id response </div> <div class='resp_right'> " + id_response + "</div>" +
                 "   </label>   " +
                 "   <label class=''>   " +
                 "       <div class='resp_left'>Type response label </div> <div class='resp_right'> " + question_types[type_response] + "</div>" +
                 "   </label>   " +
                 "   <label class=''>   " +
                 "       <div class='resp_left'>Type response key </div> <div class='resp_right'> " + type_response + "</div>" +
                 "   </label>   " +
                 "   <label class=''>   " +
                 "      <div class='user_resp_left'> User response  </div> <div class='resp_right'>" + user_response + "</div>" +
                 "   </label>   " +
                 "   <label class=''>   " +
                 "      <div class='resp_special_left'> Good response(s) </div> <div class='resp_right'> " + good_response + "</div>" +
                 "   </label>   " +

                 "   <label class=''>   " +
                 "      <div class='resp_special_left_result_points'> Points </div> <div class='resp_right'> " + points + "</div>" +
                 "   </label>   " +

                 "   <label class=''>   " +
                 "      <div class='resp_special_left_result final_result'> Result </div> <div class='" + class_Result + "'> " + message + "</div>" +
                 "   </label>   " +

             "   </div>  ";

        return matrice;
    }
    function getGoodResponses(response_type, current_questionnaire) {

        //console.log("iiiiiiiiiiiiiiiiiii");
        console.log(current_questionnaire);
        //console.log(current_questionnaire).data;

            //question_types[0] = "IS A QUESTIONNAIRE NODE!";
        if (       question_types[response_type] == question_types[0]) {
            console.log("IS A QUESTIONNAIRE NODE");
            return "Error, it s a questionnaire";

            //question_types[1] = "TEXT";
        } else if (question_types[response_type] == question_types[1]) {
            console.log("IS A TEXT NODE");
            return current_questionnaire.data.GOOD_RESPONSE_TXT;

            //question_types[2] = "NUMBER";
        } else if (question_types[response_type] == question_types[2]) {
            console.log("IS A NUMBER NODE");
            return current_questionnaire.data.GOOD_RESPONSE_NUMBER;

            //question_types[3] = "DATE";
        } else if (question_types[response_type] == question_types[3]) {
            console.log("IS A DATE NODE");
            return current_questionnaire.data.GOOD_RESPONSE_DATE;

            //question_types[4] = "QCM_EXCLU";
        } else if (question_types[response_type] == question_types[4]) {
            console.log("IS A QCM_EXCLU NODE");
            var vals = current_questionnaire.data.EXCLUSIVE_TYPE_VALUES;
            //coclo ici tu peux capturer le label x.text
            //console.log("----------------->vals");
            //console.log(vals);

            var googds = [];
            for (j = 0; j < vals.length; j++) {
                if (vals[j].isGood) {
                    console.log("is good");
                    console.log(vals[j]);
                    googds[j] =
                        " <i>[ " +

                        " id:" + vals[j].id_qcm_response + ", Points: " + vals[j].points +
                        " ]</i> </br>" +
                        vals[j].text;
                } else {
                    console.log("NOT is good");
                    console.log(vals[j]);
                }
            }
            console.log("----------->googds");
            console.log(googds);
            var concatResp = "";
            for (k = 0; k < googds.length; k++) {
                if (googds[k] == ',') {
                    continue;
                }
                if (googds[k] == undefined) {
                    continue;
                }
                concatResp += " " + googds[k];
            }
            //console.log(googds.join());
            return concatResp; //googds; //.join(); //"QCM_EXCLU";

            //question_types[5] = "QCM_INCLU";
        } else if (question_types[response_type] == question_types[5]) {// <---------------
            console.log("IS A QCM_INCLU NODE<--------------------");
            var vals = current_questionnaire.data.INCLUSIVE_TYPE_VALUES
            var googds = [];
            var goodsInString = "";
            var wrongInString = "";
            for (j = 0; j < vals.length; j++) {
                console.log("vals");
                console.log(vals);
                if (vals[j].isGood) {
                    console.log("is good");
                    console.log(vals[j]);
                    console.log(vals[j].isGood);
                    console.log(vals[j].text);
                    console.log(vals[j].id_qcm_response);
                    googds[j] = vals[j]; //.id_qcm_response;

                    goodsInString += //" " + vals[j].text + " (" + vals[j].id_qcm_response + ") ";; //vals[j].id_qcm_response + " ";
                    " <i>[ " +
                    " id:" + vals[j].id_qcm_response + ", Points: " + vals[j].points +
                    " ] </i></br>" +
                    vals[j].text + "<br>";

                } else {
                    //console.log("NOT is good");
                    //console.log(vals[j]);
                    wrongInString += //" " + vals[j].text + " (" + vals[j].id_qcm_response + ") ";; //vals[j].id_qcm_response + " ";
                    " <i>[ " +
                    " id:" + vals[j].id_qcm_response + ", Points: " + vals[j].points +
                    " ] </i></br>" +
                    vals[j].text + "</br>";
                }
            }
            return "<b>System Good answers</b> </br>" + goodsInString + " <b>System Wrong answers</b> </br>" + wrongInString; //googds; //.join(); //"QCM_INCLU";

            //question_types[6] = "CONDITIONNAL";
        } else if (question_types[response_type] == question_types[6]) {
            console.log("IS A CONDITIONNAL NODE");
            return "CONDITIONNAL";

        } else {
            return "NO GOOD RESPONSE TYPE FOUND";
        }        
    }
    function getQuestionMatrice(response, current_questionnaire) {
        console.log("*************response******************");
        console.log(response);
        console.log("__________debug__________________");
        console.log(response);
        var text_question = response.questionnaire.jsTreeNodes.nodes[i].data.INTITULE; //text;
        var id_question = response.questionnaire.jsTreeNodes.nodes[i].id;
        var text_response = "";
        var counter = true;
        var counter2 = true;
        tmp = "";
        for (ii = 0; ii < users.length; ++ii)
        {
            
            console.log("__________in loop__1________________");
            //var resp_type = response.responses[id_question][users[ii]][0].resp_type;
            console.log(ii);
            console.log(users);
            console.log(users[ii]);
            console.log(response);
            if (response.responses[id_question] === undefined) {
                continue;
            }

            console.log(response.responses[id_question]);
            console.log(response.responses[id_question][users[ii]]);
            console.log(response.responses[id_question][users[ii]][0]);
            console.log(response.responses[id_question][users[ii]][0].resp_type);

            var resp_type = response.responses[id_question][users[ii]][0].resp_type;

            var field = question_types_to_response_fields[resp_type];
            var user_response = eval('response.responses[id_question][users[ii]][0].' + field);
            var rcm_txt = eval('response.responses[id_question][users[ii]][0].' + "resp_text_2")
            var good_response = getGoodResponses(resp_type, current_questionnaire);
            console.log(6);
            console.log("good_response: " + good_response);
            var result = "";
            //console.log("0");
            var responseJsObject = getCompareResponseResultLabel(resp_type, current_questionnaire, user_response);
            console.log(7);
            console.log("__________in loop_ test label!!!!!!!!!_________________");
            console.log(responseJsObject);
            if (responseJsObject.boolean_response) {
                result = "Response: " + responseJsObject .label_response + " -> True";
            } else {
                result = "Response: " + responseJsObject.label_response + " -> False";
            }
            //return;
            var tmp = "";
            if (counter) {
                images = response.responses[id_question][users[ii]][0].IMAGES_RESPONSES;
                tmp = addImages(images);
                counter = false;
                //tmp = ii;
            }
            //return;
            text_response +=
                getResponseLabel(
                    response.responses[id_question][users[ii]][0].response_id,
                    users[ii],
                    response.responses[id_question][users[ii]][0].resp_type,
                    user_response,
                    good_response,
                    responseJsObject,
                    rcm_txt,
                    response.responses[id_question][users[ii]][0].points
                );
            //return;
        }
        if (tmp.length == 0) {
            tmp = "No images";
        }
        var matrice =
             "<div id='id_container_images_exam' class='alert alert-info' style='display: block;'>" +
                "<div class='image_container'> " +
                    tmp +
                "</div>" + 
             "</div>" +
             //question
             "   <label                               " +
             "       class='question_label_content'   " +
             "       id='question_label_id_content_" + id_question + "'>  " +
             "ID question: " + id_question + ". " + text_question +
             "    </label>                           " +

             //response
             "   <label                               " +
             "       class='response_label_content_user'   " +
             "       id='response_label_id_content_" + id_question + "'>  " +
             text_response                              +
             "   </label>                             ";

        return matrice;
    }

    //--------------------->--------------
    //function emptyImages() {
    //    $(".image_container").empty();
    //}
    function addImage(url) {
        html = "   <div id='1' class='image_loaded_exam' >          " +
               "         <img class='color_box_crochet' src='" + url + "' />           " +
               "   </div>  ";
        return html;
    }
    function addImages(images) {
        showTopImagesContainer(images);
        var imagesHtml = "";
        for (k = 0; k < images.length; ++k) {
            imagesHtml += addImage(images[k].file_url);
        }
        return imagesHtml;
    }
    function hideTopImagesContainer() {
        //$("#id_container_images_exam").hide(1000);
    }
    function showTopImagesContainer(images) {
        //if (images.length > 0) {
        //    hideTopImagesContainer();
        //    $("#id_container_images_exam").show(1500);
        //} else {
        //    hideTopImagesContainer();
        //}
    }    //------------------------<-------------------

    function getAuthorQuestionnaireExamUsers(questionnaire_id, id_author) {
        var dataToChallenge = {
            service: 'GET_LINKED_USERS',
            id_author: id_author,
            questionnaire_id: questionnaire_id,
            origin: origin
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_report/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log(response);
                setExamUsers(response.users);
            },
            error: function (response) {
                console.log("getAuthorQuestionnaireExamUsers KO!");
                return null;
            }
        });
    }
    function getAuthorQuestionnaires(id_author) {
        var dataToChallenge = {
            service: 'GET_ALL_QUESTIONNAIRES_FOR_ONE_AUTHOR',
            id_author: id_author,
            origin: origin
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_report/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log(response);
                setAuthorQuestionnaires(response);
            },
            error: function (response) {
                console.log("getExamUsersForOneAuthor KO!");
                return null;
            }
        });
    }

    function getAuthorUsers() {
        var dataToChallenge = {
            service: 'GET_AUTHOR_USERS',
            origin: origin
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_report/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log(response);
                setExUsers(response);

                //getLinkedUsers(); // id_list_author_users_linked
            },
            error: function (response) {
                console.log("getExamUsersForOneAuthor KO!");
                return null;
            }
        });
    }
});
/**
 * objects to build the top result before ed detailled version
 */
function proxy(){ //idUser, nameUser, points) {
    //addNewUserLine(1, "u1", 10);
    //addNewUserLine(2, "u2", 12);
    //addNewUserLine(3, "u3", 13);
    //addNewUserLine(4, "u4", 14);
    //addNewUserLine(5, "u5", 15);
}
function builReport() {
    console.log("&&&&&&&&&&&&&&&&&&&&&call cleanseReport START");
    cleanseReport();
    console.log("&&&&&&&&&&&&&&&&&&&&&call cleanseReport  STOP");
    //for (i = 0; i < objUserLines.length; i++) {
    //    $("#id_js_report").append("coucou");
    //    $("#id_js_report").append("coucou2");
    //}
    $.each(objUserLines, function (key, value) {
        //var percent = ((+value.TotPoints) / (+tot_amount)) * 100;
        //percent = Math.round(percent).toFixed(2);

        if (value == undefined) {
        }else{
            console.log(key + ": " + value);
            $("#id_js_report").
                append(
                    "<div class='js_report'>" +

                        " <div class='label_name_js_report1'>" + 
                        " User name " +
                            " <span class='js_report_value'>" + 
                               value.Name +
                            " </span>" +
                        " </div>" +

                        //" <div class='label_name_js_report'>" +
                        //" User id " +
                        //    " <span class='js_report_value'>" +
                        //    value.UserId +
                        //    " </span>" +
                        //" </div>"  +

                        " <div class='label_name_js_report2'>" +
                        " Points " +
                            " <span class='js_report_value'>" +
                              value.TotPoints + "/" + " " + tot_amount +
                            " </span>" +
                        " </div>" +

                        " <div class='label_name_js_report3'>" +
                        " Percentage " +
                            " <span class='js_report_value'>" +
                             // ((+value.TotPoints) / (+tot_amount)) * 100 +
                               Math.round(((+value.TotPoints) / (+tot_amount)) * 100).toFixed(2) + " %" + 
                              //tot_amount + 
                            " </span>" +
                        " </div>" +

                        //" <div class='label_name_js_report'>" +
                        //" Tot " +
                        //    " <span class='js_report_value'>" +
                        //      tot_amount +
                        //      //tot_amount + 
                        //    " </span>" +
                        //" </div>" +
                        
                    "</div>" 
                ); // UserId
        }
    });
    console.log("objUserLines.length");
    console.log(objUserLines);
    console.log(objUserLines.length);
    objUserLines = [];
}
function cleanseReport() {
    //objUserLines = [];
    $("#id_js_report").html("");
    //tot_amount = 0;
}
function addNewUserLine(idUser, nameUser, points) {
    //insert
    if (objUserLines[idUser] == undefined) {
        var objUserLine = {
            "UserId": "",
            "Name": "",
            "TotPoints": 0,
        }
        console.log(idUser + " is not defined");
        objUserLine.UserId = idUser;
        objUserLine.Name = nameUser;
        objUserLine.TotPoints = points;
        objUserLines[idUser] = objUserLine;
        //update
    } else {
        console.log(idUser + " is defined");
        objUserLine = objUserLines[idUser];
        objUserLine.TotPoints = +objUserLine.TotPoints + +points;
        objUserLines[idUser] = objUserLine;
    }
    //console.log(objUserLines);
}
function addQuestionnaire() { }
/****************************
 * AUTH --> Start
 **************************/
function isStillUsingGoodSession() {
    console.log('->isStillUsingGoodSession');
    var dataToChallenge = {
        service: 'ORIGIN_CHALLENGE',
        origin: origin
    }
    $.ajax({
        type: "POST",
        url: "/public/ajax_report/json/readquestionnaires", ///id_user/1",
        //contentType: "application/json",
        dataType: "json",
        async: false,
        data: dataToChallenge,
        success: function (response) {
            console.log("ORIGIN_CHALLENGE: OK");
            if (response['error'] == '1') {
                location.reload();
            }
        },
        error: function (response) {
            console.log("ORIGIN_CHALLENGE KO!");
        }
    });
}
/****************************
 * AUTH <-- Stop
 ***************************/