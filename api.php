<?php

session_start(); //disallow access for users not logged in

if (!(isset($_SESSION["name"]))) {
	die("You must be logged in to use the functionality of this API!");
}

// ideally should throw this in a functions file
function get($param) {
	$file = file("config.config");
	if ($param == "host") { return trim($file[0]);
	} elseif ($param == "username") { return trim($file[1]);
	} elseif ($param == "password") { return trim($file[2]);
	} else { return false; }
}

//reject post requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	header("HTTP/1.1 400 Invalid Request");
	die("HTTP/1.1 400 Invalid Request - This API is not yet accepting POST requests");

// START API
} else { //if ($_SERVER["REQUEST_METHOD"] == "GET") {
	//initialize stuff
	$name = $_SESSION["name"];
	$connection = mysql_select_db("recipe", mysql_connect(get("host"), get("username"), get("password")));
	$userid = mysql_fetch_row(mysql_query("SELECT user_id FROM users WHERE username = '$name'"));
	$userid = $userid[0];
	
	// Array to capture the presence of parameters
	$params = array(
		"del" => isset($_GET["del"]),
		"add" => isset($_GET["add"]),
		"recipes" => isset($_GET["recipes"]),
		"getid" => isset($_GET["getid"]),
	);	
	if (array_sum($params) > 1) { // disallow multiple parameters submitted
		die ("Sorry, this API doesn't like getting multiple parameters! Please make your request with only one parameer");
	} else {
	//start branching logic
		if ($params["del"]) { //it's a delete request
			$ingredient_id = $_GET["del"];
			// deprecated:
			//$ingredient_id = mysql_fetch_row(mysql_query("SELECT ingredient_id FROM ingredient WHERE ingredient = '$toDel'"));
			//$ingredient_id = $ingredient_id[0];
			$query = mysql_query("DELETE FROM users_ingredients WHERE user_id = '$userid' AND ingredient_id = '$ingredient_id'");
			echo("Ingredient number " . $ingredient_id . " deleted from " . $name . "'s ingredients");
		} elseif ($params["recipes"]) { //it's a get recipes request!! echo proper JSON
			$matches = mysql_query("SELECT DISTINCT ri.recipe_id FROM recipes_ingredients ri
									WHERE NOT EXISTS (
										SELECT ri2.ingredient_id FROM recipes_ingredients ri2
										WHERE ri.recipe_id = ri2.recipe_id AND ri2.ingredient_id NOT IN (
											SELECT ingredient_id FROM users_ingredients
											WHERE user_id = '$userid')
										)"
								);
			$recipe_ids = array();
			for ($x = 0, $numrows = mysql_num_rows($matches); $x < $numrows; $x++) {
				$row = mysql_fetch_assoc($matches);  
				array_push($recipe_ids, $row["recipe_id"]);  
			}
			if (count($recipe_ids) == 0) { //if it returns an empty set
				die("No recipe matches found. Try adding more ingredients.");
			} else { //write JSON
				$array = array();
				$count = 0;
				foreach ($recipe_ids as $id) { //for each recipe
					$ing = array();
					$recipe_ingredients = mysql_query("SELECT DISTINCT i.ingredient FROM ingredient i 
														JOIN recipes_ingredients ri ON ri.ingredient_id = i.ingredient_id
														WHERE ri.recipe_id = '$id'"); //get the ingredients
					for ($x = 0, $numrows = mysql_num_rows($recipe_ingredients); $x < $numrows; $x++) {
						$var = mysql_fetch_assoc($recipe_ingredients);
						array_push($ing, $var["ingredient"]);
					}
					//var_dump( $ing );

					$recipe_info = mysql_query("SELECT * FROM recipe WHERE recipe_id = '$id'");
					for ($x = 0; $x < mysql_num_rows($recipe_info); $x++) {
						$row = mysql_fetch_assoc($recipe_info);  
						array_push($array, $row);
					}
					$array[$count]["ingredients"] = $ing;
					$count++; //yipes this was tough
				}
				echo json_encode($array);
			}
		
		} elseif ($params["getid"]) {
			$toGet = $_GET["getid"];
			$ingredient_id = mysql_fetch_row(mysql_query("SELECT ingredient_id FROM ingredient WHERE ingredient = '$toGet'"));
			$ingredient_id = $ingredient_id[0];
			echo $ingredient_id;
		
		//it's an add ingredient request
		} elseif ($params["add"]) {
			$toAdd = $_GET["add"];
			$ingredient_id = mysql_fetch_row(mysql_query("SELECT ingredient_id FROM ingredient WHERE ingredient = '$toAdd'"));
			$ingredient_id = $ingredient_id[0];
			$query = mysql_query("INSERT IGNORE INTO users_ingredients VALUES('$userid','$ingredient_id')");
			if ($ingredient_id != "") { // ingredient not in database
				echo("'" . $toAdd . "' added to " . $name . "'s ingredients");
			} else {
				echo ("Ingredient not found");
			}
		}
		// If the API is queried with no parameters, 
		else { //it's a get all ingredients request (array_sum($params) == 0)
			if (!(isset($_SERVER['HTTP_REFERER']))) { //if it's a direct request, print a nice header
				echo ("Here are your ingredients: \n\n");
			}
			$query = mysql_query("SELECT i.ingredient FROM ingredient i JOIN users_ingredients ui ON ui.ingredient_id = i.ingredient_id JOIN users u ON u.user_id = ui.user_id WHERE u.username = '$name'");
			$ingredients = array();
			for ($x = 0, $numrows = mysql_num_rows($query); $x < $numrows; $x++) {
				$row = mysql_fetch_assoc($query);  
				array_push($ingredients, $row["ingredient"]);  
			}
			$response = json_encode($ingredients);
			echo $response;
		}
	}
}
?>