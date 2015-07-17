  jQuery("html").click(function(event) {
    /* Act on the event */

	/* no action here */
  });
  jQuery(".searcharea") .click(function(event) {
    /* Act on the event */
   jQuery("#searchbox").fadeToggle(300,"easeInQuart")
   jQuery("html, body").animate({ scrollTop: jQuery('.searcharea').offset().top }, 1000);
  });