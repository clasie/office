/**
 * -> keep track of current element selected if any   ok
 * -> getCurrentDataElement()                         ok
 * -> delete a question 
 -> on en est au gui après delete d'une question function gui(data) {
 * -> save question...
 * -> indiquer la question selectionee...
 */

/**
 * response types
 */
var NEW_QUESTION_LABEL = 'New question label';
var TEXT = '1';  
var NUMBER = '2';
var DATE = '3';
var QCM_EXCLU = '4';
var QCM_INCLU = '5';
var CONDITIONNAL = '6';
var current_node_type = null;
var current_node_id = null;
var displayStuffs = false;
var origin = "QUESTIONNAIRE";
var LEFT_TO_RIGHT = 1;
var RIGHT_TO_LEFT = 2;
var examUsersForOneAuthor = null;
var EXAM_USERS_CRUDS_UPDATE = "EXAM_USERS_CRUDS_UPDATE";
var EXAM_USERS_CRUDS_NEW = "EXAM_USERS_CRUDS_NEW";
var EXAM_USERS_CRUDS_DELETE = "EXAM_USERS_CRUDS_DELETE";
var dataStored = []; //<---------------------------------------------------data stored
var UPDATE_TREE_VALUE = 1;
var DELETE_TREE_VALUE = 2;
var ADD_QUESTION = 3;

