<?PHP

	//*****************************************************************************************************************************************
	/**
	* ZLCMS Form Class
	*
	* This is a helper class inteded to assist with form validation and repopulation. There are a series of functions
	* like set_rule and run_rule that assist with form validation and then several functions for repopulating form values and
	* handling errors.
	*
	* @package    ZLCMS
	* @subpackage form
	* @author     Ryan Stemkoski <ryan@gozipline.com>
	* @copyright  2005-2010 Zipline Communications Inc.
	* @version    1.0
	* @link       http://www.ziplineinteractive.com
	*/
	//*****************************************************************************************************************************************
	class form {
	
		var $error = array();
		var $rules = true;
	
		//*************************************************************************************************************************************
		/**
		* This method sets rules with various parameters which run_rules() checks against to validate input values from a form.  
		* @param $field string Is the $_POST field from the form for which we are validating
		* @param $params string Is a pipe seperated list of parameters to check against
		* @return $error string Is a text error message to return if the validation for the item fails
		* @return $value string|integer A value we are attempting to match against for various parameter checks
		*/
		//*************************************************************************************************************************************
		function set_rule($field = "",$params = "empty",$error = "",$value = "") {
		
			//MAKE SURE FIELDS IS SET. IF NOT THEN SKIP THIS FUNCTION ENTIRELY
			if(!empty($field) && !empty($error)) {
			
				//TAKE THE LIST OF PARAMS EACH PARAM REQUIRE A DIFFERENT ACTION
				$param = explode("|",$params);
				
				//SCAN THE LIST OF ITEMS THE USER WANTS TO ANALYZE AGAINST
				foreach($param as $check) {
				
					//CHECK TO SEE IF THE FIELD IS EMPTY 
					if($check == "empty") {
						if(empty($field)) {
							$this->rules = false;
							$this->error[] = $error;	
						}
					}
					
					//CHECK TO SEE IF THE VALUE MATHES HE FIELD VALUE
				 	if($check == "value") {
						if(!empty($value)) {
							if($field != $value) {
								$this->rules = false;
								$this->error[] = $error;
							} 
						} else {
							$this->rules = false;
							$this->error[] = $error;
						}
					}
					
					//CHECK TO SEE IF AN EMAIL IS VALID
					if($check == "email") {
						if(!filter_var($field, FILTER_VALIDATE_EMAIL)) {
							$this->rules = false;
							$this->error[] = $error;
						}
					}
					
					//ATTEMPTS TO VALIDATE A NAME
					if($check == "name") {
						if(!preg_match("/^\w+(?:(?:(?:[ '-])|(?:\.[ ]))\w+)?$/",$field)) {
							$this->rules = false;
							$this->error[] = $error;
						}
					}
					
					//CHECK FOR PHONE IN XXX-XXX-XXXX FORMAT
					if($check == "phone") {
						if(!preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $field)) {
							$this->rules = false;
							$this->error[] = $error;
						}
					}
					
					//CHECK FOR PHONE IN XXX-XXX-XXXX FORMAT
					if($check == "ssn") {
						 if(!preg_match("/\d{3}[-\s]?\d{2}[-\s]?\d{4}/", $field)) {
							$this->rules = false;
							$this->error[] = $error;
						}
					}
					
					//CHECK DATE
					if($check == "date") {
						$date = explode("-",$field);
						if(isset($date[0]) && isset($date[1]) && isset($date[2])) {
							if(!checkdate($date[1],$date[2],$date[0])) {
								$this->rules = false;
								$this->error[] = $error;
							}
						} else {
							$this->rules = false;
							$this->error[] = $error;
						}
					}

					//CHECK DATE
					if($check == "date_friendly") {
						$date = explode("/",$field);
						if(isset($date[0]) && isset($date[1]) && isset($date[2])) {
							if(!checkdate($date[0],$date[1],$date[2])) {
								$this->rules = false;
								$this->error[] = $error;
							}
						} else {
							$this->rules = false;
							$this->error[] = $error;
						}
					}
					
					//CHECK IF AGE IS 18 BASED ON BIRTHDAY MM/DD/YYYY
					if($check == "eighteen") {
						$date = explode("/",$field);
						if(isset($date[0]) && isset($date[1]) && isset($date[2])) {
							$minDate = date(Y) - 18;
							if($date[2] > $minDate) {
								$this->rules = false;
								$this->error[] = $error;
							}
							elseif ($date[2] == $minDate) {
								if ($date[0] > date(m)) {
									$this->rules = false;
									$this->error[] = $error;
								}
								elseif ($date[0] == date(m)) {
									if ($date[1] > date(d)) {
										$this->rules = false;
										$this->error[] = $error;
									}
								}
							}
						} else {
							$this->rules = false;
							$this->error[] = $error;
						}
					}
					
					//GREATER THAN
					if($check == "greater") {
						if($field < $value) {
							$this->rules = false;
							$this->error[] = $error;
						}
					}
					
					//LESS THAN
					if($check == "less") {
						if($field > $value) {
							$this->rules = false;
							$this->error[] = $error;
						}
					}
					
					//LESS THAN
					if($check == "array") {
						if(!is_array($field)) {
							$this->rules = false;
							$this->error[] = $error;
						}
					}					
				}
			
			} else {
				$this->rules = false;
				if(!empty($error)) {
					$this->error[] = $error;
				} else {
					$this->error[] = "An unexpected error occurred likely resulting from an invalid value";
				}
			}
		
		}
		
		//*****************************************************************************************************************************************
		/**
		* This is a method function that checks to see if any of the set rules have failed. If not it will return true
		* otherwise it will return false. This function can be used to determine if a form should continue after the rules have
		* checked.
		*/
		//*****************************************************************************************************************************************
		function run_rules() {
			if($this->rules == true) {
				return true;
			} else {
				return false;
			}
		}

		//*****************************************************************************************************************************************
		/**
		* This function is used when repopulating a form. It will take a default value and an entered value and determine which to
		* show as the form value.
		* @param $default string is an optional default value that can be used to pupulate value. This may be the database value on an update form.
		* @param $entered string is an optional value that can be used to override a default value if one is set. This would be the value entered into the form when an error occurs.
		* @param $return boolean is an optional value that can be used to return the result rather than output it directly to the template.
		*/
		//*****************************************************************************************************************************************
		function toggle_value($default,$entered,$return = false) {
			
			//CHOOSE PROPER VALUE
			if(!empty($entered)) {
				$output = $entered;
			} else {
				$output = $default;
			}
			
			//RETURN OR ECHO
			if($return == true) {
				return $output;
			} else {
				echo($output);
			}
			
		}
		
		//*****************************************************************************************************************************************
		/**
		* This function determines if a checkbox is checked based on provided values.
		* @param $entered string is the value from the query, $_POST, or $_SESSION we're validating against.
		* @param $value string is a string comparison value. If $entered is the same as $value then the box is checked.
		* @param $initial string is the initial value set for hte form field from the database
		* @param $default string is an optional value allowing the box to be pre-selected in case the author wants the box to be checked by default.
		* @param $return boolean is an optional value that can be used to return the result rather than output it directly to the template.
		*/
		//*****************************************************************************************************************************************
		function is_checked($entered,$value,$initial="",$default="",$return = false) {
			
			//SET VALUE TO USE 
			if(!empty($initial)) {
				$check = $this->toggle_value(@$initial,@$entered,true);
			} else {
				$check = $entered;
			}
						
			//CHOOSE PROPER VALUE
			if(isset($check)) {
				if($check == $value) {
					$output = " checked=\"checked\"";
				} else {
					$output = "";
				}
			} else {
				if($default == $value) {
					$output = " checked=\"checked\"";
				} else {
					$output = "";
				}
			}
			
			//RETURN OR ECHO
			if($return == true) {
				return $output;
			} else {
				echo($output);	
			}
		}
		
		//*****************************************************************************************************************************************
		/**
		* This function determiens if a select list is selected.
		* @param $entered string is the value from the query, $_POST, or $_SESSION we're validating against.
		* @param $value string is a string comparison value. If $entered is the same as $value then the box is checked.
		* @param $initial string is the initial value set for hte form field from the database
		* @param $default string is an optional value allowing the box to be pre-selected in case the author wants the box to be checked by default.
		* @param $return boolean is an optional value that can be used to return the result rather than output it directly to the template.
		*/
		//*****************************************************************************************************************************************
		function is_selected($entered,$value,$initial,$default="",$return = false) {
				
			
			//SET VALUE TO USE 
			if(!empty($initial)) {
				$check = $this->toggle_value(@$initial,@$entered,true);
			} else {
				$check = $entered;
			}
			
			//CHOOSE PROPER VALUE
			if(isset($check)) {
				if($check == $value) {
					$output = " selected=\"selected\"";
				} else {
					$output = "";
				}
			} else {
				if($value == $default) {
					$output = " selected=\"selected\"";
				} else {
					$output = "";
				}
			}
			
			//RETURN OR ECHO
			if($return == true) {
				return $output;
			} else {
				echo($output);	
			}
		}
	
	}
	
?>