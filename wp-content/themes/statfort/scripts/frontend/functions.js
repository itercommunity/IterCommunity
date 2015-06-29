/*
 SelectNav.js (v. 0.1)
 Converts your <ul>/<ol> navigation into a dropdown list for small screens
 https://github.com/lukaszfiszer/selectnav.js
*/
window.selectnav=function(){return function(a,b){var c,d=function(a){var b;a||(a=window.event),a.target?b=a.target:a.srcElement&&(b=a.srcElement),3===b.nodeType&&(b=b.parentNode),b.value&&(window.location.href=b.value)},e=function(a){return a=a.nodeName.toLowerCase(),"ul"===a||"ol"===a},f=function(a){for(var b=1;document.getElementById("selectnav"+b);b++);return a?"selectnav"+b:"selectnav"+(b-1)},g=function(a){n++;var b=a.children.length,c="",d="",h=n-1;if(b){if(h){for(;h--;)d+=l;d+=" "}for(h=0;b>h;h++){var p=a.children[h].children[0];if("undefined"!=typeof p){var q=p.innerText||p.textContent,r="";i&&(r=-1!==p.className.search(i)||-1!==p.parentElement.className.search(i)?o:""),j&&!r&&(r=p.href===document.URL?o:""),c+='<option value="'+p.href+'" '+r+">"+d+q+"</option>",k&&(p=a.children[h].children[1])&&e(p)&&(c+=g(p))}}return 1===n&&m&&(c='<option value="">'+m+"</option>"+c),1===n&&(c='<select class="selectnav" id="'+f(!0)+'">'+c+"</select>"),n--,c}};if((c=document.getElementById(a))&&e(c)){document.documentElement.className+=" js";var h=b||{},i=h.activeclass||"active",j="boolean"==typeof h.autoselect?h.autoselect:!0,k="boolean"==typeof h.nested?h.nested:!0,l=h.indent||"\u2192",m=h.label||"- Navigation -",n=0,o=" selected ";c.insertAdjacentHTML("afterend",g(c)),c=document.getElementById(f()),c.addEventListener&&c.addEventListener("change",d),c.attachEvent&&c.attachEvent("onchange",d)}}}();
//Tooltip Plugin
!function(a){jQuery.fn.pixTip=function(b){var c={leftit:0,topit:0,dt:"data-pix-tooltip",dg:"data-pix-caption",sticky:1};return b&&a.extend(c,b),this.each(function(){a(this).hover(function(){a("#toolwrapper").remove();var b=a(this),d=b.attr(c.dt),e=a("["+c.dg+"='"+d+"']"),f=b.offset().top,g=b.offset().left,h=e.outerHeight(),i=e.outerWidth(),j=a(window).width()-(g+h);a("body").append("<div id='toolwrapper'></div>"),0>=j?(e.addClass("rightelement"),a("#toolwrapper").css({position:"absolute",left:g+c.leftit,top:f+c.topit,"z-index":999,"margin-top":-h,"margin-left":-i/2}).hide().fadeIn(300)):a("#toolwrapper").css({position:"absolute",left:g+c.leftit,top:f+c.topit,"z-index":999,"margin-top":-h,"margin-left":-i/2}).hide().fadeIn(300),a(e).clone().appendTo("#toolwrapper")},function(){1==c.sticky&&a("#toolwrapper").hover(function(b){b.stopPropagation(),a("#toolwrapper").stop(!0,!0).fadeIn(300)},function(){a("#toolwrapper").delay(500).fadeOut(300)}),a("#toolwrapper").delay(500).fadeOut(300)})})}}(jQuery);
//Jquery Inview Funcion
!function(a){var c,d,b={},e=document,f=window,g=e.documentElement,h=a.expando;a.event.special.inview={add:function(c){b[c.guid+"-"+this[h]]={data:c,$element:a(this)}},remove:function(a){try{delete b[a.guid+"-"+this[h]]}catch(c){}}},a(f).bind("scroll resize",function(){c=d=null}),!g.addEventListener&&g.attachEvent&&g.attachEvent("onfocusin",function(){d=null}),setInterval(function(){var i,h=a(),j=0;if(a.each(b,function(a,b){var c=b.data.selector,d=b.$element;h=h.add(c?d.find(c):d)}),i=h.length){var k;if(!(k=c)){var l={height:f.innerHeight,width:f.innerWidth};l.height||!(k=e.compatMode)&&a.support.boxModel||(k="CSS1Compat"===k?g:e.body,l={height:k.clientHeight,width:k.clientWidth}),k=l}for(c=k,d=d||{top:f.pageYOffset||g.scrollTop||e.body.scrollTop,left:f.pageXOffset||g.scrollLeft||e.body.scrollLeft};i>j;j++)if(a.contains(g,h[j])){k=a(h[j]);var m=k.height(),n=k.width(),o=k.offset(),l=k.data("inview");if(!d||!c)break;o.top+m>d.top&&o.top<d.top+c.height&&o.left+n>d.left&&o.left<d.left+c.width?(n=d.left>o.left?"right":d.left+c.width<o.left+n?"left":"both",m=d.top>o.top?"bottom":d.top+c.height<o.top+m?"top":"both",o=n+"-"+m,(!l||l!==o)&&k.data("inview",o).trigger("inview",[!0,n,m])):l&&k.data("inview",!1).trigger("inview",[!1])}}},250)}(jQuery);


