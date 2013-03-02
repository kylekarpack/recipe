"use strict";

$(window).load(function() {
	bindDel();
	getRecipes(true);
	$(".logout").bind("click", logout);
	$("#add").bind("click", add);
	$("#get").bind("click", function() {
		getRecipes(false);
	});

	
	//broken:
	// $("#add").bind("keyup", function(e) {
		// var code = (e.keyCode ? e.keyCode : e.which);
			// if (code == 13) { //Enter keycode
			// }
	// });
	
	// Autocomplete
	$(function() {
        function log( message ) {
            $( "#ingredient" ).val( message );
			add();
        }
 
        $( "#ingredient" ).autocomplete({
            source: "search.php",
            autoFocus: true,
			select: function( event, ui ) {
				log( ui.item.label ?
					ui.item.label :
                    this.item.label + "(WARNING: not in database)");
				event.preventDefault(); // THIS WAS A NIGHTMARE!
            }
        });
    });
});

//DEPRECATED CODE: IMPLEMENTED SERVER SIDE
//on loading the page, gets the stored list off the server via ajax
// function getIngredients() {
	// $.ajax({
		// url: "api.php",
		// async: false,
		// type: "GET",
		// success: function(data) {
			// return data;
		// }
	// });
// }

//add a single item that is passed in to the page. DEFAULT: add what is in the input
function add(thingToAdd) {
	var ing;
	thingToAdd === undefined ? ing = cap($("#ingredient").val()) : ing = cap(thingToAdd); 
	//The following line should not need to use returnCurrentIngredients(), it should query the database!!!))
	if (ing.length > 0 && ($.inArray(ing, returnCurrentIngredients())) === -1)  { //if it isn't an empty value && isn't already in the list
		$("#ingredient").val("");
		$.get("api.php", //this is probably slow, but I didn't want it to depend on the text
				{ getid: ing },
				function(data) {
					var li = $("<li>").addClass("n" + data);
					li.html("<span>" + ing + "</span>").prepend("<img class='close' src='./assets/close.png'>"); //make the innerHTML of the li be whatever ingredient is in ingredient
					$("#list").prepend(li.hide().fadeIn(400)); //add it to the list
					bindDel(); // ineffecient
					store(ing);
				}
		);
	} else { // ingredient is already in the list
		$(".fail").fadeIn(500).text("That ingredient is aready in the list!").delay(500).fadeOut(1000);
		$("#ingredient").val("");
	}
}

function getRecipes(first) {
	$(".no, .right h2").fadeOut(500);
	$(".right .load").fadeIn(500);
	$(".left li").each(function() { $(this).removeClass("sel"); });
	$("#recipes > li").animate({"opacity":"0"},500);
	$("#recipes > li").remove();
	$.get("api.php",
		{ recipes: "true" },
			function(data) {
				$(".no").text(data);
				if (data != "No recipe matches found. Try adding more ingredients.") { //should handle this error better
					var response = $.parseJSON(data);
					for (var i = 0; i < response.length; i++) { // For each recipe in the response JSON
						var recipeData = response[i];
						var recipe = $("<li>").addClass("recipe");
						var img = $("<img>").attr("src", recipeData.image);
						var titleText = $("<h1>").text(recipeData.title);
						var title = $("<a>").attr("href", recipeData.url).append(titleText);
						var close = $("<span>").addClass("close").text("X").bind("click", delRec);
						var ing = $("<ul>");
						for (var j = 0; j < recipeData.ingredients.length; j++) {
							ing.append($("<li>").text(cap(recipeData.ingredients[j])));
						}
						recipe.append(img).append(title).append(ing);
						recipe.append(close);
						$("#recipes").append(recipe);
						$("#recipes > li").css({"opacity":"1"})
						//Change button text
						if (!first) {
							$("#get").text("Update List");
							$("#recipes > li").fadeIn();
						}
						$(".right .load").fadeOut(300);
						$(".right > span").html("<h2>" + response.length + " recipe" + ((response.length === 1) ? "" : "s") + " found:</h2>");
						select();
					}
					
					//after they've loaded, do this stuff:
					var columns = 3,
					setColumns = function() { columns = $(window).width() > 1000 ? 3 : $(window).width() > 420 ? 2 : 1; };
					setColumns();
					$( window ).resize( setColumns );
					
					var container = $('#recipes');
					if (first) {
						$(function(){
							container.delay(500 , function(){  
								container.imagesLoaded(function() {
									container.masonry({
										itemSelector: '.recipe',
										columnWidth:function( containerWidth ) { return containerWidth / columns; }
									});
								});
							});
						});
					} else {
						container.imagesLoaded(function(){  
							container.masonry("reload");
						});
					}
					
					$(".right h1").fitText(1.3);
				} else { //no recipes returned
					$(".no").fadeIn(1200);
				}
			}
	);
}

function highlight() {
	var ings = [];
	$("li.ui-selected ul li").each(function() { ings.push($(this).text()) } );
	var leftIng = $(".left li");
	for (var i = 0; i < leftIng.length; i++) {
		$(leftIng[i]).removeClass("sel");
		if ($.inArray($(leftIng[i]).text(), ings) != -1) {
			$(leftIng[i]).addClass("sel");
		}
	}
}

function select() {
	$( "#recipes" ).selectable({
								selected: highlight,
								cancel: 'a, .close'
								});
	$( "#recipes" ).disableSelection();
}

function store(ing) {
	$.get("api.php", 
		{ add: ing }
	);
}

function bindDel() {
	$(".close").bind("click", del);
}

function delRec() {
	$(this).parent().css("background","#CC7F7F").fadeOut(400, function() { $(this).remove(); $('#recipes').masonry("reload"); });
	$(".right > span").html("<h2>" + ($("#recipes > li").length - 1) + " recipe" + ((($("#recipes > li").length - 1) === 1) ? "" : "s") + " found:</h2>");
}

function del() {
	var toDel = $(this).parent().attr("class").replace("n",""); //get the ingredient id of the ingredient to be deleted
	// remove it from the database
	var hold = $(this).parent();
	$.get("api.php", 
		{ del: toDel },
		// remove it from the DOM on completion
		function() {
			hold.fadeOut(200, function() {
				$(this).remove(); 
			});
		}
	);
}

// UTILITY FUNCTIONS
// for UI, capitalizes the first letter
function cap(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function logout() {
	window.location.href = "logout.php";
}

// Returns the array of ingredients currently on the page (should probably hit up the database eventually)
function returnCurrentIngredients() {
	var list = [];
	$("#list li").each(function() { list.push($(this).text()) });
	return list;
}