var idcounter = 0;
$(document).ready(function () {

    $('body').on('click', 'img.color_box_crochet', function () {
        openColorBoxImage($(this).attr('src'));
    });

    $('body').on('click', 'label.picture_to_delete', function () { //m6
        isStillUsingGoodSession();
        //isStillUsingGoodSession();
        console.log($(this).attr('id'));
        //delete the image via ajax
        deleteFile($(this).attr('id'));
        //delete the parent
        $(this).parent().hide(1600, function () { $(this).remove(); });
        return false;
    });
    // clickable
    $('body').on('click', '.clickable', function () { //m6

        $(".is_selected").each(function (index) {//remove selection
            $(this).removeClass('is_selected');
        });

        $(this).addClass("is_selected");//Add selection

        var ids_string = $(this).attr('id');
        $(this).addClass("is_selected");
        // is_selected

        // si questionnaire: qaire_181
        // si question: qaire_181_qion_121

        var dataFound = getCurrentDataElement(ids_string);

        //QUESTIONNAIRE
        //if (ids_string.indexOf("qaire_") != -1 && (ids_string.indexOf("_qion_") == -1)) {
        if (dataFound.data.TYPE == 'QUESTIONNAIRE') {
            //alert("a questionnaire");
            //var vals = ids_string.split("qaire_");
            //var idQuestionnaire = vals[1];
            //var data = dataStored[idQuestionnaire];
            //var questionnaire = data.questionnaire;

            gui(dataFound);
            buildStructure(dataFound, "NOT_BUILD_ALL");
            fillFields(dataFound);

        //QUESTION
        } else if (dataFound.data.TYPE == 'QUESTION') {

            //var vals = ids_string.split("_");
            //var idQuestionnaire = vals[1];
            //var vals = ids_string.split("_qion_");
            //var idQuestion = vals[1];
            //var data = dataStored[idQuestionnaire];
            //var question = data.questions[idQuestion];
            gui(dataFound);
            buildStructure(dataFound, "NOT_BUILD_ALL");
            fillFields(dataFound);
            //gui(dataFound);
            //buildStructure(dataFound, "NOT_BUILD_ALL");
            //fillFields(dataFound);

            //alert("questionnaire: " + idQuestionnaire + " id question: " + idQuestion);
        }
        //alert(a);
        //gui(
    });

    buildData();
    /**
     * update campaign status
     */
    $('#id_btn_update_campaign').click(function (event) {
        //alert("update campaign status");
        var old_campaign_id = $('#id_old_campaigns')
                    .find('option:selected').attr('value');
        var new_status = $('#id_old_campaign_status')
                    .find('option:selected').attr('value');
        if (new_status == "-1" || old_campaign_id == "-1") {
            alert("WARNING: Please select  a campaign AND a status");
            return false
        }
        updateCampaign(old_campaign_id, new_status);
    });
    /**
     * delete campaign
     */
    $('#id_btn_delete_campaign').click(function (event) {
        alert("delete campaign");
        //var old_campaign_id = $('#id_old_campaigns_delete')
        //            .find('option:selected').attr('value');
        //if (old_campaign_id == "-1") {
        //    alert("WARNING: Please select  a campaign");
        //    return false
        //}
        deleteCampaign(old_campaign_id);
    });

    function updateCampaign(old_campaign_id, newStatus) {
        var node = getCurrentDataElement(getCurrentConcatId()); //$('#jstree_demo').jstree(true).get_node(sel);
        var dataToChallenge = {
            service: 'UPDATE_CAMPAIGN_STATUS',
            old_campaign_id: old_campaign_id,
            newStatus: newStatus,
            questionnaire_id: node.id
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log("updateCampaign OK");
                console.log(response);
                //update the list + update status...
                //...
            },
            error: function (response) {
                console.log("updateCampaign KO");
                console.log(response);
            }
        });
    }
    function getCurrentConcatId() {
        var valElelcted = $(".is_selected");
        
        if (valElelcted != undefined) {
            var id = valElelcted.attr('id');
            return id; //valElelcted.id;
        }
        return "";
    }
    function deleteCampaign(old_campaign_id) {
        //var dataToChallenge = {
        //    service: 'DELETE_CAMPAIGN_STATUS',
        //    old_campaign_id: old_campaign_id
        //}
        //$.ajax({
        //    type: "POST",
        //    url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
        //    //contentType: "application/json",
        //    dataType: "json",
        //    async: false,
        //    data: dataToChallenge,
        //    success: function (response) {
        //        console.log("deleteCampaign OK");
        //        console.log(response);
        //    },
        //    error: function (response) {
        //        console.log("deleteCampaign KO");
        //        console.log(response);
        //    }
        //});
    }
    function getCurrentDataElement(ids_string) {
        if (ids_string == undefined) {
            return null;
        }
        //QUESTIONNAIRE
        if (ids_string.indexOf("qaire_") != -1 && (ids_string.indexOf("_qion_") == -1)) {
            var vals = ids_string.split("qaire_");
            var idQuestionnaire = vals[1];
            var data = dataStored[idQuestionnaire];
            var questionnaire = data.questionnaire;
            return questionnaire;

        //QUESTION
        } else if ((ids_string.indexOf("_qion_") != -1)) {
            var vals = ids_string.split("_");
            var idQuestionnaire = vals[1];
            var vals = ids_string.split("_qion_");
            var idQuestion = vals[1];
            var data = dataStored[idQuestionnaire];
            var question = data.questions[idQuestion];
            return question;
        }
    }
    function deleteCurrentDataElement(ids_string) {

        //QUESTIONNAIRE
        if (ids_string.indexOf("qaire_") != -1 && (ids_string.indexOf("_qion_") == -1)) {
            var vals = ids_string.split("qaire_");
            var idQuestionnaire = vals[1];
            dataStored[idQuestionnaire] = {};
            /*var data = dataStored[idQuestionnaire];
            var questionnaire = data.questionnaire;
            return questionnaire;*/

            //QUESTION
        } else if ((ids_string.indexOf("_qion_") != -1)) {
            var vals = ids_string.split("_");
            var idQuestionnaire = vals[1];
            var vals = ids_string.split("_qion_");
            var idQuestion = vals[1];
            dataStored[idQuestionnaire].questions[idQuestion] = {};
            /*var data = dataStored[idQuestionnaire];
            var question = data.questions[idQuestion];
            return question;*/
        }
    }
    function deleteCampaign(old_campaign_id) {
    }
    function deleteFile(file_id) {
        var dataToChallenge = {
            service: 'DELETE_FILE',
            file_id: file_id
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log("DELETE_FILE OK!");
            },
            error: function (response) {
                console.log("DELETE_FILE KO!");
            }
        });
    }
    /**
     * Upload exam's users
     */
    update_users_list();
    /**
     *  upload file
     */
    $('#temp_message').hide();
    $('#image_preview').hide();
    openColorBox();
    hideResponsesQuick();
    /**
     * build the jsTree
     */
    var to = false;
    $('#demo_q').keyup(function () {
        if (to) {
            clearTimeout(to);
        }
        to = setTimeout(function () {
            var v = $('#demo_q').val();
            $('#jstree_demo').jstree(true).search(v);
        }, 250);
    });
    $("#id_question_type_reponse").change(function () { //0071
        deleteQCMs();
        hideResponsesQuick();
        selectResponseType();
    });
    function buildCSITree(testData) {
        console.log(testData);
        buildStructure(testData, "BUILD_ALL");
    }
    function buildJsTree(testData) {
        //isStillUsingGoodSession();
        //alert(8);
        return;
        $('#jstree_demo')
            .jstree({
                "core": {
                    //"check_callback": function (op, node, node_parent) {
                    //    return op == 'move_node' ? node_parent.data.TYPE == "QUESTIONNAIRE" : true;
                    //},
                    //"animation": 500,
                    //"check_callback": true,
                    "themes": { "stripes": true },
                    'data': testData
                }//[
                //        { "id": "170", "parent": "#", "text": "New questionnaire label", "data": { "TYPE": "QUESTIONNAIRE"} },
                //        { "id": "171", "parent": "170", "text": "New question label", "data": { "TYPE": "QUESTION" } }
                //    ]
                //}//,
            //    "dnd": {
            //        is_draggable: function (x) {
            //            console.log("from is_draggable: " + x[0].data.TYPE);
            //            return x[0].data.TYPE == "QUESTION";
            //        },
            //        drop_finish: function (data) {
            //            console.log("eee");
            //        },
            //    },
            //    "types": {
            //        "#": { "max_children": -1, "max_depth": 4, "valid_children": ["root"] },
            //        "root": { "icon": "/static/3.0.2/assets/images/tree_icon.png", "valid_children": ["default"] },
            //        "default": { "valid_children": ["default", "file"] },
            //        "file": { "icon": "glyphicon glyphicon-file", "valid_children": [] }
            //    },
            //    "plugins": [/*"contextmenu",  "dnd", */ "dnd", "search", "state", "types", "wholerow"]
            //}).bind('move_node.jstree', function (e, data) {
            //    var dataToTransfer = {
            //        service: 'REORDER_DRAGGED_QUESTION',
            //        position: data.position,
            //        old_position: data.old_position,
            //        parent: data.parent,
            //        old_parent: data.old_parent,
            //        id_question: data.node.id
            //    }
            //    reorderDraggedQuestion(dataToTransfer);
            });
    }
    function reorderDraggedQuestion(dataToTransfer) {
        //isStillUsingGoodSession();
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            dataType: "json",
            async: false,
            data: dataToTransfer,
            success: function (response) {
                console.log("reorderDraggedQuestion OK");
            },
            error: function (response) {
                console.log("reorderDraggedQuestion KO!");
            }
        });
    }
    /**
     * fill in the jsTree
     */
    function ajax() {
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            contentType: "application/json",
            dataType: "json",
            async: false,
            success: function (response) {
                console.log("--------------------------->ajax 2");
                console.log(response);
                //buildJsTree(response.jsTreeNodes.nodes);// ---> changer ici

                buildCSITree(response.jsTreeNodes.nodes);

                //si rien de selectionné => on ne montre que Add questionnaire
                /*
                var ref = $('#jstree_demo').jstree(true);
                if (ref) {
                    var sel = ref.get_selected();
                    var sel = $('#jstree_demo').jstree(true).get_node(sel);
                    if (!sel) {
                        //location.reload();
                        $('#id_btn_create_question').hide(1500);
                        $('#id_btn_delete_node').hide(1500);
                        $("#tab_wrapper").hide(1500);
                    }
                }*/
            },
            error: function (response) {
                console.log("--------------------------->ajax 3");
                console.log(response);
            }
        });
    }

    /****************************
     * AUTH -->
     ***************************/

    /** 
     * the entry point of the display entry
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
    function refreshExamUsersLists() { //total
        update_users_list_refresh();
    }
    /**
     * Refresh user list
     */
    $('#id_refresh_user_list').click(function (event) {
        event.preventDefault();
        refreshExamUsersLists();
    });
    /**
     * if double clicked -> fill the user management fields accordingly
     */
    $('#id_list_exam_users').dblclick(function () {
        if ($("#id_list_exam_users :selected").length > 0) {
            $("#id_list_exam_users :selected").map(function (i, el) {
                //console.log($(el).text() + " " + $(el).val());
                setUserValues($(el).val());
                return false;
            });
        }
    });
    /**
     * if double clicked -> fill the user management fields accordingly
     */
    $('#id_list_exam_users_linked').dblclick(function () {
        if ($("#id_list_exam_users_linked :selected").length > 0) {
            $("#id_list_exam_users_linked :selected").map(function (i, el) {
                //console.log($(el).text() + " " + $(el).val());
                setUserValues($(el).val());
                return false;
            });
        }
    });
    /**
     * Get linked users 
     */
    function getLinkedUsers() {

        var ref = getCurrentDataElement(getCurrentConcatId()); //$('#jstree_demo').jstree(true);
        console.log("34");
        if (!ref) {
            return;
        }
        //var sel = ref.get_selected();
        console.log("34");
        var node = ref; //$('#jstree_demo').jstree(true).get_node(sel);
        console.log("34");
        var dataToChallenge = {
            service: 'GET_LINKED_USERS', //not implemented in the remote part ... to do
            questionnaire_id: node.id,
            flag: 'oups'
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log("---------> getLinkedUsers OK");
                console.log(response);
                //fill in the list
                var listLUsers = $("#id_list_exam_users_linked");
                listLUsers.empty();
                for (i = 0; i < response.users.length; ++i) {
                    console.log("---------> getLinkedUsers OK ------> in loop");
                    console.log(response.users[i]);
                    console.log("1.3 xxxxxxxxxxxxxxxxxxxxxxx");
                    listLUsers.append(
                        new Option(
                            "(id:" + response.users[i].user_id + ", Mock: " + response.users[i].mocker + ") " +
                                          response.users[i].user_log,
                                          response.users[i].user_id)
                        );

                    if ($("#id_list_exam_users option[value='" + response.users[i].user_id + "']").length > 0) {
                        console.log("1.3 - REMOVE " + "#id_list_exam_users option[value='" + response.users[i].user_id + "']");
                        //remove it
                        $("#id_list_exam_users option[value='" + response.users[i].user_id + "']").remove();
                    } else {
                        console.log("1.3 - NOT REMOVE " + "#id_list_exam_users option[value='" + response.users[i].user_id + "']");
                    }
                }
                //$("#id_list_exam_users_linked option").map(function (i, el) {
                //    someNumbers.push($(el).val());
                //});
            },
            error: function (response) {
                console.log("---------> getLinkedUsers KO");
            }
        });
    }
    /**
     * find a user selected and fill up the fields CRUD user
     * TODO.......
     */
    function setUserValues(id_user) {//csi1.1
        //console.log(" ======================> setUserValues");
        //return true;
        // id_chrono_chckbox
        var moker = ($("#id_mock_reader_chckbox").prop('checked') == true) ? "on" : "off";
        //console.log(examUsersForOneAuthor);
        for (i = 0; i < examUsersForOneAuthor.length; ++i) {
            if (examUsersForOneAuthor[i].user_id == id_user) {
                $("#id_user_name").val(examUsersForOneAuthor[i].user_log);
                $("#id_user_mail").val(examUsersForOneAuthor[i].user_mail);
                $("#id_questionnaire_pass").val(examUsersForOneAuthor[i].user_pw);
                $("#hidden_id_selected_user").val(examUsersForOneAuthor[i].user_id);
                if (examUsersForOneAuthor[i].mocker == 'on') {//checked
                    $("#id_mock_reader_chckbox").prop("checked", true);
                } else {
                    $("#id_mock_reader_chckbox").prop("checked", false);
                }
                return false;
            }
        }
    }
    /**
     * Link users to a ticket
     */
    $('#a_sync_users').click(function (event) {
        console.log('Refresh 1');
        $('#xxxxx2').html('Update launched...');

        setTimeout(function () {
            console.log($('#xxxxx2').text());
            console.log('Refresh 2');
            event.preventDefault();
            var someNumbers = [];
            var i = 0;
            $("#id_list_exam_users_linked option").map(function (i, el) {
                someNumbers.push($(el).val());
            });
            linkUsersToTicket(someNumbers);
        }, 1000);
    });
    /**
     * Generates a unique ticker from serveur
     */
    function linkUsersToTicket(users_to_link) {

        var node = getCurrentDataElement(getCurrentConcatId()); //$('#jstree_demo').jstree(true).get_node(sel);
        var dataToChallenge = {
            service: 'LINK_USERS_TO_TICKET',
            questionnaire_id: node.id,
            users_to_link: users_to_link,
            old_tiket: $("#id_questionnaire_unique_ticket").val()
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log("1");
                console.log(response);
                var secondsDelay = 2000;
                //case no users + no ticket existing
                if (response.result == null) {
                    $('#xxxxx2').text("");
                } else {
                    if (response.result.length > 0) {
                        console.log("2");
                        $('#xxxxx2').text('ERROR: ' + response.result);
                        secondsDelay = 5000;
                    } else {
                        console.log("3");
                        $('#xxxxx2').text('Update completed');
                        setUniqueTicket(response);
                    }
                }
                setTimeout(function () {
                    closeSynchroSuccessExtUsersCrudMessages();
                }, secondsDelay);
            },
            error: function (response) {
                console.log("getUniqueTicketFromServeur KO!");
                $('#xxxxx2').text('Update NOT completed');
                return null;
            }
        });
    }
    function closeSynchroSuccessExtUsersCrudMessages() {
        //$('#xxxxx2').hide(1000);
        $('#xxxxx2').text('');
    }
    function closeTicketSuccessExtUsersCrudMessages() {
        //$('#xxxxx3').hide(1000);
        $('#xxxxx3').text('');
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
    function closeErrorSuccessExtUsersCrudMessages() {
        $('#xxxxx').hide(1000);
        $('#xxxxx').text('');
    }
    function closeNewTicketSuccessExtUsersCrudMessages() {
        $('#xxxxx3').hide(1000);
        $('#xxxxx3').text('Action started ...');
        $('#xxxxx3').show(1000);
    }
    /**
     * ajax CRUD users  csi1.1
     */
    function crudOnUsers(user_crud_flag) {

        //var node = $('#jstree_demo').jstree(true).get_node(sel);
        var mocker = ($("#id_mock_reader_chckbox").prop('checked') == true) ? "on" : "off";

        var dataToChallenge = {
            service: user_crud_flag,
            user_id: $('#hidden_id_selected_user').val(),
            user_login: $('#id_user_name').val(),
            user_mail: $('#id_user_mail').val(),
            questionnaire_pass: $('#id_questionnaire_pass').val(),
            mocker: mocker
        }

        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
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
                } else {
                    console.log("No errors");
                    $('#xxxxx').hide();
                    $('#xxxxx').text("NO Error");
                    $('#xxxxx').show(1500);
                }
                //if(response.er
                if (response.action == EXAM_USERS_CRUDS_NEW) {
                    console.log('ret ajax: EXAM_USERS_CRUDS_NEW');
                    //cleanse fields
                    $("#id_user_name").val('');
                    $("#id_user_mail").val('');
                    $("#id_questionnaire_pass").val('');
                    $("#hidden_id_selected_user").val('');
                    $("#id_mock_reader_chckbox").prop("checked", false);
                } else
                    if (response.action == EXAM_USERS_CRUDS_UPDATE) {
                        console.log('ret ajax: EXAM_USERS_CRUDS_UPDATE');
                        //cleanse fields
                        $("#id_user_name").val('');
                        $("#id_user_mail").val('');
                        $("#id_questionnaire_pass").val('');
                        $("#hidden_id_selected_user").val('');
                        $("#id_mock_reader_chckbox").prop("checked", false);
                    } else
                        if (response.action == EXAM_USERS_CRUDS_DELETE) {
                            console.log('ret ajax: EXAM_USERS_CRUDS_DELETE');
                            //cleanse fields
                            $("#id_user_name").val('');
                            $("#id_user_mail").val('');
                            $("#id_questionnaire_pass").val('');
                            $("#hidden_id_selected_user").val('');
                            $("#id_mock_reader_chckbox").prop("checked", false);
                        }
                refreshExamUsersLists();
                setTimeout(function () {
                    closeErrorSuccessExtUsersCrudMessages();
                }, 4000);
            },
            error: function (response) {
                console.log("crudOnUsers KO!");
                return null;
            }
        });
    }

    /**
     * Generate a unique ticket
     */
    $('#id_btn_generate_unique_ticket').click(function (event) { //444
        closeNewTicketSuccessExtUsersCrudMessages();
        setTimeout(function () {
            event.preventDefault();
            getUniqueTicketFromServeur();
        }, 2000);
    });
    /**
     * Generates a unique ticker from serveur
     */
    function getUniqueTicketFromServeur() {

        var node = getCurrentDataElement(getCurrentConcatId()); //$('#jstree_demo').jstree(true).get_node(sel);
        var dataToChallenge = {
            service: 'GET_UNIQUE_TICKET_FOR_A_QUESTIONNAIRE',
            questionnaire_id: node.id,
            old_tiket: $("#id_questionnaire_unique_ticket").val(),
            //ticket_label: $("#id_questionnaire_ticket_label").val()
        }
        $.ajax({ // 999
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                //console.log(response.length);
                setUniqueTicket(response);

                if (false) { //response.error.length > 0) {
                    console.log(response.error);
                    $('#xxxxx3').hide();
                    $('#xxxxx3').text("Error: " + response.error);
                    $('#xxxxx3').show(1500);
                } else {
                    console.log("No errors");
                    $('#xxxxx3').hide();
                    $('#xxxxx3').text("NO Error");
                    $('#xxxxx3').show(1500);
                }
                setTimeout(function () {
                    closeTicketSuccessExtUsersCrudMessages();
                }, 2000);
            },
            error: function (response) {
                console.log("getUniqueTicketFromServeur KO!");
                return null;
            }
        });
    }
    /**
     * Put users to the right list
     */
    $('#id_push_users_to_right').click(function (event) {
        //alert(8);
        event.preventDefault();
        transfer_users(LEFT_TO_RIGHT);
        //put users to the left list
    });
    /**
     * Put users to the left list
     */
    $('#id_push_users_to_left').click(function (event) {
        //alert(9);
        event.preventDefault(RIGHT_TO_LEFT);
        transfer_users(RIGHT_TO_LEFT);
        //put users to right list...
    });
    function setUniqueTicket(response) {
        console.log("in: 1");
        console.log(response);
        console.log("in: " + response.ticket);
        console.log(response.result);
        console.log(response.result.ticket);
        $("#id_questionnaire_unique_ticket").val(response.result.ticket);
    }
    function transfer_users(flag) {
        /**
         * transfer selected users LEFT to RIGHT
         */
        if (flag == LEFT_TO_RIGHT) {
            var list = $("#id_list_exam_users_linked");
            $("#id_list_exam_users :selected").map(function (i, el) {
                //test if exists already in the target
                if ($("#id_list_exam_users_linked option[value='" + $(el).val() + "']").length == 0) {
                    list.append(new Option($(el).text(), $(el).val()));
                    /**
                     * remove from origine
                     */
                    $("#id_list_exam_users option[value='" + $(el).val() + "']").remove();
                    //... todo UPDATE AJAX
                }
            });
            /**
             * transfer selected users RIGHT to LEFT
             */
        } else {
            var list = $("#id_list_exam_users");
            $("#id_list_exam_users_linked :selected").map(function (i, el) {
                //test if exists already in the target
                if ($("#id_list_exam_users option[value='" + $(el).val() + "']").length == 0) {
                    list.append(new Option($(el).text(), $(el).val()));
                    /**
                     * remove from origine
                     */
                    $("#id_list_exam_users_linked option[value='" + $(el).val() + "']").remove();
                    //... todo UPDATE AJAX
                }
            });
        }
    }
    function update_users_list() {
        //console.log('in update_users_list()');
        //1- ajax: get all users for this Author ONCE
        getExamUsersForOneAuthor();
        //console.log(examUsersForOneAuthor);
        //2- get users selected for this questionnaire-ticket
        update_users_list_refresh();
        //3- avoid duplicates left/right
    }
    function update_users_list_refresh() {
        //var output = [];
        getExamUsersForOneAuthor();
        var list = $("#id_list_exam_users");
        list.empty();
        for (i = 0; i < examUsersForOneAuthor.length; ++i) {
            console.log("1.0 xxxxxxxxxxxxxxx");
            console.log(examUsersForOneAuthor[i]);
            list.append(

                new Option(
                    "(id:" + examUsersForOneAuthor[i].user_id + ", Mock: " + examUsersForOneAuthor[i].mocker + ") " +
                                 examUsersForOneAuthor[i].user_log,
                                 examUsersForOneAuthor[i].user_id)
                );

            //new Option(
            //   "1.0 " + examUsersForOneAuthor[i].user_log,
            //   examUsersForOneAuthor[i].user_id
            //));
            if ($("#id_list_exam_users_linked option[value='" + examUsersForOneAuthor[i].user_id + "']").length > 0) {
                //console.log("1.3 - REMOVE " + "#id_list_exam_users option[value='" + examUsersForOneAuthor[i].user_id + "']");
                //remove it
                $("#id_list_exam_users option[value='" + examUsersForOneAuthor[i].user_id + "']").remove();
            } else {
                //console.log("1.3 - NOT REMOVE " + "#id_list_exam_users_linked option[value='" + examUsersForOneAuthor[i].user_id + "']");
            }
        }
        console.log($('#id_list_exam_users')); //.html(output.join(''));
    }
    function getExamUsersForOneAuthor() {
        var element = getCurrentDataElement(getCurrentConcatId()); //$('#jstree_demo').jstree(true);
        var id_node = "-1";
        if (element) {
            //var sel = ref.get_selected();
            //var node = $('#jstree_demo').jstree(true).get_node(sel);
            id_node = element.id;
        }
        var dataToChallenge = {
            service: 'GET_EXAM_USERS_FOR_SESSION_AUTHOR',
            id_questionnaire: id_node
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToChallenge,
            success: function (response) {
                console.log("--------------------> GET_EXAM_USERS_FOR_SESSION_AUTHOR");
                console.log(response);
                setExUsers(response);

                //getLinkedUsers();
            },
            error: function (response) {
                console.log("getExamUsersForOneAuthor KO!");
                return null;
            }
        });
    }
    function setExUsers(setExUsers) {
        //alert(8);
        examUsersForOneAuthor = setExUsers;
        var list = $("#id_list_exam_users");
        list.empty();
        for (i = 0; i < examUsersForOneAuthor.length; ++i) {
            console.log("1.1");
            list.append(
                new Option(
                   "1.1 (id:" + examUsersForOneAuthor[i].user_id + ") "
                   + examUsersForOneAuthor[i].user_log,
                     examUsersForOneAuthor[i].user_id
                ));
        }
        getLinkedUsers();
    }
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
            $('#logged_user_data').html("");
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
        //show unlock
        $('a#id_log_unlog').text('Unlog');
        //-//$("#tab_wrapper").show(1000);
        ajax();
    } else {
        //show Log in
        $('a#id_log_unlog').text('Log in');
        //clease session
        reset_session();
        //lauches dialog
        $("#dialog").dialog('open');//->will update the hidden field accordingly
    }
    //input hidden has been changed
    $("#hidden_password_dialog_to").bind("change", function () {
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
                    console.log(response);
                    displayStuffs = true;
                    $('#logged_user_data').html(response.id_user);
                    //reload page
                    //location.reload();
                } else {
                    console.log("KO setSessionRunningAuthentificationTest");
                    displayStuffs = false;
                    $('#logged_user_data').html("");
                }
            },
            error: function (response) {
                console.log("SESSION AUTH KO!");
            }
        });
    }
    function xxxisStillUsingGoodSession() {
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
    function authentificationTest() {
        var dataToChallenge = {
            service: 'AUTH_CHALLENGE',
            log: $("#hidden_login_dialog_to").val(),
            pw: $("#hidden_password_dialog_to").val(),
            origine: 'QUESTIONNAIRE'
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
                    //-//$("#tab_wrapper").show(1000);
                    //$(".buttons_tree").show(1000);
                    $(".buttons_tree").show(1000);
                    ajax();
                    $('#logged_user_data').html(response.id);
                } else {
                    displayStuffs = false;
                    $("#jstree_demo").hide();
                    $('a#id_log_unlog').text('Log in');
                    $("#dialog").dialog('close');
                    $("#dialog").dialog('open');
                    $("#xxx").html("Wrong creds!");
                    //$(".buttons_tree").hide(1000);
                    $('#logged_user_data').html("");
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
                $(".buttons_tree").hide(1000);
            }
        });
    }
    function emptyTheTree() {
        $("#jstree_demo").hide();
        //$(".buttons_tree").hide(1000);
        hideAllQuestionsQuestionnaires();
    }
    /****************************
     * <-- AUTH
     **************************/
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
    function closeColorBox() {//return;
        setTimeout('$.colorbox.close()', 1500);
    }
    //openColorBox();
    $("#tab_wrapper").hide();
    $('ul.tabs').each(function () {
        // For each set of tabs, we want to keep track of
        // which tab is active and it's associated content
        var $active, $content, $links = $(this).find('a');

        // If the location.hash matches one of the links, use that as the active tab.
        // If no match is found, use the first link as the initial active tab.
        $active = $($links.filter('[href="' + location.hash + '"]')[0] || $links[0]);
        $active.addClass('active');

        $content = $($active[0].hash);

        // Hide the remaining content
        $links.not($active).each(function () {
            $(this.hash).hide();
        });

        // Bind the click event handler
        $(this).on('click', 'a', function (e) {
            isStillUsingGoodSession();
            // Make the old tab inactive.
            $active.removeClass('active');
            $content.hide(1000);

            // Update the variables with the new link and content
            $active = $(this);
            $content = $(this.hash);

            // Make the tab active.
            $active.addClass('active');
            $content.show(1000);

            // Prevent the anchor's default click action
            e.preventDefault();
        });
    });

    /**
     * qcm delete_btn_class
     */
    $(document).on("click", '.delete_btn_class', function () {
        isStillUsingGoodSession();
        //isStillUsingGoodSession();
        $(this).parent().parent().remove();
    });
    //$(".delete_btn_class").click(function () {
    //    console.log('delete_btn_class');
    //});    
    /**
     * qcm inclu click
     */
    $("#id_btn_add_qcm_incl").click(function () {
        isStillUsingGoodSession();
        //isStillUsingGoodSession();
        console.log('id_btn_add_qcm_incl');
        createQCMInclu();
    });

    /**
     * qcm excl click
     */
    $("#id_btn_add_qcm_excl").click(function () {
        isStillUsingGoodSession();
        $("#id_btn_save_question").removeAttr("disabled");
        console.log('id_btn_add_qcm_excl');
        createQCMExclu();
    });

    /** 
     * QUESTIONNAIRE SAVE CHANGES locally
     */
    $("#id_btn_archive_questionnaire").click(function () {
        var node = getCurrentDataElement(getCurrentConcatId()); //$('#jstree_demo').jstree(true).get_node(sel);
        var dataToSave = {
            service: 'ARCHIVE_QUESTIONNAIRE',
            id_questionnaire: node.id
        }

        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToSave,
            success: function (response) {
                $('#xxxxx4').show(1000);
                console.log("QUESTIONNAIRE ARCHIVE CHANGES remotely: OK");
                $('#xxxxx4').html('<b>Archive terminated</b> ' + response.message);
            },
            error: function (response) {
                console.log("QUESTIONNAIRE ARCHIVE CHANGES remotely: KO!");
            }
        });
    });
    /** 
     * QUESTIONNAIRE SAVE CHANGES locally
     */
    $("#id_btn_save_questionnaire").click(function () {
        //return;
        //alert("in save questionnaire");
        console.log('y');
        $('#xxxxx4').hide();
        $('#xxxxx4').html('Save launched...');
        $('#xxxxx4').show(1000);
        $("#id_btn_save_questionnaire").attr('disabled', 'disabled');

        setTimeout(function ()
        {

            isStillUsingGoodSession();

            //var ref = $('#jstree_demo').jstree(true);
            //sel = ref.get_selected();
            var data = getCurrentDataElement(getCurrentConcatId());
            var node = data;
            //if (sel.length > 0) {
                //$('#tree').jstree(true).get_node(sel).data.obj.asdf = "some other value"
                //var node = $('#jstree_demo').jstree(true).get_node(sel); //.data.obj.asdf = "some other value"
                node.text = $("#id_questionnaire_name").val(); //datas.text);
                //$("#jstree_demo").jstree('set_text', node, node.text); //update the tree display
                node.data.ETAT = $("#id_questionnaire_etat").val(); //datas.data.ETAT);
                node.data.SAUVEGARDE = $("#id_questionnaire_sauvegarde").val(); //datas.data.SAUVEGARDE);
                node.data.EXAMEN = $("#id_questionnaire_examen").val(); //datas.data.EXAMEN);
                node.data.DATE_START = $("#datepickerstart").val(); //datas.data.DATE_START);
                node.data.DATE_STOP = $("#datepickerstop").val(); //datas.data.DATE_STOP); 
                node.data.COMMENT = $("#id_questionnaire_comment").val();
                node.data.isChrono = ($("#id_chrono_chckbox").prop('checked') == true) ? "on" : "off";
                node.data.seconds = $("#id_chrono_value").val();//datas.data.DATE_STOP);             
                //auto correction
                if ($("#auto_correction").prop('checked')) {
                    //datas.data.AUTO_CORRECTION == 'CHECKED'
                    node.data.AUTO_CORRECTION = '1';
                } else {
                    node.data.AUTO_CORRECTION = '0';
                }
            //}
            //->//gui();

            /**
             * QUESTIONNAIRE SAVE CHANGES remotely
             */
            //autocorrection stuff
            var autocorrection = 0;
            if (node.data.AUTO_CORRECTION == '1') {
                autocorrection = 1;
            } else {
                autocorrection = 0;
            }

            var dataToSave = {
                service: 'UPDATE_QUESTIONNAIRE_NODE',
                text: node.text,
                etat: node.data.ETAT,
                sauvegarde: node.data.SAUVEGARDE,
                examen: node.data.EXAMEN,
                date_start: node.data.DATE_START,
                date_stop: node.data.DATE_STOP,
                comment: node.data.COMMENT,
                id_questionnaire: node.id,
                autocorrection: autocorrection,
                is_chrono: node.data.isChrono,
                seconds: node.data.seconds
            }

            $.ajax({
                type: "POST",
                url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
                //contentType: "application/json",
                dataType: "json",
                async: false,
                data: dataToSave,
                success: function (response) {
                    console.log("QUESTIONNAIRE SAVE CHANGES remotely: OK");
                    $('#xxxxx4').html('Save terminated');
                },
                error: function (response) {
                    console.log("QUESTIONNAIRE SAVE CHANGES remotely: KO!");
                }
            });

            updateTreeLabel("UPDATE_QUESTIONNAIRE", "");

            //$(document).on('dnd_stop.vakata', function (e, data) {
                //console.log("tutu");
            //});

            
            $('#xxxxx4').hide(50);
            $('#xxxxx4').html('Save terminated.');
            $('#xxxxx4').show(1000);

            setTimeout(function () {
                $("#id_btn_save_questionnaire").removeAttr('disabled');
                $('#xxxxx4').hide(1000);
            }, 2000);
            

        }, 1000);
    });
    /**
     * QUESTION SAVE CHANGES locally csi
     */
    $("#id_btn_save_question").click(function () {
        //csihere1
        var data = getCurrentDataElement(getCurrentConcatId());
        //alert(getCurrentConcatId());
        console.log(data);
        isStillUsingGoodSession();
        //$("#id_btn_save_question").text("Sending..."); //('title', 'your new title');;//.hide(); //toggle(); //attr('disabled', 'disabled');
        //$("#id_btn_save_question").button("refresh");
        //console.log('disabled IN');
        //var ref = $('#jstree_demo').jstree(true);
        //sel = ref.get_selected();
        var node = data; //null;
        if (true){ //node !=  null){ //sel.length > 0) {
            //node = $('#jstree_demo').jstree(true).get_node(sel); //.data.obj.asdf = "some other value"
            node.text = $("#id_question_name").val(); //datas.text);
            //mettre titre update dans l arbre
            //$("#jstree_demo").jstree('set_text', node, node.text); //update the tree display            
            node.data.INTITULE = $(".qc_input_question .jqte_editor").html(); //datas.data.ETAT);
            //node.data.GOOD_RESPONSE_TXT = $(".qc_good_response .jqte_editor").html();
            node.data.TYPE_REPONSE = $("#id_question_type_reponse").val(); //datas.data.SAUVEGARDE);  

            //var nodeParent = ref.get_node(node.parent);
            //console.log(nodeParent.data.ETAT);

            //catch the questionnaire
            var idsConcat = getCurrentConcatId().split('_')[1];
            var idsConcat = "qaire_" + idsConcat;
            var nodeParent = getCurrentDataElement(idsConcat);
            console.log("dataQaire");
            console.log(nodeParent);
            if (nodeParent.data.ETAT == "2") {
                alert("Impossible de modifier une question lorsque le questionnaire est dans un status 'Actif'");
                return;
            }

            /**
             * test the response type
             */
            var selectedVal = $("#id_question_type_reponse").val();

            console.log('1- POINTS');
            console.log(node.data.points);

            switch (selectedVal) {
                case TEXT:
                    //$(".good_answer_text_wrapper").show(1000);
                    node.data.GOOD_RESPONSE_TXT = $(".qc_good_response .jqte_editor").html();
                    node.data.points = $("#weight_good_answer_text").val();
                    console.log('TEXT from save');
                    break;
                case NUMBER:
                    //$(".good_answer_number_wrapper").show(1000);
                    // number_good_answer
                    node.data.GOOD_RESPONSE_NUMBER = $("#number_good_answer").val();
                    console.log('NUMBER from save: ' + node.data.GOOD_RESPONSE_NUMBER + " " + $("#number_good_answer").html());
                    node.data.points = $("#weight_good_answer_number").val();
                    break;
                case DATE:
                    //$(".good_answer_date_wrapper").show(1000);
                    node.data.GOOD_RESPONSE_DATE = $("#date_picker_good_answer").val();
                    node.data.points = $("#weight_good_answer_date").val();
                    console.log('DATE from save');
                    break;
                case QCM_INCLU:
                    runThrougAlllQCMIncl(".qcm_incl ul li", node);
                    console.log('QCM_INCLU from save ---in----> ');
                    break;
                case QCM_EXCLU:
                    runThrougAlllQCMExcl(".qcm_exc ul li", node);
                    console.log('QCM_EXCLU from save ---ex----> ');
                    break;
                case CONDITIONNAL:
                    //$(".good_answer_conditionnal_wrapper").show(1000);
                    console.log('CONDITIONNAL from save');
                    break;
                default:
                    console.log('UNKNOWN response type selected!  from save');
            }

            console.log('disabled OUT');
            console.log('2- POINTS');
            console.log(node.data.points);
        }
        closeColorBox();
        //return;
        //console.log('&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&------&&&&&&&&&&&&&&&');
        //console.log(
        //    ref.get_node(node.parent)
        //    //node.parent.data
        //    );
        //var nodeParent = ref.get_node(node.parent);
        //console.log(nodeParent.data.ETAT);
        //return;
        //$("#id_btn_save_question").show(); //removeAttr("disabled");
        //$("#id_btn_save_question").text("Save");
        //gui();
        /**
         * QUESTION SAVE CHANGES remotely   //  007
         */
        var dataToSave = {
            service: 'UPDATE_QUESTION_NODE',
            text: node.text,
            intitule: node.data.INTITULE,
            good_response_txt: node.data.GOOD_RESPONSE_TXT,
            good_response_date: node.data.GOOD_RESPONSE_DATE,
            good_response_number: node.data.GOOD_RESPONSE_NUMBER,
            responsetype: node.data.TYPE_REPONSE,
            qcmExclu: node.data.EXCLUSIVE_TYPE_VALUES,
            qcmInclu: node.data.INCLUSIVE_TYPE_VALUES,
            id_question: node.id,
            points: node.data.points
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToSave,
            success: function (response) {
                console.log("QUESTION SAVE CHANGES remotely: OK");
            },
            error: function (response) {
                console.log("QUESTION SAVE CHANGES remotely: KO!");
            }
        });
        updateTreeLabel(UPDATE_TREE_VALUE,"");
    });

    /**
     * Create a new QUESTION node remotely csi ud
     */
    $("#id_btn_create_question").click(function () {
        //alert("in create question");
        isStillUsingGoodSession();
        var ref = $('#jstree_demo').jstree(true);
        //sel = ref.get_selected();
        //node = $('#jstree_demo').jstree(true).get_node(sel);
        var data = getCurrentDataElement(getCurrentConcatId());
        var dataToSave = {
            service: 'CREATE_QUESTION_NODE',
            text: NEW_QUESTION_LABEL, //'New question label', //node.text,
            intitule: 'The brand new question...',
            responsetype: '1', //node.data.TYPE_REPONSE,
            parent: data.id, //node.id,
            node_type: 'QUESTION' //node.data.TYPE
        }
        //var idnewquestion = "";
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToSave,
            success: function (response) {

                console.log("RESPONSE");
                console.log(response);

                if (response.id == "-1") {
                    alert("This questionnaire cannot be modified, the Exam is 'activated'");
                    return;
                }
                //ici construire dans l objetc la question

                //get id questionnaire
                var data = getCurrentDataElement(getCurrentConcatId());
                var idquestionnaire = data.id;
                //var t = dataStored;
                //dataStored[idquestionnaire]
                window.newidquestion = response.id;
                var nquest = {
                    "id": response.id,
                    "type": "default",
                    "text": NEW_QUESTION_LABEL, //"New question label",
                    "data":
                        {
                            "TYPE": dataToSave.node_type,
                            "INTITULE": dataToSave.intitule,
                            "TYPE_REPONSE": dataToSave.responsetype,
                            "IMAGES": new Array()
                        }
                }
                dataStored[idquestionnaire].questions[response.id] = nquest;
                console.log("--------------> dataStored");
                console.log(dataStored);

                updateTreeLabel(
                    ADD_QUESTION,
                    window.newidquestion,
                    formatIdsConcatQuestionnaireNewQuestion(
                        idquestionnaire,
                        nquest.id)
                    );
            },
            error: function (response) {
                console.log("CREATE_QUESTION_NODE: KO!");
                console.log(response);
            }
        });
        //set the new node as selected -> gui()
        gui();
        //updateTreeLabel(ADD_QUESTION, window.newidquestion); //response.id);
        window.newidquestion = "";
    });

    /**
     * Create a new QUESTIONNAIRE node remotely csi ud
     */
    $("#id_btn_create_questionnaire").click(function () {
        //alert("in create questionnaire");
        isStillUsingGoodSession();
        console.log('id_btn_create_questionnaire');
        var ref = $('#jstree_demo').jstree(true);
        //sel = ref.get_selected();
        //node = $('#jstree_demo').jstree(true).get_node(sel);
        var dt = new Date();
        var currformdate = dt.getDate() + '/' + (dt.getMonth() + 1) + '/' + dt.getFullYear();
        var dataToSave = {
            service: 'CREATE_QUESTIONNAIRE_NODE',
            text: 'New questionnaire label', //node.text,
            etat: '1',
            sauvegarde: '1', //node.data.TYPE_REPONSE,
            examen: '1',
            date_start: currformdate,
            date_stop: currformdate,
            autocorrection: '1',
            comment: 'My new comment',
            parent: '', //node.id,
            node_type: 'QUESTIONNAIRE' //node.data.TYPE
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",  //007
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToSave,
            success: function (response) {
                //console.log(node.data);
                //var sel = $("#jstree_demo").jstree(true).create_node
                //("#",
                window.newidquestionnaire = response.id;
                var nquest =
                       {
                           "questionnaire": {
                               "id": response.id,
                               "text": dataToSave.text,
                               "type": "root",
                               "data":
                                       {
                                           "TYPE": dataToSave.node_type,
                                           "ETAT": dataToSave.etat,
                                           "SAUVEGARDE": dataToSave.sauvegarde,
                                           "EXAMEN": dataToSave.examen,
                                           "DATE_START": dataToSave.date_start,
                                           "DATE_STOP": dataToSave.date_stop,
                                           "AUTO_CORRECTION": dataToSave.autocorrection,
                                           "COMMENT": dataToSave.comment
                                       }
                           },//, "last", null, false
                           "questions": {
                           }
                       }
                 //);
                //console.log($("#jstree_demo").jstree().last_error ());
                dataStored[window.newidquestionnaire] = nquest;
                console.log('Create a new QUESTIONNAIRE: id: ' + response.id);
                //alert(9);

                updateTreeLabel(
                    "ADD_QUESTIONNAIRE",
                    window.newidquestionnaire,
                    formatIdsConcatQuestionnaireNewQuestion(
                        window.newidquestionnaire,
                        nquest.id
                    )
                 );
            },
            error: function (response) {
                console.log("CREATE_QUESTION_NODE: KO!");
                console.log(response);
            }
        });
        //set the new node as selected -> gui()
        gui();
    });   //    
    /**
     * Delete node selected
     */
    $("#id_btn_delete_node").click(function () {
        //alert("in delete node");
        isStillUsingGoodSession();
        //console.log('id_btn_delete_node');
        //var ref = $('#jstree_demo').jstree(true),
        //sel = ref.get_selected();

        var data = getCurrentDataElement(getCurrentConcatId());


        if (data == null) {
            return false;
        }

        //return;
        /**
         * 
         */


        /**
         * to do supprimer dans le html AUSSI!! <- ok fait
         */
        var currId = getCurrentConcatId();
        updateTreeLabel(DELETE_TREE_VALUE, currId)

        //node = $('#jstree_demo').jstree(true).get_node(ref.get_selected());
        //var idnode = node.id;
        //ref.delete_node(sel);
        var dataToSave = {
            service: 'DELETE_NODE',
            id_to_delete: data.id,
            node_type: data.data.TYPE,
            node_parent: data.parent,
        }
        $.ajax({
            type: "POST",
            url: "/public/ajax_questionnaire/json/readquestionnaires", ///id_user/1",
            //contentType: "application/json",
            dataType: "json",
            async: false,
            data: dataToSave,
            success: function (response) {
                console.log("OOOOOOOOOOK");
                console.log(response);
                if (response.id == "-1") {
                    alert('The questionnaire is in ACTIVE mode, impossible to delete or update values');
                    location.reload();
                }
            },
            error: function (response) {
                //alert('Impossible 2 ...');
                console.log("DELETE_NODE: KO!");
                console.log(response);
            }
        });
        deleteCurrentDataElement(currId);
        gui();
        //updateTreeLabel(DELETE_TREE_VALUE,getCurrentConcatId())
    });   //    //
    //console.log("jquery ok");
    function formatIdsConcatQuestionnaireNewQuestion(idQuestionnaire, idQuestion) {
        return "qaire_" + idQuestionnaire + "_qion_" + idQuestion;
    }
    /**
     * the very first place we build the questionnaire into the tree
     */
    //$('#jstree_demo')
    //        // listen for event
    //        .on('changed.jstree', function (e, data) {
    function updateTreeLabel(flag, value,concatnewqresp) {
        //alert(10);
        if (flag == UPDATE_TREE_VALUE) {

            // source: id_question_name
            var valueToTransfer = $("#id_question_name").val();
            //alert(valueToTransfer);
            // target: qaire_181_qion_189
            var concatid = getCurrentConcatId();
            $("#" + concatid).text(valueToTransfer); // = valueToTransfer;

        } else if (flag == DELETE_TREE_VALUE) {

            $("#" + value).remove();

        } else if (flag == ADD_QUESTION) {

            //$("#" + value).remove();
            //alert("Add Value In Layout, to do...");
            // qaire_181_qion_207
            newli = "<li id='" + concatnewqresp + "' class='ui-state-default tree_question clickable'>  " + NEW_QUESTION_LABEL + "  </li>";
            $("#jstree_demo_ul").append(newli);
            //add label of new question in edition form

        } else if (flag == "UPDATE_QUESTIONNAIRE") {
            var newText = $("#id_questionnaire_name").val();  // value
            var concatid = getCurrentConcatId();
            $("#" + concatid).text(newText); // = valueToTransfer;
        } else if (flag == "ADD_QUESTIONNAIRE") {//qaire_231_qion_231 == concatnewqresp
            //var newText = $("#id_questionnaire_name").val();
            //var concatid = getCurrentConcatId();
            //       <li id="qaire_181"           class="ui-state-default tree_title clickable">  New questionnaire label  </li>
            newli = "<li id='qaire_" + value + "' class='ui-state-default tree_title clickable'>  New questionnaire  </li>";
            $("#jstree_demo_ul").append(newli);
            //$("#" + concatid).append(newli); // = valueToTransfer;
        }
    }
    function buildStructureBackup(data) {  //20160817   nb: le data ici est celui de l arbre.....
        //return;
            console.log(data);
            var i, j, r = [];
            for (i = 0, j = data.length; i < j; i++) {
                //r.push(data.instance.get_node(data.selected[i]).data.TYPE);
                //continue;
                /**
                    * QUESTIONNAIRE
                    */
                if (data[i].data.TYPE == "QUESTIONNAIRE") {

                    openColorBox();

                    //show add question button
                    $("#id_btn_create_question").show(1000);

                    //-//$("#tab_wrapper").show(1000);
                        
                    //console.
                    //-//$('#id_questionnaire').click();

                    //-//setQuestionnaire(data[i]); //20160818   nb: le data ici est celui de l arbre.....
                        
                    //gui();
                    fillQuestionnaires(data[i]);

                    //-//fupdate_users_list_refresh();

                    //getLinkedUsers();


                    //-//ffillTheTicketValues(data[i]);
                    closeColorBox();

                    /**
                        * QUESTION
                        */
                } else if (data[i].data.TYPE == "QUESTION") {
                    //return;
                    openColorBox();
                    //hide add question button
                    $("#id_btn_create_question").hide(1000);

                    //-//$("#tab_wrapper").show(1000);
                    //-//$('#id_question').click();
                    setQuestion(data[i]);
                    //gui();
                    fillQuestionnaires(data[i]);

                    closeColorBox();
                }
                //break;
            }
            //$('#event_result').html('Selected: ' + r.join(', '));
            //console.log(r.join(', '));
    }
    function fillFields(data) {
        /**
         * QUESTIONNAIRE
         */
        if (data.data.TYPE == "QUESTIONNAIRE") {

            openColorBox();

            //show add question button
            $("#id_btn_create_question").show(1000);

            $("#tab_wrapper").show(1000);

            //console.
            $('#id_questionnaire').click();

            setQuestionnaire(data); //20160818   nb: le data ici est celui de l arbre.....

            //gui();
            //-//fillQuestionnaires(data[i]);

            //-//fupdate_users_list_refresh();

            //getLinkedUsers();


            //-//ffillTheTicketValues(data[i]);
            closeColorBox();

            /**
             * QUESTION
             */
        } else if (data.data.TYPE == "QUESTION") {
            //return;
            openColorBox();
            //hide add question button
            $("#id_btn_create_question").hide(1000);

            $("#tab_wrapper").show(1000);
            $('#id_question').click();
            setQuestion(data);
            //gui();
            //-//fillQuestionnaires(data[i]);

            closeColorBox();
        }
    }
    function preSortElements(dataRaw) {
        //return dataSorted;
    }
    function installDataInGui() {
            // dataStored
        //foreach (dataStored as value){}
        dataStored.forEach(function (entryQuestionnaire) {
            //console.log(entry);
            var questionnaire = entryQuestionnaire.questionnaire;
            var val = '<li id="qaire_' + questionnaire.id + '" class="ui-state-default tree_title clickable">  ' + questionnaire.text + '  </li>';
            $("#jstree_demo_ul").append(val);

            var questions = entryQuestionnaire.questions;
            console.log(questions.length);
            if (questions.length > 0) 
            {
                questions.forEach(function (entryQuestion) {
                    var question = entryQuestion; //.question;
                    var val = '  <li id="qaire_' + question.parent + '_qion_' + question.id + '" class="ui-state-default tree_question clickable">  ' + question.text + '  </li>';
                    $("#jstree_demo_ul").append(val);
                });
            }
        });        
    }
    function buildStructure(data,flagInit) {  //20160817   nb: le data ici est celui de l arbre.....
        //return;
        if (data == undefined) {
            return;
        }
        console.log(data);
        var i, j, r = [];
        for (i = 0, j = data.length; i < j; i++) {
            //r.push(data.instance.get_node(data.selected[i]).data.TYPE);
            //continue;
            /**
                * QUESTIONNAIRE
                */
            if (data[i].data.TYPE == "QUESTIONNAIRE") {

                openColorBox();

                //show add question button
                $("#id_btn_create_question").show(1000);

                //-//$("#tab_wrapper").show(1000);

                //console.
                //-//$('#id_questionnaire').click();

                //-//setQuestionnaire(data[i]); //20160818   nb: le data ici est celui de l arbre.....

                //gui();
                fillQuestionnaires(data[i]);

                //-//fupdate_users_list_refresh();

                //getLinkedUsers();


                //-//ffillTheTicketValues(data[i]);
                closeColorBox();

                /**
                    * QUESTION
                    */
            } else if (data[i].data.TYPE == "QUESTION") {
                //return;
                openColorBox();
                //hide add question button
                $("#id_btn_create_question").hide(1000);

                //-//$("#tab_wrapper").show(1000);
                //-//$('#id_question').click();
                setQuestion(data[i]);
                //gui();
                fillQuestionnaires(data[i]);

                closeColorBox();
            }
            //break;
        }
        //$('#event_result').html('Selected: ' + r.join(', '));
        //console.log(r.join(', '));
        if (flagInit == "BUILD_ALL") {
            installDataInGui();
        }
    }
    //$( "#datepicker" ).datepicker();
    $("#datepickerstart").datepicker({
        dateFormat: 'dd/mm/yy'
    });
    $("#datepickerstop").datepicker({
        dateFormat: 'dd/mm/yy'
    });
    $(".editor").jqte();
    $(".editor_good_answer").jqte();
    /** 
     * date good response 
     */
    $("#date_picker_good_answer").datepicker({
        dateFormat: 'dd/mm/yy'
    });
    /**
     * fill the questionnaire form
     */
    function setQuestionnaire(datas) {
        console.log('setQuestionnaire ----> ');
        console.log(datas);
        console.log(datas.data);
         //console.log(datas.id);
        emptyImages();
        addImages(datas.data.IMAGES);
        setGlobalParams(datas);
        setCampaignLinkedToThisQuestionnaire(datas.data.campaigns);
        $("#id_questionnaire_name").val(datas.text);
        $("#id_questionnaire_etat").val(datas.data.ETAT);
        $("#id_questionnaire_sauvegarde").val(datas.data.SAUVEGARDE);
        $("#id_questionnaire_examen").val(datas.data.EXAMEN);
        $("#datepickerstart").val(datas.data.DATE_START);
        $("#datepickerstop").val(datas.data.DATE_STOP);
        $("#id_questionnaire_label").html(" (id:" + datas.id + ") ");
        //auto correction
        if (datas.data.AUTO_CORRECTION == '1') {
            $("#auto_correction").prop('checked', true);
        } else {
            $("#auto_correction").prop('checked', false);
        }
        $("#id_questionnaire_comment").val(datas.data.COMMENT);
        if (datas.data.isChrono == "on") {
            $("#id_chrono_chckbox").prop('checked', true); //.val(datas.data.isChrono); //$(".myCheckBox").checked(true);
        } else {
            $("#id_chrono_chckbox").prop('checked', false);
        }
        $("#id_chrono_value").val(datas.data.seconds);
    }
    function fillTheTicketValues(datas) {
        console.log('|||||||||||||||||>>>>what the hell');
        console.log(datas.data);
        if (datas.data.ticket != null) {
            $('#id_questionnaire_unique_ticket').val(datas.data.ticket.value);
            //var texte = $('#id_btn_generate_unique_ticket').text();
            //$('#id_btn_generate_unique_ticket').text("(id:" + datas.data.ticket.id + ") " + texte);
            $('#id_unique_tiket_display').html("(id:" + datas.data.ticket.id + ") ");
        }
    }
    function setCampaignLinkedToThisQuestionnaire(campaigns) {
        console.log("setCampaignLinkedToThisQuestionnaire ---a--------> ");
        console.log(campaigns);
        ////console.log(images);
        if (campaigns == null) {
            console.log("setCampaignLinkedToThisQuestionnaire ---b--->");
            $('#id_old_campaigns').append($('<option>', {
                value: "-1",
                text: "No campaign found"
            }));
            $('#id_old_campaigns_delete').append($('<option>', {
                value: "-1",
                text: "No campaign found"
            }));
            return;
        }

        ////select a campaign
        //$('#id_old_campaigns').append($('<option>', {
        //    value: "-1",
        //    text: "Select a campaign"
        //}));
        //$('#id_old_campaigns_delete').append($('<option>', {
        //    value: "-1",
        //    text: "Select a campaign"
        //}));

        //id_old_campaigns
        for (i = 0; i < campaigns.length; ++i) {
            console.log("setCampaignLinkedToThisQuestionnaire ---c--------> ");
            $('#id_old_campaigns').append($('<option>', {
                value: campaigns[i].id,
                text: (campaigns[i].title + "/" + campaigns[i].status)
            }));
            $('#id_old_campaigns_delete').append($('<option>', {
                value: campaigns[i].id,
                text: (campaigns[i].title + "/" + campaigns[i].status)
            }));
            console.log(campaigns[i].id);
            console.log(campaigns[i].title);
        }
    }

    function addImages(images) {
        console.log("addImages -----------> ");
        //console.log(images);
        if (images == null) {
            return;
        }
        for (i = 0; i < images.length; ++i) {
            addImage(images[i].file_url, images[i].file_id);
            console.log("file_id -----------> ");
            console.log(images[i].file_id);

            console.log("addImages -----------> ");
            console.log(images[i]);
        }
    }
    function setGlobalParams(datas) {
        current_node_type = datas.data.TYPE;
        current_node_id = datas.id;
        $("#id_current_node_type").val(datas.data.TYPE);
        $("#id_current_node_id").val(datas.id);
    }
    /**
     * fill the question form
     */
    function setQuestion(datas) { //csi new
        //alert("set question");
        //return;
        console.log('setQuestion ----> ' + datas.data.TYPE);
        console.log(datas.data);
        //console.log(datas.id);
        emptyImages();
        addImages(datas.data.IMAGES);
        setGlobalParams(datas)
        $("#id_question_name").val(datas.text);
        $(".qc_input_question .jqte_editor").html(datas.data.INTITULE); //datas.text);
        $(".qc_good_response .jqte_editor").html(datas.data.GOOD_RESPONSE_TXT); //datas.text);
        $("#date_picker_good_answer").val(datas.data.GOOD_RESPONSE_DATE); //datas.text);
        $("#number_good_answer").val(datas.data.GOOD_RESPONSE_NUMBER); //datas.text);
        $("#id_question_type_reponse").val(datas.data.TYPE_REPONSE);
        $("#id_question_label").html(" (id:" + datas.id + ") ");

        //good_answer_text_wrapper
        var selectedVal = $("#id_question_type_reponse").val();
        hideResponsesQuick();
        switch (selectedVal) {
            case TEXT:
                $(".good_answer_text_wrapper").show(1000);
                $("#weight_good_answer_text").val(datas.data.points);
                console.log('TEXT');
                console.log('datas.data.points');
                console.log(datas.data.points);
                break;
            case NUMBER:
                $(".good_answer_number_wrapper").show(1000);
                $("#weight_good_answer_number").val(datas.data.points);
                console.log('NUMBER');
                break;
            case DATE:
                $(".good_answer_date_wrapper").show(1000);
                $("#weight_good_answer_date").val(datas.data.points);
                console.log('DATE');
                break;
            case QCM_INCLU:
                $(".good_answer_qcm_cbx_wrapper").show(1000);
                /**
                 * inclusive qcm 
                 */
                console.log('QCM_INCLU -> ' + datas.data.INCLUSIVE_TYPE_VALUES.length);
                createQCMInclu(datas.data.INCLUSIVE_TYPE_VALUES);
                console.log('QCM_INCLU');
                break;
            case QCM_EXCLU:
                $(".good_answer_qcm_radio_wrapper").show(1000);
                /**
                 * exclusive qcm 
                 */
                console.log('QCM_EXCLU -> ' + datas.data.EXCLUSIVE_TYPE_VALUES.length);
                createQCMExclu(datas.data.EXCLUSIVE_TYPE_VALUES);
                console.log('QCM_EXCLU');
                break;
            case CONDITIONNAL:
                $(".good_answer_conditionnal_wrapper").show(1000);
                console.log('CONDITIONNAL');
                break;
            default:
                console.log('UNKNOWN response type selected!');
        }
    }
    gui();
    closeColorBox();
    //QUESTION: listen to changes to save
    $(".jqte_editor").change(function () {
        $("#id_btn_save_question").removeAttr("disabled"); //.removeClass( "btn-success" );  
    });
    $("#id_question_type_reponse").change(function () {
        $("#id_btn_save_question").removeAttr("disabled");
    });
    $("#id_question_name").change(function () {
        $("#id_btn_save_question").removeAttr("disabled");
    });
    //QUESTIONNAIRE: listen to changes to save
    $("#id_questionnaire_name").change(function () {
        $("#id_btn_save_questionnaire").removeAttr("disabled");
    });
    $("#id_questionnaire_etat").change(function () {
        $("#id_btn_save_questionnaire").removeAttr("disabled");
    });
    $("#id_questionnaire_sauvegarde").change(function () {
        $("#id_btn_save_questionnaire").removeAttr("disabled");
    });
    $("#id_questionnaire_examen").change(function () {
        $("#id_btn_save_questionnaire").removeAttr("disabled");
    });
    $("#datepickerstart").change(function () {
        $("#id_btn_save_questionnaire").removeAttr("disabled");
    });
    $("#datepickerstop").change(function () {
        $("#id_btn_save_questionnaire").removeAttr("disabled");
    });
    $("#auto_correction").change(function () {
        $("#id_btn_save_questionnaire").removeAttr("disabled");
    });
    $("#id_questionnaire_comment").change(function () {
        $("#id_btn_save_questionnaire").removeAttr("disabled");
    });
    var that = $("#id_btn_save_questionnaire"); //.removeAttr("disabled");
    $(document).delegate('.txta_custom', 'change', function () {
        console.log("change2!");
        $("#id_btn_save_question").removeAttr("disabled");
    });
    $(document).delegate('.delete_btn_class', 'click', function () {
        console.log("change3!");
        $("#id_btn_save_question").removeAttr("disabled");
    });

    //d&d stuffs
    $("#sortable-multiple").sortable({
        placeholder: "ui-state-highlight",
    });
    //$( "#sortable-multiple" ).disableSelection();

    $("#sortable-mono").sortable({
        placeholder: "ui-state-highlight",
    });
    //$( "#sortable-mono" ).disableSelection();  


});

