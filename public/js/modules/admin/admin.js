var displayStuffs = false;
var origin = "ADMIN";
var examUsersForOneAuthor = null;

var EXAM_USERS_CRUDS_UPDATE = "EXAM_USERS_CRUDS_UPDATE";
var EXAM_USERS_CRUDS_NEW = "EXAM_USERS_CRUDS_NEW";
var EXAM_USERS_CRUDS_DELETE = "EXAM_USERS_CRUDS_DELETE";

$(document).ready(function () {

    openColorBox();

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
            service: 'DISTROY_SESSION_AUTH_CHALLENGE'
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_admin/json/readquestionnaires", ///id_user/1",
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
            service: 'SESSION_AUTH_CHALLENGE'
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_admin/json/readquestionnaires", ///id_user/1",
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
        console.log("->authentificationTest");
        var dataToChallenge = {
            service: 'AUTH_CHALLENGE',
            log: $("#hidden_login_dialog_to").val(),
            pw: $("#hidden_password_dialog_to").val(),
            origine: origin
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_admin/json/readquestionnaires", ///id_user/1",
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
            url: "/public/ajax_admin/json/readquestionnaires", ///id_user/1",
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
    refreshAuthorUsersLists();
    /**
     * Refresh user list
     */
    $('#id_refresh_user_list').click(function (event) {
        event.preventDefault();
        refreshAuthorUsersLists();
    });
    function refreshAuthorUsersLists() { //total
        update_users_list_refresh();
    }
    function update_users_list_refresh() {
        getAuthorUsers();
    }
    function getAuthorUsers() {
        var dataToChallenge = {
            service: 'GET_AUTHOR_USERS'
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_admin/json/readquestionnaires", ///id_user/1",
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
    /**
     * if double clicked -> fill the user management fields accordingly
     */
    $('#id_list_author_users').dblclick(function () {
        if ($("#id_list_author_users :selected").length > 0) {
            $("#id_list_author_users :selected").map(function (i, el) {
                //console.log($(el).text() + " " + $(el).val());
                setUserValues($(el).val());
                return false;
            });
        }
    });
    /**
     * find a user selected and fill up the fields CRUD user
     * TODO.......
     */
    function setUserValues(id_user) {
        //console.log(" ======================> setUserValues");
        //return true;
        //console.log(examUsersForOneAuthor);
        for (i = 0; i < examUsersForOneAuthor.length; ++i) {
            if (examUsersForOneAuthor[i].user_id == id_user) {
                $("#id_user_name").val(examUsersForOneAuthor[i].user_log);
                $("#id_user_mail").val(examUsersForOneAuthor[i].user_mail);
                $("#id_questionnaire_pass").val(examUsersForOneAuthor[i].user_pw);
                $("#hidden_id_selected_user").val(examUsersForOneAuthor[i].user_id);
                return false;
            }
        }
    }
    /**
     * CRUD on user EXAM_USERS_CRUDS_UPDATE 
     */
    $('#id_btn_user_update').click(function (event) {
        cleanseErrorSuccessExtUsersCrudMessages();
        setTimeout(function () {
            event.preventDefault();
            console.log("id_btn_user_update");
            crudOnUsers(EXAM_USERS_CRUDS_UPDATE);
        }, 2000);
    });
    /**
     * CRUD on user EXAM_USERS_CRUDS_NEW 
     */
    $('#id_btn_user_new').click(function (event) {
        cleanseErrorSuccessExtUsersCrudMessages();
        setTimeout(function () {
            event.preventDefault();
            console.log("id_btn_user_new");
            crudOnUsers(EXAM_USERS_CRUDS_NEW);
        }, 2000);
    });
    /**
     * CRUD on user EXAM_USERS_CRUDS_DELETE 
     */
    $('#id_btn_user_delete').click(function (event) {
        cleanseErrorSuccessExtUsersCrudMessages();
        setTimeout(function () {
            event.preventDefault();
            console.log("id_btn_user_delete");
            crudOnUsers(EXAM_USERS_CRUDS_DELETE);
        }, 2000);
    });
    function cleanseErrorSuccessExtUsersCrudMessages() {
        $('#xxxxx').hide(1000);
        $('#xxxxx').text('Action started ...');
        $('#xxxxx').show(1000);
    }
    function setExUsers(setExUsers) {
        examUsersForOneAuthor = setExUsers;
        var list = $("#id_list_author_users");
        list.empty();
        for (i = 0; i < examUsersForOneAuthor.length; ++i) {
            list.append(
                new Option(
                   examUsersForOneAuthor[i].user_log,
                   examUsersForOneAuthor[i].user_id
                ));
        }
        //getLinkedUsers();
    }
    /**
     * ajax CRUD users
     */
    function crudOnUsers(user_crud_flag) {

        var dataToChallenge = {
            service: user_crud_flag,
            user_id: $('#hidden_id_selected_user').val(),
            user_login: $('#id_user_name').val(),
            user_mail: $('#id_user_mail').val(),
            questionnaire_pass: $('#id_questionnaire_pass').val()
        }

        $.ajax({
            type: "POST",
            url: "/public/ajax_admin/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,

            // todo
            // refresh all users
            // refresh selected users
            //.....................

            success: function (response) { // 111
                console.log("111");
                if (response.error.length > 0) {
                    console.log(response.error);
                    $('#xxxxx').hide();
                    $('#xxxxx').text("Error: " + response.error);
                    $('#xxxxx').show(1500);
                    $('#id_questionnaire_log_crud').empty();
                    $('#id_questionnaire_log_crud').text(
                        response.action + '\n' + response.error);
                } else {
                    console.log("No errors");
                    $('#xxxxx').hide();
                    $('#xxxxx').text("NO Error");
                    $('#xxxxx').show(1500);
                    $('#id_questionnaire_log_crud').empty();
                    $('#id_questionnaire_log_crud').text(
                        response.action + '\n' + 'No errors');
                }
                //if(response.er
                if (response.action == EXAM_USERS_CRUDS_NEW) {
                    console.log('ret ajax: EXAM_USERS_CRUDS_NEW');
                    //cleanse fields
                    $("#id_user_name").val('');
                    $("#id_user_mail").val('');
                    $("#id_questionnaire_pass").val('');
                    $("#hidden_id_selected_user").val('');
                    $('#id_questionnaire_log_crud').text(
                        $('#id_questionnaire_log_crud').text() + 
                        response.message
                     );
                } else
                    if (response.action == EXAM_USERS_CRUDS_UPDATE) {
                        console.log('ret ajax: EXAM_USERS_CRUDS_UPDATE');
                        //cleanse fields
                        $("#id_user_name").val('');
                        $("#id_user_mail").val('');
                        $("#id_questionnaire_pass").val('');
                        $("#hidden_id_selected_user").val('');
                    } else
                        if (response.action == EXAM_USERS_CRUDS_DELETE) {
                            console.log('ret ajax: EXAM_USERS_CRUDS_DELETE');
                            //cleanse fields
                            $("#id_user_name").val('');
                            $("#id_user_mail").val('');
                            $("#id_questionnaire_pass").val('');
                            $("#hidden_id_selected_user").val('');
                        }
                refreshExamUsersLists();
                setTimeout(function () {
                    closeErrorSuccessExtUsersCrudMessages();
                }, 4000);
            },
            error: function (response) {
                console.log("crudOnUsers KO!");
                $('#id_questionnaire_log_crud').empty();
                $('#id_questionnaire_log_crud').text("crudOnUsers KO!");
                return null;
            }
        });
    }
    function refreshExamUsersLists() { //total
        update_users_list_refresh();
        //getLinkedUsers();
    }
        function closeErrorSuccessExtUsersCrudMessages() {
            $('#xxxxx').hide(1000);
            $('#xxxxx').text('');
        }
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
     * COLOR BOX Start -->
     ***************************/

});

/****************************
 * AUTH --> Start
 **************************/
function isStillUsingGoodSession() {
    console.log('->isStillUsingGoodSession');
    var dataToChallenge = {
        service: 'ORIGIN_CHALLENGE',
        origine: origin
    }
    $.ajax({
        type: "POST",
        url: "/public/ajax_admin/json/readquestionnaires", ///id_user/1",
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