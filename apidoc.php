<?php //API DOCUMENTATION
include("common.php");
?>
<h3>Why Document?</h3>
<p>Research done by the HCDE department at UW continually shows that poor documentation will result in deserted APIs, even if they are useful or innovative. I am documenting so that you can use my API outside of my program.</p>
&nbsp;
<h3>A Word of Warning...</h3>
<p>This API has <i>not</i> been extensively tested! It is built on fairly fragile infrastructure. It will probably break at some point!</p>
<div id="doc">
	<h1>API Documentation</h1>
	<p>A Recipes API request is an HTTP GET request with a URL of the following form:
	<p class="req">http://students.washington.edu/kkarpack/recipe/api.php?{request type}={parameters}</p>
	<p>If contacted with no parameters, it will yield you a list of ingredients for the current user</p>
	<p>If contacted while logged out, your request will be rejected</p>
	<p>If contacted with a POST request, your request will be rejected.<p>
	<p>If contacted with multiple concurrent request types, your request will be rejected</p>
	<h2>Delete</h2>
	<h3>Call:</h3>
	<p>A delete request comes in the following form:</p>
	<p class="req">http://students.washington.edu/kkarpack/recipe/api.php?del={ingredient id #}</p>
	<h3>Response:</h3>
	<p>No response necessary, but will return success text in the form of "<span class="mono">Ingredient number {#} deleted from {username}'s ingredients</span>"</p>
	<h2>Add</h2>
	<h3>Call:</h3>
	<p>An add request comes in the following form:</p>
	<p class="req">http://students.washington.edu/kkarpack/recipe/api.php?add={ingredient name}</p>
	<p>Note: this method takes an ingredient name, not an id number as does delete</p>
	<h3>Response:</h3>
	<p>No response necessary, but will return success text in the form of "<span class="mono">{ingredient} added to {username}'s ingredients</span>"</p>
	<h2>Get Recipes</h2>
	<h3>Call:</h3>
	<p>A get recipes request comes in the following form:</p>
	<p class="req">http://students.washington.edu/kkarpack/recipe/api.php?recipes=true</p>
	<h3>Response:</h3>
	<p>A JSON list of recipe objects, formatted as such:</p>
	<pre>
{
	"recipe_id": "id",
	"title": "title",
	"url": "recipe url",
	"image": "image url",
	"rating": "rating",
	"ingredients": [
		"ingredient1",
		"ingredient2",
		...
	]
}
	</pre>
	<h2>Get Ingredient ID</h2>
	<h3>Call:</h3>
	<p>A get ingredient ID request comes in the following form:</p>
	<p class="req">http://students.washington.edu/kkarpack/recipe/api.php?getid={ingredient name}</p>
	<h3>Response:</h3>
	<p>A recipe identification number as an integer</p>
</div>
<?php
include("footer.html");
?>