function buildData() {

    var questionnaires = [];
    var question = [];

    var o1 = { "test1": "coco1" }
    var o2 = { "test2": "coco2" }
    var o3 = { "test3": "coco3" }

    //questions
    question[0] = o1;
    question[1] = o2;
    question[2] = o3;

    for (var i = 0; i < 10; i++) {
        //questionnaires
        questionnaires[i] = question;
    }
    console.log(questionnaires);
}

function emptyImages() {
    $(".image_container").empty();
}
function addImage(url, id) {
    console.log("------------> addImage");
    console.log(url);
    console.log(id);

    console.log("------------------> getCurrentNode()");
    //console.log(getCurrentNode().id);
    //console.log(getCurrentNode());

    html = "   <div class='image_wrapper'> " +

           "     <label id='" + id + "' class='picture_to_delete' ><i class='fa fa-scissors'></i>  </label>             " +

           "     <div class='image_loaded' >          " +
           "         <img class='color_box_crochet' src='" + url + "' />           " +
           "     </div>  " +
           "   </div> ";

    $(".image_container").append(html);
}
function hideResponsesQuick() {
    $(".good_answer_text_wrapper").hide(500);
    $(".good_answer_date_wrapper").hide(500);
    $(".good_answer_number_wrapper").hide(500);
    $(".good_answer_qcm_cbx_wrapper").hide(500);
    $(".good_answer_qcm_radio_wrapper").hide(500);
    $(".good_answer_conditionnal_wrapper").hide(500);
}
function demo_create() {
    /*
     var sel = $("#jstree_demo").jstree(true).create_node("#", {
     "text": "New questionnaire",
     "type": "root","data" : 
     { 
     "TYPE" :       "QUESTIONNAIRE",
     "ETAT" :       "1",
     "SAUVEGARDE" : "2",
     "EXAMEN" :     "3",
     "DATE_START" : "23/12/2014",
     "DATE_STOP" :  "24/12/2014",
     "AUTO_CORRECTION" :  "1",
     "COMMENT" :  "comment 1"                                                         
     } 
     }, "first", null, false);
     console.log(sel);
     gui();*/
}
function selectResponseType() {
    //response type management
    var selectedVal = $("#id_question_type_reponse").val();
    switch (selectedVal) {
        case TEXT:
            $(".good_answer_text_wrapper").show(1000);
            console.log('TEXT');
            break;
        case NUMBER:
            $(".good_answer_number_wrapper").show(1000);
            console.log('NUMBER');
            break;
        case DATE:
            $(".good_answer_date_wrapper").show(1000);
            console.log('DATE');
            break;
        case QCM_INCLU: //007
            $(".good_answer_qcm_cbx_wrapper").show(1000);
            var node = getCurrentNode();
            createQCMInclu(node.data.INCLUSIVE_TYPE_VALUES);      //0071          
            console.log('QCM_INCLU');
            break;
        case QCM_EXCLU:
            $(".good_answer_qcm_radio_wrapper").show(1000);
            var node = getCurrentNode();
            createQCMExclu(node.data.EXCLUSIVE_TYPE_VALUES);
            console.log('QCM_EXCLU');
            break;
        case CONDITIONNAL:
            $(".good_answer_conditionnal_wrapper").show(1000);
            console.log('CONDITIONNAL');
            break;
        default:
            console.log('UNKNOWN response type selected!');
    }
}
function runThrougAlllQCMExcl(id, node) {// 00777

    node.data.EXCLUSIVE_TYPE_VALUES = [];
    var counter = 0;
    $(id).each(function () { // txta_custom
        var obj = {
            text: $(this).children("textarea").val(),
            points: $(this).children(".points_class").children(".points").val(), // points_class
            isGood: $(this).children(".qcm_inside_li_cbox").children(".xxx").is(':checked'),
            type: ""
        };
        console.log("debug new EXCUSIVE--------->");
        console.log(obj);
        console.log(id);
        node.data.EXCLUSIVE_TYPE_VALUES[counter] = obj;
        counter++
    });
}
function runThrougAlllQCMIncl(id, node) {// 007

    node.data.INCLUSIVE_TYPE_VALUES = [];
    var counter = 0;
    console.log("debug INCLUSIVES--------->");
    $(id).each(function () { // txta_custom
        var obj = {
            text: $(this).children("textarea").val(),
            points: $(this).children(".points_class").children(".points").val(), // points_class
            isGood: $(this).children(".qcm_inside_li_cbox").children(".xxx").is(':checked'),
            type: ""
        };
        console.log(obj);
        node.data.INCLUSIVE_TYPE_VALUES[counter] = obj;
        counter++
    });
}
function createQCMExclu(excluValues) { //<input type="radio" name="option1" value="Milk">' + 
    console.log('-------------------->>>>>>>>>>createQCMExclu');
    console.log(excluValues);
    if (excluValues == null) {
        cboxValue = "";
        var x =
        '    <li class="ui-state-default">' +
        '        <div class="qcm_inside_li_cbox">' +
        '        <input ' + cboxValue + 'type="radio" class="xxx" name="option1" value="Milk_a">' +
        '        </div>' +
        '        <textarea class="txta_custom" >' + ' New excl possible answer' + '</textarea>' +

        '        <div class="points_class" > <span class="point_label" >Points</span> ' +
        '        <input class="points" type="text" name="lname" style="height:30px;width:60px"></div> ' +

        '         <div class="delete_btn">' +
        '              <button ' +
        '                   type="button"  ' +
        '                   class="btn btn-danger delete_btn_class" >' +
        '                   <i class="fa fa-times"></i>' +
        '              </button>  ' +
        '         </div>                    ' +
        '    </li>    ';
        $(".qcm_exc ul").append(x);
        return;
    } else {
        $(".qcm_exc ul li").remove();
    }
    //$(".qcm_exc ul li").remove();

    for (i = 0; i < excluValues.length; ++i) {

        var cboxValue = "";
        if (excluValues[i].isGood == true) {
            console.log(' excluValues is TRUE ' + excluValues[i].isGood);
            cboxValue = " CHECKED ";
        } else {
            console.log(' excluValues is FALSE ' + excluValues[i].isGood);
            cboxValue = " ";
        }

        console.log("the value: " + excluValues[i].text);
        var x =
        '    <li class="ui-state-default">' +
        '        <div class="qcm_inside_li_cbox">' +
        '        <input ' + cboxValue + ' type="radio" class="xxx" name="option1" value="Milk_b">' +
        '        </div>' +
        '        <textarea class="txta_custom" >' + excluValues[i].text + '</textarea>' +

        '        <div class="points_class" > <span class="point_label" >Points</span> ' +
        '        <input class="points" type="text" value="' + excluValues[i].points + '" name="lname" style="height:30px;width:60px"></div> ' +

        '        <div class="delete_btn">' +
        '              <button ' +
        '                   type="button"  ' +
        '                   class="btn btn-danger delete_btn_class" >' +
        '                   <i class="fa fa-times"></i>' +
        '              </button>  ' +
        '        </div>                    ' +
        '    </li>    ';
        console.log(x);
        var qqq = $(".qcm_exc ul");
        console.log(qqq);
        $(".qcm_exc ul").append(x); //"<li>tititi</li>");
    }

}
function deleteQCMs() {
    $(".qcm_incl ul li").remove();
    $(".qcm_exc ul li").remove();
}
function createQCMInclu(incluValues) {

    if (incluValues == null) {
        console.log('createQCMInclu: incluValues sont nulles');
        cboxValue = "";
        var x =
        '    <li class="ui-state-default">' +
        '        <div class="qcm_inside_li_cbox">' +
        '        <input ' + cboxValue + 'type="checkbox" name="option1" class="xxx" value="Milk_c">' +
        '        </div>' +
        '        <textarea class="txta_custom" >' + ' New incl possible answer' + '</textarea>' +

        '        <div class="points_class" > <span class="point_label" >Points</span> ' +
        '        <input class="points" type="text" name="lname" style="height:30px;width:60px"></div> ' +

        '         <div class="delete_btn">' +
        '              <button ' +
        '                   type="button"  ' +
        '                   class="btn btn-danger delete_btn_class" >' +
        '                   <i class="fa fa-times"></i>' +
        '              </button>  ' +
        '         </div>                    ' +
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
        if (incluValues[i].isGood == true) {
            console.log(' incluValues is TRUE ' + incluValues[i].isGood);
            cboxValue = " CHECKED ";
        } else {
            console.log(' incluValues is FALSE ' + incluValues[i].isGood);
            cboxValue = "  ";
        }
        var x =
        '    <li class="ui-state-default">' +
        '        <div class="qcm_inside_li_cbox">' +
        '        <input ' + cboxValue + 'type="checkbox" name="option1" class="xxx" value="Milk_d">' +
        '        </div>' +
        '        <textarea class="txta_custom" >' + incluValues[i].text + '</textarea>' +

        '        <div class="points_class" > <span class="point_label" >Points</span> ' +
        '        <input class="points" type="text" value="' + incluValues[i].points + '" name="lname" style="height:30px;width:60px"></div> ' +

        '         <div class="delete_btn">' +
        '              <button ' +
        '                   type="button"  ' +
        '                   class="btn btn-danger delete_btn_class" >' +
        '                   <i class="fa fa-times"></i>' +
        '              </button>  ' +
        '         </div>                    ' +
        //'         Last name: <input type="text" name="lname">' + 
        '    </li>    ';
        $(".qcm_incl ul").append(x);
    }
}
function fillQuestionnaires(data) {

    //alert("installer la structure data ici");

    if (data.data.TYPE == "QUESTIONNAIRE") {
        /////var val = '<li id="qaire_' + data.id + '" class="ui-state-default tree_title clickable">  ' + data.text + '  </li>';
        /////$("#jstree_demo_ul").append(val);
        buildDataStore("QUESTIONNAIRE",data);
    } else if (data.data.TYPE == "QUESTION") {
        /////var val = '  <li id="qaire_' + data.parent + '_qion_' + data.id + '" class="ui-state-default tree_question clickable">  ' + data.text + '  </li>';
        /////$("#jstree_demo_ul").append(val);
        buildDataStore("QUESTION", data);
    }
    
}
function buildDataStore(flagType,data) {

    if (flagType == "QUESTIONNAIRE")
    {

        if (dataStored[data.id] == undefined) {
            var qcmObjet = {
                questionnaire: null,
                questions: []
            };
            qcmObjet.questionnaire = data;
            dataStored[data.id] = qcmObjet;
        }
    }
    else if (flagType == "QUESTION")
    {
        if (dataStored[data.parent] == undefined) {
            alert("IMPOSSIBLE!");
        } else {
            //mettre dans le bon questionnaire
            var qcmObjet = dataStored[data.parent];
            qcmObjet.questions[data.id] = data;
            dataStored[data.parent] = qcmObjet;
        }
    }

    /* csi
    var questionnaires = [];
    var question = [];

    var o1 = { "test1": "coco1" }
    var o2 = { "test2": "coco2" }
    var o3 = { "test3": "coco3" }

    //questions
    question[0] = o1;
    question[1] = o2;
    question[2] = o3;

    for (var i = 0; i < 10; i++) {
        //questionnaires
        questionnaires[i] = question;
    }
    console.log(questionnaires);
    */

}
function gui(data) {

    //fillQuestionnaires();

    // jstree_demo

    //return;

    if (!displayStuffs) {
        return;
    }
    deleteQCMs();
    /**
     * if nothing selected in the jsTree
     */
    //var ref = $('#jstree_demo').jstree(true);
    try {
        //sel = ref.get_selected();
        //console.log("in gui())" + sel.length);
        //console.log($('#jstree_demo').jstree(true).get_node(sel).data.TYPE);
        if (data == undefined) { //sel.length == 0) {
            $("#tab_wrapper").hide();
            $("#id_btn_create_question").hide(1000);
            $("#id_btn_create_questionnaire").show(1000);
            $("#id_btn_delete_node").hide(1000);
        } else {
            /**
             * a questionnaire is selected
             */
            if ("QUESTIONNAIRE" == data.data.TYPE ){ //"toto"){ //$('#jstree_demo').jstree(true).get_node(sel).data.TYPE) {
                hideResponsesQuick();
                //$("#tab_wrapper").hide();
                $("#id_btn_create_question").show(1000);
                $("#id_btn_create_questionnaire").show(1000);
                $("#id_btn_delete_node").show(1000);
                $("#id_questionnaire").show(1000);
                $("#id_question").hide(1000);
                //$("#id_btn_save_questionnaire").attr("disabled", "disabled");
            }
                /**
                 * a question is selected
                 */
            else if ("QUESTION" == data.data.TYPE ){ //"tata"){ //$('#jstree_demo').jstree(true).get_node(sel).data.TYPE) {
                console.log('777777777777');
                //$("#tab_wrapper").hide();
                $("#id_btn_create_question").hide(1000);
                $("#id_btn_create_questionnaire").show(1000);
                $("#id_btn_delete_node").show(1000);
                $("#id_questionnaire").hide(1000);
                $("#id_question").show(1000);
                //$("#id_btn_save_question").attr("disabled", "disabled");
                hideResponsesQuick();
                selectResponseType();
            }
        }
    } catch (err) {
        console.log("exception in function gui()");
        isStillUsingGoodSession();
    }
    //$("#jstree_demo").jstree().refresh();    
}
function hideAllQuestionsQuestionnaires() {
    //alert('fuck');
    hideResponsesQuick();
    //$("#tab_wrapper").hide();
    $("#id_btn_create_question").hide(1000);
    $("#id_btn_create_questionnaire").hide(1000);
    $("#id_btn_delete_node").hide(1000);
    $("#id_questionnaire").hide(1000);
    $("#id_question").hide(1000);
    //$("#id_btn_save_questionnaire").attr("disabled", "disabled");
    $("#tab_wrapper").hide(1000);

}
function getCurrentNode() {
    var ref = $('#jstree_demo').jstree(true);
    sel = ref.get_selected();
    if (sel.length > 0) {
        return $('#jstree_demo').jstree(true).get_node(sel);
    } else {
        return null;
    }
}
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