// JQuery Easing Plugin 1.3
jQuery.easing.jswing=jQuery.easing.swing,jQuery.extend(jQuery.easing,{def:"easeOutQuad",swing:function(a,b,c,d,e){return jQuery.easing[jQuery.easing.def](a,b,c,d,e)},easeInQuad:function(a,b,c,d,e){return d*(b/=e)*b+c},easeOutQuad:function(a,b,c,d,e){return-d*(b/=e)*(b-2)+c},easeInOutQuad:function(a,b,c,d,e){return(b/=e/2)<1?d/2*b*b+c:-d/2*(--b*(b-2)-1)+c},easeInCubic:function(a,b,c,d,e){return d*(b/=e)*b*b+c},easeOutCubic:function(a,b,c,d,e){return d*((b=b/e-1)*b*b+1)+c},easeInOutCubic:function(a,b,c,d,e){return(b/=e/2)<1?d/2*b*b*b+c:d/2*((b-=2)*b*b+2)+c},easeInQuart:function(a,b,c,d,e){return d*(b/=e)*b*b*b+c},easeOutQuart:function(a,b,c,d,e){return-d*((b=b/e-1)*b*b*b-1)+c},easeInOutQuart:function(a,b,c,d,e){return(b/=e/2)<1?d/2*b*b*b*b+c:-d/2*((b-=2)*b*b*b-2)+c},easeInQuint:function(a,b,c,d,e){return d*(b/=e)*b*b*b*b+c},easeOutQuint:function(a,b,c,d,e){return d*((b=b/e-1)*b*b*b*b+1)+c},easeInOutQuint:function(a,b,c,d,e){return(b/=e/2)<1?d/2*b*b*b*b*b+c:d/2*((b-=2)*b*b*b*b+2)+c},easeInSine:function(a,b,c,d,e){return-d*Math.cos(b/e*(Math.PI/2))+d+c},easeOutSine:function(a,b,c,d,e){return d*Math.sin(b/e*(Math.PI/2))+c},easeInOutSine:function(a,b,c,d,e){return-d/2*(Math.cos(Math.PI*b/e)-1)+c},easeInExpo:function(a,b,c,d,e){return 0==b?c:d*Math.pow(2,10*(b/e-1))+c},easeOutExpo:function(a,b,c,d,e){return b==e?c+d:d*(-Math.pow(2,-10*b/e)+1)+c},easeInOutExpo:function(a,b,c,d,e){return 0==b?c:b==e?c+d:(b/=e/2)<1?d/2*Math.pow(2,10*(b-1))+c:d/2*(-Math.pow(2,-10*--b)+2)+c},easeInCirc:function(a,b,c,d,e){return-d*(Math.sqrt(1-(b/=e)*b)-1)+c},easeOutCirc:function(a,b,c,d,e){return d*Math.sqrt(1-(b=b/e-1)*b)+c},easeInOutCirc:function(a,b,c,d,e){return(b/=e/2)<1?-d/2*(Math.sqrt(1-b*b)-1)+c:d/2*(Math.sqrt(1-(b-=2)*b)+1)+c},easeInElastic:function(a,b,c,d,e){var f=1.70158,g=0,h=d;if(0==b)return c;if(1==(b/=e))return c+d;if(g||(g=.3*e),h<Math.abs(d)){h=d;var f=g/4}else var f=g/(2*Math.PI)*Math.asin(d/h);return-(h*Math.pow(2,10*(b-=1))*Math.sin((b*e-f)*2*Math.PI/g))+c},easeOutElastic:function(a,b,c,d,e){var f=1.70158,g=0,h=d;if(0==b)return c;if(1==(b/=e))return c+d;if(g||(g=.3*e),h<Math.abs(d)){h=d;var f=g/4}else var f=g/(2*Math.PI)*Math.asin(d/h);return h*Math.pow(2,-10*b)*Math.sin((b*e-f)*2*Math.PI/g)+d+c},easeInOutElastic:function(a,b,c,d,e){var f=1.70158,g=0,h=d;if(0==b)return c;if(2==(b/=e/2))return c+d;if(g||(g=e*.3*1.5),h<Math.abs(d)){h=d;var f=g/4}else var f=g/(2*Math.PI)*Math.asin(d/h);return 1>b?-.5*h*Math.pow(2,10*(b-=1))*Math.sin((b*e-f)*2*Math.PI/g)+c:.5*h*Math.pow(2,-10*(b-=1))*Math.sin((b*e-f)*2*Math.PI/g)+d+c},easeInBack:function(a,b,c,d,e,f){return void 0==f&&(f=1.70158),d*(b/=e)*b*((f+1)*b-f)+c},easeOutBack:function(a,b,c,d,e,f){return void 0==f&&(f=1.70158),d*((b=b/e-1)*b*((f+1)*b+f)+1)+c},easeInOutBack:function(a,b,c,d,e,f){return void 0==f&&(f=1.70158),(b/=e/2)<1?d/2*b*b*(((f*=1.525)+1)*b-f)+c:d/2*((b-=2)*b*(((f*=1.525)+1)*b+f)+2)+c},easeInBounce:function(a,b,c,d,e){return d-jQuery.easing.easeOutBounce(a,e-b,0,d,e)+c},easeOutBounce:function(a,b,c,d,e){return(b/=e)<1/2.75?d*7.5625*b*b+c:2/2.75>b?d*(7.5625*(b-=1.5/2.75)*b+.75)+c:2.5/2.75>b?d*(7.5625*(b-=2.25/2.75)*b+.9375)+c:d*(7.5625*(b-=2.625/2.75)*b+.984375)+c},easeInOutBounce:function(a,b,c,d,e){return e/2>b?.5*jQuery.easing.easeInBounce(a,2*b,0,d,e)+c:.5*jQuery.easing.easeOutBounce(a,2*b-e,0,d,e)+.5*d+c}});


