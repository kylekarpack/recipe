<?php
	if (!isset($_SESSION["name"])) {
		session_start();
	}

	//this function is to perform redirects based on whether the user is logged in
	//takes a redirect header location and a true/false value to determine logged in or not
	function check($dir, $flag) {
		if (isset($_SESSION["name"]) == $flag) {
			header($dir);	
		}
	}
	
	// credential masking for version control
	function get($param) {
		$file = file("config.config");
		if ($param == "host") { return trim($file[0]);
		} elseif ($param == "username") { return trim($file[1]);
		} elseif ($param == "password") { return trim($file[2]);
		} else { return false; }
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5 Transitional//EN" "http://www.w3.org/TR/html5/loose.dtd">

<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html" charset="utf-8">
	<title>Reverse Recipe Search</title>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />
	<link rel="stylesheet" href="style.css">
	<link rel="shortcut icon" href="./assets/favicon.png">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/jquery-ui.min.js"></script>
	<script src="js.js"></script>
	<script src="jquery.fittext.js"></script>
	<script src="jquery.masonry.min.js"></script>
</head>

<body>
	<div id="menu">
		<a href="index.php"><img src="logo.png" class="logo" alt="Revercipe"></a>
		<?php if (isset($_SESSION["name"])) { ?>
			<button class="logout">Log Out</button>
		<?php } ?>
		<div id="stats">	
		<?php  
			$connection = mysql_select_db("recipe", mysql_connect(get("host"), get("username"), get("password")));
			$recipeCount = number_format(mysql_num_rows(mysql_query("SELECT recipe_id FROM recipe")));
			$ingredientCount = number_format(mysql_num_rows(mysql_query("SELECT ingredient_id FROM ingredient")));
		?>
		<p>Recipes in Database: <b><?php echo( $recipeCount ) ?></b></p>
		<p>Ingredients in Database: <b><?php echo( $ingredientCount ) ?></b><p>
		</div>
		
	</div>
	<div id="wrapper">