<?PHP

	/*********************************************************************************************************
	//DATABASE CONNECTION
	**********************************************************************************************************/

	//ATTEMPT TO CONNECT TO THE DATABASE
	$db = new mysqli($this->connect['hostname'],$this->connect['database_username'],$this->connect['database_password'],$this->connect['database']);    
	
	//IF ATTEMPT FALES THEN LOG ERROR INTO ARRAY OF RUNTIME ERRORS
	if (mysqli_connect_errno()) {
	  $this->log_error(mysqli_connect_error());
	} 

?>