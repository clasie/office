<?php
/**
 * bootstrap
 */
include_once "../config/config.class.php";
include_once "../core/mvc.class.php";
include_once "../core/workflow.class.php";
//include_once "../lib_custom/includes.php";
include_once '../lib_custom/Constant.class.php';
include_once '../config/config.php';
include_once '../lib_custom/Auth.class.php';//to do
include_once "../core/model/db.class.php";
///include_once '../lib_custom/Crud.class.php';

/**
 * new fwk building
 */

global $workflow; //bad way to make it accessible to the layout
$workflow = new Workflow();
/**
 * run the request
 */

$workflow->run();

