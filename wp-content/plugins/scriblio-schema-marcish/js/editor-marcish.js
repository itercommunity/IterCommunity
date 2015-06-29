// this function runs when the meditor form is displayed
function scrib_meditor_marcish() {
	jQuery("div.wrap h2:first").text("Record Details");

	jQuery("#bsuite_machinetags").hide();
	jQuery("#categorydiv").hide();
	jQuery("#trackbacksdiv").hide();
	jQuery("#tagsdiv").hide();
	jQuery("#postcustom").hide();
	jQuery("#passworddiv").hide();
	jQuery("#postexcerpt").hide();
	jQuery("#postdivrich").hide();

	jQuery("#titlediv #title").css({ marginLeft:"5px", width:"98%" });
	jQuery("#titlediv").addClass("postbox");
	jQuery("#titlediv #titlewrap").before('<h3 class="hndle"><label for="title">Primary Title</label></h3>');

	jQuery("#scrib_meditor_div h3").text("Details");
	jQuery("#scrib_meditor_div").removeClass('closed');
	jQuery("#scrib_meditor_div").insertAfter("#titlediv");

	jQuery("#bsuite_post_icon h3").text("Image");
	jQuery("#bsuite_post_icon").removeClass('closed');
	jQuery("#bsuite_post_icon").insertAfter('#titlediv');
}
