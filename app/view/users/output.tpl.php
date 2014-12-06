<?php 
		if (isset($title)) {
				echo "<h4>" . $title . "</h4>";
			//	unset($title);
		} if (isset($feedback)) {
				echo "<h5>" . $feedback . "</h5>";
			//	unset($feedback);
		}  if (isset($_SESSION['form-output'])) {
        		$output = $_SESSION['form-output'];
				echo "<h5>" . $output . "</h5>";        		
        		unset($_SESSION['form-output']);
      } 
?>