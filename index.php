<?php session_start();
if (isset($_SESSION["name"]) ) { # User is logged in ?> 
	<?php include("common.php"); ?>
	<div class="left">
		<h2><?php echo( $_SESSION["name"] ) ?>'s Ingredients:</h2>
		<input type="text" name="ingredient" id="ingredient" />
		<button type="button" id="add" >Add Ingredient</button>
		
		<div class="fail"></div>
		
		<ul id="list">
			<?php populate() ?>
			<!-- JS populates here -->
		</ul>
		
	</div>
	<div class="right">
		<button type="button" id="get">Find Recipes Now!</button><span></span>
		
		<div class="clear"></div>
		<p class="no" />
		<img class="load" src="assets/load.gif" />
		<ul id="recipes" class="masonry clearfix">
		
		</ul>
		<div class="clear"></div>

	</div>
	<div class="clear"></div>
</div>

<?php } else { # User is not logged in
	header("Location: loginpage.php");
} ?>

<?php include("footer.html");

// FUNCTIONS
// populate the list with ingredients from server
function populate() {
	$name = $_SESSION["name"];
	$query = mysql_query("SELECT i.ingredient, i.ingredient_id FROM ingredient i JOIN users_ingredients ui ON ui.ingredient_id = i.ingredient_id JOIN users u ON u.user_id = ui.user_id WHERE u.username = '$name'");
	$ingredients = array();
	$ids = array();
	for ($x = 0, $numrows = mysql_num_rows($query); $x < $numrows; $x++) {
		$row = mysql_fetch_assoc($query);  
		$ingredients[$x] = array(ucfirst($row["ingredient"]), $row["ingredient_id"]); 
	}
	foreach ($ingredients as $ingredient) { ?>
		<li class="n<?php echo($ingredient[1]) ?>"><img class="close" src="./assets/close.png"><span><?php echo($ingredient[0]) ?></span></li>
	<?php 
	}
}
?>