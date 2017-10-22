<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);


	//instantiate the program object

	//Class to load classes it finds the file when the progrm starts to fail for calling a missing class
	class Manage {
    		public static function autoload($class) {
            	//you can put any file name or directory here
	        	include $class . '.php';
		}
	}

	spl_autoload_register(array('Manage', 'autoload'));

	//instantiate the program object
	$obj = new main();

	class main {
		public function __construct() {
		//print_r($_REQUEST);
		//set default page request when no parameters are in URL
			$pageRequest = 'homepage';
			//check if there are parameters
			if(isset($_REQUEST['page'])) {
			//load the type of page the request wants into page request
				$pageRequest = $_REQUEST['page'];
			}
			
			//instantiate the class that is being requested
			$page = new $pageRequest;

			if($_SERVER['REQUEST_METHOD'] =='GET') {
				$page->get();
			} else {
				$page->post();
			}
		}
	}
	abstract class page {
		protected $html;
		
		public function  __construct() {
			$this->html.='<html>';
			$this->html.='<body>';
		}
																							public function __destruct() {
			$this->html.='</body></html>';
			stringFunctions::printThis($this->html);
		}

		public function get() {
			echo 'default get message';
		}

		public function post() {
			print_r($_POST);
		}
																						}
	class homepage extends page {

		public function get() {
			$form = '<form method="post" enctype="multipart/form-data">';
			$form .= '<input type="file" name="fileToUpload" id="fileToUpload"> </br> </br>';
			$form .= '<input type="submit" value="Upload" name="submit"> ';
			$form .= '</form> ';
			$this->html .= '<h1>Upload File</h1>';
			$this->html .= $form;
		}
		public function post() {
			$target_dir = "Uploads/";
			$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$uploadOk = 1;
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			$imageFileName = pathinfo($target_file,PATHINFO_BASENAME);
			move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
			
			header("Location: https://web.njit.edu/~sp2363/uploadCSV/index.php?page=table&filename=".$_FILES["fileToUpload"]["name"]);
			}

	}
	
	class htmlTable extends page {
	}
		
	class stringFunctions {
		static public function printThis($inputText) {
			return print($inputText);
		}
			
		static public function stringLength($text) {
			return strLen($text);
		}	
	}
	class table extends page {
		public function get() {
			$firstRow = true;
			$this->html .= '<table border=1>';
			$name= "Uploads/".$_REQUEST['filename'];
			$f = fopen($name,"r");
			while (($line = fgetcsv($f)) !== false) {
				$this->html .= '<tr>';
				if($firstRow) {
					foreach ($line as $cell) {
						$this->html .='<th>' . htmlspecialchars($cell). '</th>';
					}
				$firstRow=false;
				}
				else {
				foreach($line as $cell) {
					$this->html.='<td>'.htmlspecialchars($cell).'</td>';
					}
				}
				
			}
			fclose($f);
				$this->html.='</table>';
		}
	}
?>



