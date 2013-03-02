<?php

	// credential masking for version control
function get($param) {
	$file = file("config.config");
	if ($param == "host") { return trim($file[0]);
	} elseif ($param == "username") { return trim($file[1]);
	} elseif ($param == "password") { return trim($file[2]);
	} else { return false; }
}

# A simple php script to query the database and return matching ingredients to the JavaScript as a JSON file
    //connection information  
    $host = get("host");  
    $user = get("username");  
    $password = get("password");  
    $database = "recipe"; 
	if (isset($_GET["term"])) {
		$param = $_GET["term"]; 
	} else {
		die("You must make a 'term=' query to use this service");
	}
    //make connection  
    $server = mysql_connect($host, $user, $password);  
    $connection = mysql_select_db($database, $server);  
    //query the database. Yield top ten most common ingredients matching query 
    $query = mysql_query("SELECT i.ingredient_id, count(*), i.ingredient FROM recipes_ingredients ri JOIN ingredient i on i.ingredient_id = ri.ingredient_id WHERE ingredient REGEXP '^$param' GROUP BY ingredient_id ORDER BY count(*) DESC LIMIT 10");
    //build array of results  
    for ($x = 0, $numrows = mysql_num_rows($query); $x < $numrows; $x++) {
        $row = mysql_fetch_assoc($query);  
        $ingredients[$x] = $row["ingredient"];  
    }
    //echo JSON to page
	if (isset($ingredients)) {
		$response = json_encode($ingredients); 
		echo $response;
	} else {
		die("Not in database");
	}
    mysql_close($server);  
?> 