<?php 

	//SHOW ALL ERRORS
	ini_set('display_errors',1);
	error_reporting(E_ALL);

	//START THE ZLCMS SESSION
	session_start();

	//INCLUDE THE ZLCMS SYSTEM CLASS
	include("core/classes/controller.class.php"); 

	//INITIATE THE ZLCMS CLASS
	$controller = new controller();
	
	//SELETS A WEBSITE AND LOADS THE PAGES FROM THE DB
	$controller->initiate($_SERVER['SERVER_NAME'],@$_GET['url']);

	//SERVES AS A CONSTRUCT FOR ALL PLUGINS BUT LOADS AFTER THEY ARE INITIATED
	$controller->class_construct();

	//PROCESSES ALL POST/GET REQUESTS WITH ACTION SET (USED FOR FORM PROCESSING)
	$controller->process_request(@$_REQUEST['action']);

	//LOAD UP THE TEMPLATE
	$controller->load_template();

	//CLOSE THE DATABASE CONNECTION
	$controller->close_database();
?>
