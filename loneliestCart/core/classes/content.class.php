<?PHP

	//*****************************************************************************************************************************************
	/**
	*
	* Front-end Content Plugin
	* 
	* @package    ZLCMS
	* @subpackage content
	* @author     Ryan Stemkoski <ryan@gozipline.com>
	* @copyright  2005-2010 Zipline Communications Inc.
	* @version    2.0
	* @link       http://www.ziplineinteractive.com
	*/
	//*****************************************************************************************************************************************
	class content {
		
		var $id = "";
		var $page = array();
		var $level = array();
		
		
		//*************************************************************************************************************************************
		/**
		* This functin serves as a __construct() for the content class. It uses the segment determined in the ZLCMS class and then finds
		* the correct page data to display and use within the other content class functions.
		*/
		//*************************************************************************************************************************************
		function class_construct() {
			
			//IMPORT GLOBAL ZLCMS
			global $controller;
			
			//REDIRECT TO SECURE IF NECESSARY
			$this->secure_redirect();
			
			//LOAD THE CORRECT ID
			$this->set_page();
			
			//BUILD AN ARRAY OF THE LEVELS CURRENTLY IN USE
			$this->build_level($this->id);
			
		}
		
		//*************************************************************************************************************************************
		/**
		* This function redirects the user to a secure version of the page if necessary
		*/
		//*************************************************************************************************************************************
		function secure_redirect() {
			global $controller;
			
			if(in_array($controller->segments['0'],$controller->connect['secure_pages']) && ($controller->connect['secure_pages'][0] != '')) {
				if($_SERVER['SERVER_PORT'] != 443) {
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
					exit();
				}
			} else {
				if($_SERVER['SERVER_PORT'] == 443) {
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
					exit();
				}
			}
		}
				
		//*************************************************************************************************************************************
		/**
		* This function loads with every page. It triggers a number of functions that determine the current page, plugin, and configuration
		* This function also initiates a database connection and loads a number of sub classes from the configuration file.
		* @returns $page string Indicates which template to load for the current page
		*/
		//*************************************************************************************************************************************
		function set_page() {
				
			//IMPORT THE GLOBAL ZLCMS OBJECT
			global $controller;
				
			//CLEAR OUTPUT FOR USE
			$output = "";	
					
			//CONTENT
			$content = true;

			//FIRST LETS SET THE ID FOR THE CURRENT PAGE IF IT DOESN'T EXIST CHOOSE THE HOMEPAGE
			if(!empty($controller->segments['0'])) {
			
				//QUERY THE DATABASE TO MAKE SURE THE PAGE EXISTS
				$sql = "SELECT * FROM pages WHERE permalink=?";
				$query = $controller->database->query($sql, array($controller->segments['0']));
				$number = $query->num_rows();
				
				//IF IT DOES THEN USE ITS ID IF NOT THEN SEND THIS USER TO THE HOMEPAGE
				if($number >= 1) {
					$result = $query->fetch_assoc();
					$this->page = $result;
					$this->id = $result['id'];
				} else {
					$this->id = $controller->connect['error_page']; 
					$content = false;
				}	
				
			//IF THERE IS NO ID SET THEN JUST JUMP STRIAGHT ON TO THE DEFAULT PAGE
			} else {
				$this->id = $controller->connect['default_page'];
				$content = false;
			}
			
			//CHECK TO SEE IF ID IS THE SAME AS PASSED IN THE URL IF SO THEN SET THE PAGE VALUES TO THE QUERY WE USED TO CHECK
			if($content == false) {
				
				//QUERY THE DATABASE TO MAKE SURE THE PAGE EXISTS
				$sql = "SELECT * FROM pages WHERE id=?";
				$query = $controller->database->query($sql, array($this->id));
				$number = $query->num_rows();
				
				if($number == 1) {
					$result = $query->fetch_assoc();
					$this->page = $result;
					$content = true;
				}
				
				//IF THIS IS EXECUTED AN ERROR OCCURED
				if($content == false) {
				
					//LOG ERROR
					$controller->log_error("Fatal Error: Could not select a page.");
					
					//STOP THE SCRIPT THIS IS A SECURITY RISK
					die;
				
				}
			}
			
		}
	
		//*************************************************************************************************************************************
		/**
		* build_level()
		* This function recursively builds an array of all pages in association with the current page and above it in the 
		* navigational hierarchy
		*
		* @param integer $input A Numerical value representing the page to be queried.
		* @uses array $this->level $ An array set using this function that contains the current page and the id of all pages before it in the navigational hierarchy
		* @uses boolean $complete Notifies the function if it should continue executing recursively.
		* @uses integer $level The current level we're working on. Level will continue counting through each possible level of the site ane executing the function until it ends.
		*/
		//*************************************************************************************************************************************
		function build_level($input,$level = 1) {
			
			//IMPORT GLOBAL ZLCMS
			global $controller;
			
			//SET COMPLETE TO FALSE
			$complete = false;
			
			//IF THIS IS THE FIRST LEVEL THEN SET THE ARRAY VALUES
			if($level == "1") {
		
				//LETS NOW SELECT THE MAXIMUM NAVIGATION LEVEL IN THE ZLCMS SYTEM 
				$sql = "SELECT max(tier) as tier FROM pages";
				$query = $controller->database->query($sql);
				$number = $query->num_rows;
				$result = $query->fetch_assoc();
				$lookups = $result['tier'];
				
				//NOW FOR EACH LEVEL WE WILL BUILD AN ARRAY VALUE USED FOR LEVELS
				for($counter = 1; $counter < $lookups; $counter++) {
					$this->level[$counter] = intval($input);
				}
			}

			//OK LETS QUERY AND FIND THE DIRECT PARENT OF THIS ITEM
			$sql = "SELECT id,tier,parent_id FROM pages WHERE id=?";
			$query = $controller->database->query($sql,array($input));
			$result = $query->fetch_assoc();
			
			//BUILD THE ID OF THIS LEVEL
			$this->level[$result['tier']] = intval($result['id']);

			//CHECK TO SEE IF THIS LEVEL HAS SUBNAV IF SO THEN RUN THIS BAD BOY AGAIN			
			if($result['parent_id'] != 0) {
				$nextLevel = $level + 1;
				$this->build_level($result['parent_id'],$nextLevel);
			} else {
				$complete = true;
			}
			
			//IF WE ARE COMPLETE THEN OUTPUT THE RESULTS
			if($complete == true) {
				$this->level;
			}
		}
	
		//*************************************************************************************************************************************
		/**
		* This function generates a meta titile tag from the content intput into the system.
		*/
		//*************************************************************************************************************************************
		function meta_title($company) {

			//IF A VALUE IS SET FOR META TITLE USE IT
			if(!empty($this->page['meta_title'])) {
				$output = $this->page['meta_title'];
			} else {

				//CHECK TO SEE IF WE HAVE A TITLE SET IF NOT USE DEFAULT
				if(!empty($this->page['title'])) {

					//IMPORT GLOBAL ZLCMS
					global $controller;

					//SET OUTPUT
					$output = $company;

					//LOOP LEVEL AND BUILD TITLE
					foreach($this->level as $id) {

						//IF THE PAGE IS NOT THE SAME AS THE CURRENT ID THEN SHOW THE TITLE			
						$sql = "SELECT title FROM pages WHERE id=?";
						$query = $controller->database->query($sql,array($id));
						$result = $query->fetch_assoc();
						$output .= " > " . $result['title'];

						//IF THE CURRENT PAGE THEN BREAK
						if($this->id == $id) {
							break;
						}

					}

				} else {
					$output = "";
				}
			}


			//OUTPUT THE TITLE
			if(!empty($output)) { 
				echo($output);
			}

		}

		//*************************************************************************************************************************************
		/**
		* This function generates meta tags and outputs them to the template
		* @uses $this->page array Array containing the various values contained in the database for the current page.
		*/
		//*************************************************************************************************************************************
		function meta_tags() {
		
			//CLEAR OUTPUT FOR USE
			$output = "";
		
			//IF PRSENT SHOW DESCRIPTION TAG WITH META DESCRIPTION
			if(!empty($this->page['meta_description'])) {
				$output .= "<meta name=\"description\" content=\"{$this->page['meta_description']}\" />\n";
			}
			
			//IF PRESENT SHOW META KEYWORDS TAG WITH KEYWORDS
			if(!empty($this->page['meta_keywords'])) {
				$output .= "<meta name=\"keywords\" content=\"{$this->page['meta_keywords']}\" />\n";
			}
			
			//SEND OUTPUT TO BROWSERS
			if(!empty($output)) {
				echo($output);
			}
			
		}

		//*************************************************************************************************************************************
		/**
		* This function selects and includes the right template for use on the current page. The tempaltes are located
		* in system/teampltes/pages/.  The select template function uses an array with $key=>$value pairs where $key is the 
		* id of the page to use the custom template on and value is the name of hte template located in /system/tempaltes/pages/.
		* The value name would be the text name such as sample in a template called sample.template.php and an array to show this
		* page id 36 would look like this $this->select_template(array(36,"sample"));. If this array is not passed a simple index/interior
		* tempalte style will be used by default.
		*
		* @param $custom array Array of page ids and corresponding template names.
		* @param $customsection array Array of section headings and corresponding template names 
		*/
		//*************************************************************************************************************************************
		function select_template($page_views = "") {
		
			//GLOBAL ZLCMS
			global $controller;
		
			//SET REGULAR A TRUE
			$regular = true;
		
			//NOW LOOK FOR A PAGE SPECIFIC TEMPLATE. IF ONE EXISTS SET IT (OVERRIDES SECTION TEMPLATE)
			if(is_array($page_views)) {
				foreach($page_views as $key=>$value) {
					if($this->id == $key) {
						if(is_file("{$controller->views_path_relative}/{$value}.view.php")) {
							$template = "{$controller->views_path_relative}/{$value}.view.php";
							$regular = false; //IF WE MAKE IT HERE THIS IS A CUSTOM PAGE SO SHOW IT AND SKIP REGULAR TEMPLATES
						}
					}
				}
			}
		
			//IF THIS IS A REGULAR PAGE GO DO YOUR BUSIENSS OTHERWISE INCLUDE THE CUSTOM TEMPLATE WE FOUND BEFORE
			if($regular == true) {
				if($this->id == $controller->connect['default_page']) {
					include("{$controller->views_path_relative}/index.view.php");
				} else {
					include("{$controller->views_path_relative}/interior.view.php");
				}
			} else {
				include($template);
			}	
		}

		//*************************************************************************************************************************************
		/**
		* output_content();
		* Outputs the page content and includes the script_url if there is one present. Any specialized functions like plugins
		* or contact forms will display using the script_url. If there aren't any for this page it will only show content.
		*
		* @param $custom array Array of page ids and corresponding tempalte names. 
		*/
		//*************************************************************************************************************************************
		function output_content() {
			
			//IF VISIBLE SHOW 
			if($this->page['visible'] == "true") {
			
				//OUTPUT THE CONTENT TO THE 
				if(!empty($this->page['content'])) {
					echo($this->page['content']);
				}
				
				//INCLUDE THE SCRIPT URL
				if(!empty($this->page['script_url'])) {
					include($this->page['script_url']);
				}
			
			}
		}
		
		//*************************************************************************************************************************************
		/**
		* This class stips the HTML tags specified for display in the mobile site. (http://php.net/manual/en/function.strip-tags.php)
		* @param $str string A string that we are stripping HTML from.
		* @param $tags an array of tags to remove from the string.
		*/
		//*************************************************************************************************************************************
		function strip_only($str, $tags) {
			if(!is_array($tags)) {
				$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
				if(end($tags) == '') array_pop($tags);
			}
			foreach($tags as $tag) $str = preg_replace('#</?'.$tag.'[^>]*>#is', '', $str);
			return $str;
		}

		//*************************************************************************************************************************************
		/**
		* Outputs the page content and includes the script_url if there is one present. Any specialized functions like plugins
		* or contact forms will display using the script_url. If there aren't any for this page it will only show content.
		*
		* @param $custom array Array of page ids and corresponding tempalte names. 
		*/
		//*************************************************************************************************************************************
		function output_mobile_content() {
			
			//IF VISIBLE SHOW 
			if($this->page['visible'] == "true") {
			
				//OUTPUT THE CONTENT TO THE 
				if(empty($this->page['mobile_content'])) {
					if(!empty($this->page['content'])) {
						$content = $this->strip_only($this->page['content'],array("table","thead","tbody","tr","td","th"));
						echo($content);
					}
				} else {
					$content = $this->strip_only($this->page['mobile_content'],array("table","thead","tbody","tr","td","th"));
					echo($content);
				}
				
				//INCLUDE THE SCRIPT URL
				if($this->page['include_plugin_in_mobile'] == "true") {
					if(!empty($this->page['script_url'])) {
						include($this->page['script_url']);
					}
				}
			
			}
		}
		
		//********************************************************************************************************************
		/**
		* This function takes the action from $_REQUEST and processes that request.
		* @param string $action string used to indicate which section of processing code should be executed for the request.
		*/
		//*********************************************************************************************************************
		function build_mobile_navigation() {
			
			//IMPORT ZLCMS
			global $controller;
			
			//OPEN VARIABLE OUTPUT
			$output = "";
		
			//SELECT THE LIST OF NAVIGATION ITEMS FROM THE 
			$sql = "SELECT id,url,image_url,permalink,title,visible FROM pages WHERE parent_id = '0' AND pagetype = 'core' AND include_in_mobile='true' ORDER BY display";
			$query = $controller->database->query($sql);
			$number = $query->num_rows();

			if($number >= 1) {
				while($result = $query->fetch_assoc()) {
					$link = $controller->build_link($result['permalink'],$result['url']);
					$output .= '<a href="'. $link['address'] .'"><span>'. $result['title'] .'</span></a>';
				
				}
				
				echo($output);
				
			}
				
		}
		
		
		//********************************************************************************************************************
		/**
		* This function generates a back button for the mobile interface
		*/
		//*********************************************************************************************************************
		function mobile_back_button() {
			
			//IMPORT GLOBAL ZLCMS
			global $controller;

			if(!empty($controller->content->id)) {
				if($controller->content->page['parent_id'] == 0) {
					$output = '<a href="/"><span>Back to Home</span></a>';
				} else {
					$sql = "SELECT id,title,url,permalink FROM pages WHERE id=?";
					$query = $controller->database->query($sql,array($controller->content->page['parent_id']));
					$number = $query->num_rows();
					if($number >= 1) {
						$result = $query->fetch_assoc();
						$link = $controller->build_link($result['permalink'],$result['url']);
						$output = '<a href="'. $link['address'] .'"><span>Back to '. $result['title'] .'</span></a>';
					}
				}
				if(!empty($output)) {
					echo($output);
				}
			}
			
		}

		//********************************************************************************************************************
		/**
		* This function selects the subnav saves it to a variable and then shows a button if subnav exists.
		*/
		//*********************************************************************************************************************
		function mobile_subnav_button() {
			
			//IMPORT GLOBAL ZLCMS
			global $controller;
			
			//DO QUERY
			$sql = "SELECT id,url,permalink,title,visible FROM pages WHERE parent_id=? AND pagetype = 'core' AND include_in_mobile='true' ORDER BY display";
			$query = $controller->database->query($sql,array($controller->content->page['id']));
			$number = $query->num_rows();	
			
			if($number >= 1) {
				$output = '<a href="#subnav"><span>View a List of Sub Pages</span></a>';
				echo($output);
			}	
		}
		

		//********************************************************************************************************************
		/**
		* Show a list of the mobile subnavigation
		*/
		//*********************************************************************************************************************
		function show_mobile_subnav() {
		
			//IMPORT GLOBAL ZLCMS
			global $controller;	
		
			//DO QUERY
			$sql = "SELECT id,url,permalink,title FROM pages WHERE parent_id=? AND pagetype = 'core' AND include_in_mobile='true' ORDER BY display";
			$query = $controller->database->query($sql,array($controller->content->page['id']));
			$number = $query->num_rows();	
			
			if($number >= 1) {
			
				$output = '<div class="mobile_buttons">';
					$output .= '<a name="subnav"></a>';
					while($result = $query->fetch_assoc()) {
						$link = $controller->build_link($result['permalink'],$result['url']);
						$output .= '<a href="' . $link['address'] . '" target="'. $link['target'] .'"><span>' . $result['title'] . '</span></a>';
					}
				$output .= '</div>';
				
				//SHOW ERROR
				echo($output);
				
			} 
		}


			
	}
		
?>