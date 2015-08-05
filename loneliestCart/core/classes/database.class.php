<?PHP

	//*****************************************************************************************************************************************
	/**
	*
	* The database class is a simple database abstraction layer developed by Zipline Interactive to help organize, abstract, and prepare SQL queries.  This version is designed to interact with a MySQL database using a PHP MySQLi object. This database abstraction class is used throughout the ZLCMS system for all database queries. This library provides a number of standardized tools to create, escape, and execute SQL queries.  Each query creates a query object with all data and details regarding the query. The code for the query object is included within the same PHP file as the database class.
	* 
	* @package    ZLCMS
	* @subpackage database
	* @author     Ryan Stemkoski <ryan@ziplineinteractive.com>
	* @copyright  2012 Zipline Communications Inc.
	* @version    2.1
	* @link       http://www.ziplineinteractive.com
	*/
	//*****************************************************************************************************************************************
	class database {
		
		/**
		* Variable that holds the SQL query statement used in all queries. Data for this variable is set when each query occurs by the escaping function.
		* @see function escape_sql
		* @var string 
		*/
		var $sql = "";
		
		/**
		* When a database error occurs it is captured and stored in the error variable.  This variable can output by itself or it can be shown by running the error() function.
		* @see function error
		* @var string 
		*/
		var $error = "";
		
		/**
		* This method stores the escaping method used for the database queries.  Default is mysql_real_escape_string but if for some reason that isn't available it will default to addslashes unless a standard MySQL connection is available. 
		* @see function determine_escaping
		* @var string 
		*/	
		var $escape = "";
		
		
		//*************************************************************************************************************************************
		/**
		* This method is the primary function within this class.  Every database query  in the website executes this function.  
		* This function will do a query and return an result object.
		* @param $raw_sql string Is a raw SQL statement that uses ? in place of unenescaped variables
		* @param $values array Is an array of values that are escaped and used to replace the ? in the raw sql
		* @return $query object A result object containg the result, number of rows, and other factors
		*/
		//*************************************************************************************************************************************
		public function query($raw_sql,$values = array()) {
			
			//IMPORT GLOBAL ZLCMS
			global $controller;
	
			//IF THERE ARE ? AND AN ARRAY THEN MATCH THEM UP RUNNING THE CORRECT ESCAPING FUNCTION
			$this->escape_sql($raw_sql,$values);
	
			//DO QUERY
			$this->error = "";
			$this->query = $controller->db->query($this->sql);
			$this->affected_rows = $controller->db->affected_rows;
	
			//IF ERROR ADD IT
			if($controller->db->error) {
				$this->error = $controller->db->error;
				return false;
			} else {
				$insert_id = $controller->db->insert_id;
				return new query($this->query,$this->affected_rows,$this->escape,$insert_id);		
			}
	
		}
	
		//*************************************************************************************************************************************
		/**
		* This method is designed to assist in inserting a large number of variables into the database.  It works by merging the post array
		* with a set of available fields and a table then generates and executes sql. 
		*
		* @param $fields array Is a list of fields that are available for insert into the database. Key names must match those in post array.
		* @param $data array Is a post array of data with keys matching fields. (Note: non matching keys will be ignored)
		* @param $table string Is the table in which the insert should occur.
		* @return $query object A result object containg the result, number of rows, and other factors
		*/
		//*************************************************************************************************************************************
		public function insert_auto_helper($fields = array(),$data = array(),$table = "") {
			
			//IMPORT GLOBAL DB CLASS
			global $controller;
			
			//CONSTRUCT SQL INSERT QUERY
			$sql = "INSERT INTO {$table} (";
			$length = count($fields);
			$n = 1;
			
			//ADD THE FIELDS
			foreach($fields as $field) {
				if($n < $length) {
					$sql .= "{$field},";
				} else {
					$sql .= "{$field}";
				}
				$n++;
			}
			
			//INSERT THE VALUES
			$sql .= ") VALUES(";
				$length = count($fields);
				$n = 1;
				
				//ESCAPE EACH VALUE
				foreach($fields as $field) {
					if($n < $length) {
						$sql .= "'" . $this->escape_string($data[$field]) . "',";
					} else {
						$sql .= "'" . $this->escape_string($data[$field]) . "'";
					}
					$n++;
				} 
				
			//COMPLETE QUERY
			$sql .= ")";
			
			//DO INSERT
			$query = $this->query($sql);
			if(empty($this->error)) {			
				return $query;
			} else {
				return false;
			}
		}

		//*************************************************************************************************************************************
		/**
		* This method is designed to assist in updating a large number of variables in the database.  It works by merging the post array
		* with a set of available fields and a table then generates and executes sql. 
		* 
		* @param $fields array Is a list of fields that are available for insert into the database. Key names must match those in post array.
		* @param $data array Is a post array of data with keys matching fields. (Note: non matching keys will be ignored)
		* @param $table string Is the table in which the insert should occur.
		* @param $where string Is the where aregument in the SQL.  Use ? for non escaped variables
		* @param $arguments array Is an array of the values to escape and inject into the sql in place of the ? marks.
		* @return $query object A result object containg the result, number of rows, and other factors
		*/
		//*************************************************************************************************************************************
		public function update_auto_helper($fields = array(),$data = array(),$table = "",$where = "",$arguments = array()) {
				
			//IMPORT GLOBAL ZLCMS
			global $controller;
				
			//CLEAN SQL
			$sql = "";	
			
			//CONSTRUCT SQL QUERY
			$sql .= "UPDATE {$table} SET ";
			$length = count($fields);
			$n = 1;
			foreach($fields as $field) {
				if($n < $length) {
					$sql .= "{$field}='" . $this->escape_string($data[$field]) . "',";
				} else {
					$sql .= "{$field}='" . $this->escape_string($data[$field]) . "'";
				}
				$n++;
			}
			
			//IF NOT EMPTY WHERE
			if(!empty($where)) {
				$sql .= " ";
				$this->escape_sql($where,$arguments);
				$sql .= $this->sql;
			}
		
			//DO UPDATE
			$query = $this->query($sql);
			if(empty($this->error)) {
				return $query;
			} else {
				return false;
			}
	
		}
		
		//*************************************************************************************************************************************
		/**
		* This method checks to see what types of escaping options are available on the server then chooses the proper one to escape
		* variables in teh SQL statement. 
		* 
		* @return $this->escape string A string that represents the escaping method to use based on the settings of the server.
		*/
		//*************************************************************************************************************************************
		protected function determine_escaping() {
		
			//IMPORT GLOBAL ZLSMCS FOR DB OBJECT
			global $controller;
		
			//REMOVED MYTSQL_REAL_ESCAPE_STRING
			if(method_exists($controller->db,"real_escape_string")) {
				$this->escape = "mysqli_real_escape_string";
			} else {
				if($this->server == "mysql") {
					if(function_exists("mysql_real_escape_string")) {
						$this->escape = "mysql_real_escape_string";
					} else if(function_exists("mysql_escape_string")) {
						$this->escape = "mysql_escape_string";
					} else {
						$this->escape = "addslashes";
					}
				} else {
					$this->escape = "addslashes";
				}
			}
		}
		
		
		//*************************************************************************************************************************************
		/**
		* This method escapes a string prior to execution of a SQL statement using the escape value set previously.
		* 
		* @param $value mixed The variable that needs to be escaped 
		* @uses $this->escape string A string that represents the escaping style to be used.
		*/
		//*************************************************************************************************************************************
		public function escape_string($value) {
		
			//GLOBAL ZLCMS
			global $controller;
		
			//DETERMINE THE PROPER ESCAPING
			$this->determine_escaping();
			
			//IF MYSQLI USE THE MYSQL ESCAPING METHOD
			if($this->escape == "mysqli_real_escape_string") {
				$output = $controller->db->real_escape_string($value);
			} else {
				$output = call_user_func($this->escape,$value);
			}
			return $output;
		}

		
		//*************************************************************************************************************************************
		/**
		* This method escapes a full SQL statement prior to the execution of a query
		* 
		* @param $raw_sql string A raw SQL string that needs to be escaped. ? marks should be used in place of variables that need to be escaped.
		* @param $values array An array of values to escape and replace the ? in the raw SQL with.
		* @uses escape_string() A function that that escapes data based. 
		* @returns $this->sql string An escaped SQL string
		*/
		//*************************************************************************************************************************************
		protected function escape_sql($raw_sql="",$values = array()) {
			
			global $controller;
		
			//TAKE THE VALUES AND DO THE ESCAPING
			if(is_array($values)) {
				if(count($values) >= 1) {
					$field = 0;
					$raw_sql = explode("?",$raw_sql);
					if(is_array($raw_sql)) {
						$total_fields = count($values);
						$new_sql = "";
						foreach($raw_sql as $sql_part) {
							$new_sql .= $sql_part;
							if($field < $total_fields) {
								$new_sql .= "'" . $this->escape_string($values[$field]) . "'";
							}
							$field++;
						}
						$sql = $new_sql;
					} else {
						$sql = $raw_sql;
					}	
					
					$this->sql = $sql;
				} else {
					$this->sql = $raw_sql;
				}
			} else {
				$this->sql = $raw_sql;
			}
		}
		
		//*************************************************************************************************************************************
		/**
		* This method is used to display the raw SQL for debugging purposes before it has been escaped.
		* 
		* @param $return boolean If this value is set to true the function will return the string otherwise it will echo it.
		*/
		//*************************************************************************************************************************************
		public function raw_sql($return = false) {

$sql = 'fired';
echo $sql;
		
			if(!empty($this->sql)) {
			
				if($return == true) {
					return $this->sql;
				} else {
					echo $this->sql;
				}
			} 
		}
		
		//*************************************************************************************************************************************
		/**
		* This method turns an associtive array into an standard array with the previous keys as the value for use in queries
		* @param $array array An associtive array of data usually from $_POST
		* @param $exclude array A array of values to exclude from the new array. Usually submit, action, you don't want in DB.
		*/
		//*************************************************************************************************************************************        
		public function convert_array_to_fields($array, $exclude = array()) {
		
			//CLEAR OUTPUT FOR USE
			$output = array();
			
			//LOOP ARRAY AND CONSTRUCT A NEW ARRAY
			if(is_array($array)) {
			   foreach($array as $key => $value) {
			       if(!in_array($key,$exclude)) {
			           $output[] = $key;
			       }
			   }
			   return $output;
			}
		}
		
		
		//*************************************************************************************************************************************
		/**
		* This method is used to output an error if one exists otherwise it will return false
		* @uses $this->error string A string that contains a detailed error message from a SQL error.
		*/
		//*************************************************************************************************************************************        
		public function error() {
			if($this->error) {
				return $this->error;
			} else {
				return false;
			}
		}
	
	}
	
	

	//*****************************************************************************************************************************************
	/**
	*
	* This is an object created for each query instance that contains a number of variables related to that query. 
	* 
	* @package    ZLCMS
	* @subpackage query
	* @author     Ryan Stemkoski <ryan@ziplineinteractive.com>
	* @copyright  2012 Zipline Communications Inc.
	* @version    2.1
	* @link       http://www.ziplineinteractive.com
	*/
	//*****************************************************************************************************************************************
	class query {
	
		/**
		* Raw query object coming from the database class of the completed query.  This object can be used to expand the functionality of this class to include additional MySQLi functionality that was not included.
		* @var object 
		*/
		var $query_result = "";
		
		/**
		* This variable is created when a query occurs to determine the number of affected_rows.
		* @var integer 
		*/
		var $affected_rows = "";
		
		/**
		* This variable is created when a query occurs to determine the number of number of rows returned.
		* @var integer 
		*/
		var $num_rows = "";
		
		/**
		* This variable is passed from the database function and contains the name of the escaping method used by the database class so that data can be unescaped
		* @var string 
		*/
		var $escape = "";
		
		/**
		* This variable is created when a query occurs and contains the insert id of the object if there is one.
		* @var integer 
		*/
		var $insert_id = "";
	
		//*************************************************************************************************************************************
		/**
		* This method is executed by the database query function to construct a result and affected rows values
		* 
		* @param $query_result object Is the object created by the MySQLi object.
		* @param $affected_rows integer Is the number of rows affected by the latest query.
		* @param $escape string Is the escaping type for the query that took place.
		* @param $insert_id integer The insert ID if the query is an insert query
		*/
		//*************************************************************************************************************************************
		public function query($query_result,$affected_rows,$escape,$insert_id) {
			$this->query_result = $query_result;
			$this->affected_rows = $affected_rows;
			$this->insert_id = $insert_id;
		}
		
		//*************************************************************************************************************************************
		/**
		* This method creates an associtive array using the data returned in the query
		* 
		* @uses $this->result object An object containing the database results information
		* @uses $this->escape string Is the type of escaping used for the query. If it is add to slashes the function auto removes slashes.
		*/
		//*************************************************************************************************************************************
		public function fetch_assoc() {
			$this->result = $this->query_result->fetch_assoc();
			if($this->escape == "addslashes") {
				foreach($this->result as $key=>$value) {
					$this->result[$key] = stripslashes($value);
				}
			}
			return $this->result;
		}
		
		//*************************************************************************************************************************************
		/**
		* This method returns the number of rows contained in the last query.
		* 
		* @uses $this->result object An object containing the database results information
		* @return $this->num_rows integter A value reprsenting the number of rows contained within the latest query.
		*/
		//*************************************************************************************************************************************
		public function num_rows() {
			if(!empty($this->query_result->num_rows)) {
				$this->num_rows = $this->query_result->num_rows;
			} else {
				$this->num_rows = $this->affected_rows;
			}
			return $this->num_rows;
		}
		
		
		//*************************************************************************************************************************************
		/**
		* This method returns the insert id for the last inserted 
		* 
		* @uses $this->result object An object containing the database results information
		* @return $this->insert_id integer A value representing the number of rows contained within the latest query.
		*/
		//*************************************************************************************************************************************
		public function insert_id() {
			return $this->insert_id;
		}
		
		
		//*************************************************************************************************************************************
		/**
		* This method returns the number of affected rows.
		* 
		* @uses $this->result object An object containing the database results information
		* @return $this->affected_rows integter A value reprsenting the number of rows contained within the latest query.
		*/
		//*************************************************************************************************************************************
		public function affected_rows() {
			return $this->affected_rows;
		}
	
	}

	
?>