//Normal Call Back Functions
jQuery(document).ready(function($) {
jQuery('audio,video').mediaelementplayer();
selectnav('menus', {
    label: 'Menu',
    nested: true,
    indent: '-'
});

  jQuery('.back-to-top').click(function(event) {
    event.preventDefault();
    jQuery('html, body').animate({scrollTop: 0}, 2000);
    return false;
})
  jQuery(".blog-grid article").hover(function() {
    $(this).find("p").stop(true,true).slideDown(300)
  }, function() {
     $(this).find("p").stop(true,true).slideUp(300)
  });


/*
  // Foucs Blur function for input field 
  jQuery(' textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"]').focus(function() {
    if (!$(this).data("DefaultText")) $(this).data("DefaultText", $(this).val());
    if ($(this).val() != "" && $(this).val() == $(this).data("DefaultText")) $(this).val("");
  }).blur(function() {
    if ($(this).val() == "") $(this).val($(this).data("DefaultText"));
  });
    jQuery(".btnsearch").click(function(event) {
    /* Act on the event */
    jQuery(this).next().fadeToggle(600,"easeOutQuart")
    return false;
  });
  jQuery("html").click(function(event) {
    /* Act on the event */
   jQuery("#searchbox").fadeOut(600,"easeOutQuart")
  });
  jQuery(".searcharea") .click(function(event) {
    /* Act on the event */
    event.stopPropagation();
  });
    
  
    jQuery('.our_staff .flexslider').flexslider({
    animation: "slide",
    itemWidth: 221,
    itemMargin: 20,
    prevText:"<em class='fa fa-long-arrow-left'></em>",
    nextText:"<em class='fa fa-long-arrow-right'></em>",
    start: function(slider) {
    $('body').removeClass('loading');
    }
    }); 
    
*/
    // Window REsize Function

    // Window REsize Function End

  });
// team carousal
function cs_team_carousal(){
	"use strict";
	 jQuery('.our_staff .flexslider').flexslider({
		animation: "slide",
		itemWidth: 221,
		itemMargin: 20,
		prevText:"<em class='fa fa-long-arrow-left'></em>",
		nextText:"<em class='fa fa-long-arrow-right'></em>",
		start: function(slider) {
			jQuery('body').removeClass('loading');
		}
    }); 
	
}

function event_map(add,lat, long, zoom, counter){
	"use strict";
 	var map;
	var myLatLng = new google.maps.LatLng(lat,long)
	//Initialize MAP
	var myOptions = {
	  zoom:zoom,
	  center: myLatLng,
	  disableDefaultUI: true,
	  zoomControl: true,
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById('map_canvas'+counter),myOptions);
	//End Initialize MAP

	//Set Marker
	var marker = new google.maps.Marker({
	  position: map.getCenter(),
	  map: map
	});
	marker.getPosition();
	//End marker
	
	//Set info window
	var infowindow = new google.maps.InfoWindow({
		content: ""+add,
		position: myLatLng
	});
	google.maps.event.addListener(marker, 'click', function() {
		infowindow.open(map,marker);
	});
}

// Twitter js function
function cs_twitter_slider(){
	"use strict";
	jQuery('.widget_slider .flexslider').flexslider({
		animation: "slide",
		prevText:"<em class='fa fa-long-arrow-left'></em>",
		nextText:"<em class='fa fa-long-arrow-right'></em>",
		start: function(slider) {
		jQuery('.widget_slider').css("opacity",1);
		}
	});
}
function cs_menu_sticky(){
	"use strict";
	jQuery("#mainheader").scrollToFixed();
}
// Mailchimp widget 
function cs_mailchimp_add_scripts () {
	'use strict';
	(function(a){a.fn.ns_mc_widget=function(b){var e,c,d;e={url:"/",cookie_id:false,cookie_value:""};d=jQuery.extend(e,b);c=a(this);c.submit(function(){var f;f=jQuery("<div></div>");f.css({"background-image":"url("+d.loader_graphic+")","background-position":"center center","background-repeat":"no-repeat",height:"16px",left:"48%",position:"absolute",top:"40px",width:"16px","z-index":"100"});c.css({height:"100%",position:"relative",width:"100%"});c.children().hide();c.append(f);a.getJSON(d.url,c.serialize(),function(h,k){var j,g,i;if("success"===k){if(true===h.success){i=jQuery("<p>"+h.success_message+"</p>");i.hide();c.fadeTo(400,0,function(){c.html(i);i.show();c.fadeTo(400,1)});if(false!==d.cookie_id){j=new Date();j.setTime(j.getTime()+"3153600000");document.cookie=d.cookie_id+"="+d.cookie_value+"; expires="+j.toGMTString()+";"}}else{g=jQuery(".error",c);if(0===g.length){f.remove();c.children().show();g=jQuery('<div class="error"></div>');g.prependTo(c)}else{f.remove();c.children().show()}g.html(h.error)}}return false});return false})}}(jQuery));
}
// js fucntion for news ticker
function fn_jsnewsticker(cls,startDelay,tickerRate){
	'use strict';
	var options = {
		newsList: "."+cls,
		startDelay: startDelay,
		tickerRate: tickerRate,
		controls: true,
		ownControls: false,
		stopOnHover: false,
		resumeOffHover: true
	}
	jQuery().newsTicker(options);
}
function cs_video_load(theme_url, post_id, post_video,poster){
	'use strict';
   	//var dataString = 'post_video=' + post_video;
   	var dataString = {post_video:post_video,poster:poster};
	jQuery.ajax({
		type:"POST",
		url: theme_url+"/include/video_load.php",
			 data:dataString, 
		success:function(response){
	//jQuery("#myModal"+post_id).hide();
	jQuery("a[data-target='#myModal"+post_id+"']").removeAttr('onclick')
	jQuery("#myModal"+post_id).html(response);
		jQuery('audio,video').mediaelementplayer({
			sfeatures: ['playpause']
		});
	}
});
            //return false;
}
