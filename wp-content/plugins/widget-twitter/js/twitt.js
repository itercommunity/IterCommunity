//Change_main_type Twitter Tools
function spider_twitt_change_type (type) {
	jQuery("#tweetbutton").show();
	jQuery("#followbutton").show();
	jQuery("#timeline").show();
	jQuery("#mention").show();
	jQuery("#hashtag").show();
	jQuery("#"+type+"").hide();
  
	jQuery("#tweetbutton_hover").hide();
	jQuery("#followbutton_hover").hide();
	jQuery("#timeline_hover").hide();
	jQuery("#mention_hover").hide();
	jQuery("#hashtag_hover").hide();
	jQuery("#"+type+"_hover").show();
	
	jQuery("#tweetbutton_prev").hide();
	jQuery("#followbutton_prev").hide();
	jQuery("#timeline_prev").hide();
	jQuery("#mention_prev").hide();
	jQuery("#hashtag_prev").hide();
	jQuery("#"+type+"_prev").show(); 
    switch (type) {
	    case 'tweetbutton':
            document.getElementById('type').value='tweetbutton';
 			document.getElementById('counturl_tr').style.display='';
			document.getElementById('url_type').style.display='';			
			if(document.getElementById('url_type0').checked)
			document.getElementById('url_tr').style.display='';			
			else			
			document.getElementById('url_tr').style.display='none';				
			document.getElementById('via_tr').style.display='';			
			document.getElementById('text_tr').style.display='';
			document.getElementById('count_tr').style.display='';
			document.getElementById('lang_type').style.display='';			
			if(document.getElementById('lang_type0').checked)
			document.getElementById('lang').style.display='';
			else
			document.getElementById('lang').style.display='none';
			if(document.getElementById('all_posts_input').checked) {
			document.getElementById('add_post').style.display='none';
			document.getElementById('posts_div').style.display='none'; }
			else {
			document.getElementById('add_post').style.display='';
			document.getElementById('posts_div').style.display=''; }
			if (jQuery("#posts_ids").val() == '')
            jQuery("#posts_div").hide();			
			if(document.getElementById('all_pages_input').checked) {
			document.getElementById('add_pages').style.display='none';
			document.getElementById('pages_div').style.display='none'; }
			else {
			document.getElementById('add_pages').style.display='';
			document.getElementById('pages_div').style.display=''; }
			if (jQuery("#pages_ids").val() == '')
            jQuery("#pages_div").hide();			
			document.getElementById('screen_name').style.display='none';			
			document.getElementById('but_size').style.display='';
			document.getElementById('dnt').style.display='';
			document.getElementById('show_count').style.display='none';
			document.getElementById('align').style.display='none';
			document.getElementById('show_screen_name').style.display='none';
			document.getElementById('width').style.display='none';
			document.getElementById('tweet_to').style.display='none';
			document.getElementById('username_to_1').style.display='none';
			document.getElementById('username_to_2').style.display='none';
			document.getElementById('tw_hashtag').style.display='';
			document.getElementById('count_tr').style.display='';
			document.getElementById('tw_stories').style.display='none';			
			document.getElementById('notification').innerHTML='The Tweet Button is a small widget which allows users to easily share your website with their followers.';
		break;
        case 'followbutton': 
            document.getElementById('type').value='followbutton';
            document.getElementById('counturl_tr').style.display='none';			
			document.getElementById('url_type').style.display='none';   
			document.getElementById('url_tr').style.display='none';			
			document.getElementById('via_tr').style.display='none';
			document.getElementById('text_tr').style.display='none';
			document.getElementById('count_tr').style.display='none';
			document.getElementById('lang_type').style.display='';
			if(document.getElementById('lang_type0').checked)
			document.getElementById('lang').style.display='';
			else
			document.getElementById('lang').style.display='none';
			if(document.getElementById('all_posts_input').checked) {
			document.getElementById('add_post').style.display='none';
			document.getElementById('posts_div').style.display='none'; }
			else {
			document.getElementById('add_post').style.display='';
			document.getElementById('posts_div').style.display=''; }
			if (jQuery("#posts_ids").val() == '')
            jQuery("#posts_div").hide();
			if(document.getElementById('all_pages_input').checked) {
			document.getElementById('add_pages').style.display='none';
			document.getElementById('pages_div').style.display='none'; }
			else {
			document.getElementById('add_pages').style.display='';
			document.getElementById('pages_div').style.display=''; }
			if (jQuery("#pages_ids").val() == '')
            jQuery("#pages_div").hide();
			document.getElementById('screen_name').style.display='';		
			document.getElementById('but_size').style.display='';
			document.getElementById('dnt').style.display='';
			document.getElementById('show_count').style.display='';
			document.getElementById('align').style.display='';
			document.getElementById('tweet_to').style.display='none';
			document.getElementById('username_to_1').style.display='none';
			document.getElementById('username_to_2').style.display='none';
			document.getElementById('show_screen_name').style.display='';
			document.getElementById('width').style.display='';
			document.getElementById('tw_hashtag').style.display='none';
			document.getElementById('tw_stories').style.display='none';			
			document.getElementById('notification').innerHTML='The Follow Button is a small widget which allows users to easily follow a Twitter account from any webpage. The Follow Button uses the same implementation model as the Tweet Button.';
		break;  
        case 'mention': 
            document.getElementById('type').value='mention';
            document.getElementById('counturl_tr').style.display='none';			
			document.getElementById('url_type').style.display='none';			
			document.getElementById('url_tr').style.display='none';			
			document.getElementById('tweet_to').style.display='';
			document.getElementById('username_to_1').style.display='';
			document.getElementById('username_to_2').style.display='';
			document.getElementById('tw_stories').style.display='none';			
			document.getElementById('text_tr').style.display='';
			document.getElementById('via_tr').style.display='none';
			document.getElementById('count_tr').style.display='none';
			document.getElementById('lang_type').style.display='';
			if(document.getElementById('lang_type0').checked)
			document.getElementById('lang').style.display='';
			else
			document.getElementById('lang').style.display='none';			
			if(document.getElementById('all_posts_input').checked) {
			document.getElementById('add_post').style.display='none';
			document.getElementById('posts_div').style.display='none'; }
			else {
			document.getElementById('add_post').style.display='';
			document.getElementById('posts_div').style.display=''; }
			if (jQuery("#posts_ids").val() == '')
            jQuery("#posts_div").hide();
			if(document.getElementById('all_pages_input').checked) {
			document.getElementById('add_pages').style.display='none';
			document.getElementById('pages_div').style.display='none'; }
			else {
			document.getElementById('add_pages').style.display='';
			document.getElementById('pages_div').style.display=''; }
			if (jQuery("#pages_ids").val() == '')
            jQuery("#pages_div").hide();			
			document.getElementById('screen_name').style.display='none';		
			document.getElementById('but_size').style.display='none';
			document.getElementById('dnt').style.display='';
			document.getElementById('show_count').style.display='none';
			document.getElementById('align').style.display='none';
			document.getElementById('show_screen_name').style.display='none';
			document.getElementById('width').style.display='none';
			document.getElementById('tw_hashtag').style.display='none';
			document.getElementById('notification').innerHTML='You can create a Tweet button that allows you to specify a user to mention from within the text and the button. ';
		break;        
        case 'hashtag':
		    document.getElementById('type').value='hashtag';
            document.getElementById('counturl_tr').style.display='none';			
			document.getElementById('url_type').style.display='';
			if(document.getElementById('url_type0').checked)
			document.getElementById('url_tr').style.display='';			
			else			
			document.getElementById('url_tr').style.display='none';			
			document.getElementById('tweet_to').style.display='none';
			document.getElementById('username_to_1').style.display='';
			document.getElementById('username_to_2').style.display='';
			document.getElementById('text_tr').style.display='';
			document.getElementById('via_tr').style.display='none';
			document.getElementById('tw_stories').style.display='';			
			document.getElementById('count_tr').style.display='none';
			document.getElementById('lang_type').style.display='';
			document.getElementById('tw_hashtag').style.display='none';			
			if(document.getElementById('all_posts_input').checked) {
			document.getElementById('add_post').style.display='none';
			document.getElementById('posts_div').style.display='none'; }
			else {
			document.getElementById('add_post').style.display='';
			document.getElementById('posts_div').style.display=''; }
			if (jQuery("#posts_ids").val() == '')
            jQuery("#posts_div").hide();
			if(document.getElementById('all_pages_input').checked) {
			document.getElementById('add_pages').style.display='none';
			document.getElementById('pages_div').style.display='none'; }
			else {
			document.getElementById('add_pages').style.display='';
			document.getElementById('pages_div').style.display=''; }
			if (jQuery("#pages_ids").val() == '')
            jQuery("#pages_div").hide();			
			document.getElementById('screen_name').style.display='none';			
			document.getElementById('but_size').style.display='none';
			document.getElementById('dnt').style.display='';
			document.getElementById('show_count').style.display='none';
			document.getElementById('align').style.display='none';
			document.getElementById('show_screen_name').style.display='none';
			document.getElementById('width').style.display='none';
			document.getElementById('notification').innerHTML='You can create a Tweet button that specifies a hashtag within the text and the button.';
		break; 
    }
}

//Change url_type Twitter Tools
 
function spider_twitt_change_url(type_of_url) {
	if(type_of_url=="normal")
		document.getElementById('url_tr').style.display='';
	else if(type_of_url=="auto")
		document.getElementById('url_tr').style.display='none';
}

//Change lang_type Twitter Tools

function spider_twitt_change_lang(type_of_lang) {
	if(type_of_lang=="normal")
		document.getElementById('lang').style.display='';
	else if(type_of_lang=="auto")
		document.getElementById('lang').style.display='none';
}

//Check post_or_page_type Twitter Tools


function spider_twitt_all_posts(all_posts) {
	if(all_posts) { 
		document.getElementById('add_post').style.display='none';
		document.getElementById('posts_div').style.display='none'; 
	}
	else { 
		document.getElementById('add_post').style.display='';
		if (jQuery("#posts_ids").val() == '')
			document.getElementById('posts_div').style.display='none';
		else 
			document.getElementById('posts_div').style.display=''; 
	}
}

function spider_twitt_all_pages(all_pages) {
	if(all_pages) {
		document.getElementById('add_pages').style.display='none';
		document.getElementById('pages_div').style.display='none'; 
	}
	else {
		document.getElementById('add_pages').style.display='';
		if (jQuery("#pages_ids").val() == '')
			document.getElementById('pages_div').style.display='none';
		else 
			document.getElementById('pages_div').style.display=''; 
	}
}

//Change timeline_type Twitter Tools

function spider_twitt_change_timeline(timeline_type) {
	jQuery("#user_prev").hide();
	jQuery("#list_prev").hide();
	jQuery("#fav_prev").hide();
	jQuery("#search_prev").hide();
	jQuery("#"+timeline_type+"_prev").show();
    switch (timeline_type) { 
        case "user":  
            document.getElementById('notification').innerHTML='You may create an embedded timeline for any public Twitter user.Each user timeline includes a follow button in the header, allowing website visitors to follow the account with one-click. There is a Tweet box in the footer, enabling visitors to Tweet directly to the user without leaving the page.';
            document.getElementById('show_replies').style.display='';
        break;
        case "fav":
			document.getElementById('notification').innerHTML="A favorites timeline may be created for any public Twitter user, and displays that user's favorited Tweets.";
            document.getElementById('show_replies').style.display='none';
        break;
        case "list":
			document.getElementById('notification').innerHTML="The list timeline shows Tweets from a specific list of users. The header of the list widget contains the list name, description, and links to the list creator's profile. Retweets by members of the list are included in the timeline. To create a list timeline you must have either created that list yourself, or subscribe to it.";
            document.getElementById('show_replies').style.display='none';
        break;     
        case "search":
			document.getElementById('notification').innerHTML="You may create a search timeline for any query or #hashtag. Searches for a single #hashtag feature a simplified header section, and a “Tweet #hashtag” Tweet box in the footer so that visitors can easily contribute to the conversation directly from your page. Clicking on the #hashtag in the header will open twitter.com search page for that #hashtag. You may also choose to enable “safe mode”, which will exclude Tweets with common profanity and those marked possibly sensitive from appearing on your website.";
            document.getElementById('show_replies').style.display='none';
        break;   
    }
}

//Insert shortcode Twitter Tools

function insert_twitt() {
    if(document.getElementById('Widget_Twitter').value=='- Select Facbook -') {
 	    tinyMCEPopup.close();
    }
    else {
		var twitt_id;
		twitt_id='[Widget_Twitter id="'+document.getElementById('Widget_Twitter').value+'"]';
		window.tinyMCE.execCommand('mceInsertContent', false, twitt_id);
		tinyMCEPopup.close();		
    }		
}	

//Functions for posts popup Twitter Tools

function twitt_get_posts(e) {
  if (e.preventDefault) {
    e.preventDefault();
  }
  else {
    e.returnValue = false;
  }
  var postids = [];
  var titles = [];
  var tbody = document.getElementById('tbody_arr');
  var trs = tbody.getElementsByTagName('tr');
  for (j = 0; j < trs.length; j++) {
    i = trs[j].getAttribute('id').substr(3);
    if (document.getElementById('check_' + i).checked) {
      postids.push(i);
      titles.push(document.getElementById("a_" + i).innerHTML);
    }
  }
  window.parent.twitt_add_post(postids, titles);
}

function twitt_add_post(postids, titles) {
  var posts_ids = document.getElementById('posts_ids').value;
  tags_array = posts_ids.split(',');
  if(document.getElementById('posts_div').style.display='none')
  document.getElementById('posts_div').style.display='';		
  var div = document.getElementById('div_for_posts');
  var counter = 0;
  for (i = 0; i < postids.length; i++) {
    if (tags_array.indexOf(postids[i]) == -1) {
      posts_ids = posts_ids + postids[i] + ',';
      var tag_div = document.createElement('div');
      tag_div.setAttribute('id',"post_" + postids[i]);
      tag_div.setAttribute('class', "post_or_page_div");
      div.appendChild(tag_div);

      var tag_name_span = document.createElement('span');
      tag_name_span.setAttribute('class', "post_or_page_name");
      tag_name_span.innerHTML = titles[i];
      tag_div.appendChild(tag_name_span);

      var tag_delete_span = document.createElement('span');
      tag_delete_span.setAttribute('class', "spider_delete_img_small");
      tag_delete_span.setAttribute('onclick', "twitt_remove_post('" + postids[i] + "')");
      tag_delete_span.setAttribute('style', "float:right;");
      tag_div.appendChild(tag_delete_span);

      counter++;
    }
  }
  document.getElementById('posts_ids').value = posts_ids;
  if (counter) {
    div.style.display = "block";
  }
  tb_remove();
}

function twitt_remove_post(post_id) {
  if (jQuery('#post_' + post_id)) {
    jQuery('#post_' + post_id).remove();
    var posts_ids_string = jQuery("#posts_ids").val();
    posts_ids_string = posts_ids_string.replace(post_id + ',', '');
    jQuery("#posts_ids").val(posts_ids_string);
    if (jQuery("#posts_ids").val() == '') {
      jQuery("#posts_div").hide();
    }
  }
}

//Functions for pages popup Twitter Tools

function twitt_get_pages(e) {
  if (e.preventDefault) {
    e.preventDefault();
  }
  else {
    e.returnValue = false;
  }
  var postids = [];
  var titles = [];
  var tbody = document.getElementById('tbody_arr');
  var trs = tbody.getElementsByTagName('tr');
  for (j = 0; j < trs.length; j++) {
    i = trs[j].getAttribute('id').substr(3);
    if (document.getElementById('check_' + i).checked) {
      postids.push(i);
      titles.push(document.getElementById("a_" + i).innerHTML);
    }
  }
  window.parent.twitt_add_page(postids, titles);
}

function twitt_add_page(postids, titles) {
  var pages_ids = document.getElementById('pages_ids').value;
  tags_array = pages_ids.split(',');
  if(document.getElementById('pages_div').style.display='none')
  document.getElementById('pages_div').style.display='';		
  var div = document.getElementById('div_for_pages');
  var counter = 0;
  for (i = 0; i < postids.length; i++) {
    if (tags_array.indexOf(postids[i]) == -1) {
      pages_ids = pages_ids + postids[i] + ',';
      var tag_div = document.createElement('div');
      tag_div.setAttribute('id',"page_" + postids[i]);
      tag_div.setAttribute('class', "post_or_page_div");
      div.appendChild(tag_div);

      var tag_name_span = document.createElement('span');
      tag_name_span.setAttribute('class', "post_or_page_name");
      tag_name_span.innerHTML = titles[i];
      tag_div.appendChild(tag_name_span);

      var tag_delete_span = document.createElement('span');
      tag_delete_span.setAttribute('class', "spider_delete_img_small");
      tag_delete_span.setAttribute('onclick', "twitt_remove_page('" + postids[i] + "')");
      tag_delete_span.setAttribute('style', "float:right;");
      tag_div.appendChild(tag_delete_span);

      counter++;
    }
  }
  document.getElementById('pages_ids').value = pages_ids;
  if (counter) {
    div.style.display = "block";
  }
  tb_remove();
}

function twitt_remove_page(post_id) {
  if (jQuery('#page_' + post_id)) {
    jQuery('#page_' + post_id).remove();
    var posts_ids_string = jQuery("#pages_ids").val();
    posts_ids_string = posts_ids_string.replace(post_id + ',', '');
    jQuery("#pages_ids").val(posts_ids_string);
    if (jQuery("#pages_ids").val() == '') {
      jQuery("#pages_div").hide();
    }
  }
}
// Tooltip for Twitter Tools
function simple_tooltip(target_items, name){
  jQuery('.'+target_items).each(function(i){
  var x = 6;
  var y = 105;
  switch (i) {
    case 4: y = 90;
      break;
	case 8: y = 90;
      break;
	case 9: y = 124;
      break;
    case 13: y = 144;
      break;
	case 12: y = 124;
      break;
    case 14: y = 164;
      break;
    case 8: y = 300;
      break;
    case 2: y = 144;
      break;
    case 3: y = 144;
      break;		   
	case 21: y = 250;
      break;
    case 22: y = 250;
      break;	
    case 23: y = 250;
      break;	  
	 }
	jQuery("body").append("<div class='"+name+"' id='"+name+i+"'><div class='wdti_tooltip_content'>"+jQuery(this).attr('title')+"</div><div class='wdti_tooltip_image'></div></div>");
	var my_tooltip = jQuery("#"+name+i);
	jQuery(this).removeAttr("title").mouseover(function(){
	my_tooltip.css({opacity:1, display:"none"}).fadeIn(400);
	}).mousemove(function(kmouse){
	my_tooltip.css({left:kmouse.pageX+x, top:kmouse.pageY-y});
	}).mouseout(function(){
     my_tooltip.fadeOut(200);
	});
	});
}

// Show/hide order column and drag and drop column.
function spider_show_hide_weights() {
  if (jQuery("#show_hide_weights").val() == 'Show order column') {
    jQuery(".connectedSortable").css("cursor", "default");
    jQuery("#tbody_arr").find(".handle").hide(0);
    jQuery("#th_order").show(0);
    jQuery("#tbody_arr").find(".spider_order").show(0);
    jQuery("#show_hide_weights").val("Hide order column");
    if (jQuery("#tbody_arr").sortable()) {
      jQuery("#tbody_arr").sortable("disable");
    }
  }
  else {
    jQuery(".connectedSortable").css("cursor", "move");
    var page_number;
    if (jQuery("#page_number") && jQuery("#page_number").val() != '' && jQuery("#page_number").val() != 1) {
      page_number = (jQuery("#page_number").val() - 1) * 20 + 1;
    }
    else {
      page_number = 1;
    }
    jQuery("#tbody_arr").sortable({
      handle:".connectedSortable",
      connectWith:".connectedSortable",
      update:function (event, tr) {
        jQuery("#draganddrop").attr("style", "");
        jQuery("#draganddrop").html("<strong><p>Changes made in this table should be saved.</p></strong>");
        var i = page_number;
        jQuery('.spider_order').each(function (e) {
          if (jQuery(this).find('input').val()) {
            jQuery(this).find('input').val(i++);
          }
        });
      }
    });//.disableSelection();
    jQuery("#tbody_arr").sortable("enable");
    jQuery("#tbody_arr").find(".handle").show(0);
    jQuery("#tbody_arr").find(".handle").attr('class', 'handle connectedSortable');
    jQuery("#th_order").hide(0);
    jQuery("#tbody_arr").find(".spider_order").hide(0);
    jQuery("#show_hide_weights").val("Show order column");
  }
}

function spider_ajax_save(form_id) {
    var search_value = jQuery("#search_value").val();
	var current_id = jQuery("#current_id").val();
	var page_number = jQuery("#page_number").val();
	var search_or_not = jQuery("#search_or_not").val();
	var ids_string = jQuery("#ids_string").val();
	var image_order_by = jQuery("#image_order_by").val();
	var asc_or_desc = jQuery("#asc_or_desc").val();
	var ajax_task = jQuery("#ajax_task").val();
	var image_current_id = jQuery("#image_current_id").val();
	ids_array = ids_string.split(",");

	var post_data = {};
	post_data["search_value"] = search_value;
	post_data["current_id"] = current_id;
	post_data["page_number"] = page_number;
	post_data["image_order_by"] = image_order_by;
	post_data["asc_or_desc"] = asc_or_desc;
	post_data["ids_string"] = ids_string;
	post_data["task"] = "ajax_search";
	post_data["ajax_task"] = ajax_task;
	post_data["image_current_id"] = image_current_id;
	for (var i in ids_array) {
    if (jQuery("#check_" + ids_array[i]).attr('checked') == 'checked') {
      post_data["check_" + ids_array[i]] = jQuery("#check_" + ids_array[i]).val();
    }
    post_data["input_filename_" + ids_array[i]] = jQuery("#input_filename_" + ids_array[i]).val();
    post_data["image_url_" + ids_array[i]] = jQuery("#image_url_" + ids_array[i]).val();
    post_data["thumb_url_" + ids_array[i]] = jQuery("#thumb_url_" + ids_array[i]).val();
    post_data["image_description_" + ids_array[i]] = jQuery("#image_description_" + ids_array[i]).val();
    post_data["image_alt_text_" + ids_array[i]] = jQuery("#image_alt_text_" + ids_array[i]).val();
    post_data["input_date_modified_" + ids_array[i]] = jQuery("#input_date_modified_" + ids_array[i]).val();
    post_data["input_size_" + ids_array[i]] = jQuery("#input_size_" + ids_array[i]).val();
    post_data["input_filetype_" + ids_array[i]] = jQuery("#input_filetype_" + ids_array[i]).val();
    post_data["input_resolution_" + ids_array[i]] = jQuery("#input_resolution_" + ids_array[i]).val();
    post_data["input_rotate_" + ids_array[i]] = jQuery("#input_rotate_" + ids_array[i]).val();
    post_data["input_flip_" + ids_array[i]] = jQuery("#input_flip_" + ids_array[i]).val();
    post_data["input_crop_" + ids_array[i]] = jQuery("#input_crop_" + ids_array[i]).val();
    post_data["order_input_" + ids_array[i]] = jQuery("#order_input_" + ids_array[i]).val();
    post_data["tags_" + ids_array[i]] = jQuery("#tags_" + ids_array[i]).val();
  }
  // Loading.
  jQuery("#opacity_div").css('width', jQuery("#images_table").css('width'));
  jQuery("#opacity_div").css('height', jQuery("#images_table").css('height'));
  jQuery("#loading_div").css('width', jQuery("#images_table").css('width'));
  jQuery("#loading_div").css('height', jQuery("#images_table").css('height'));
  document.getElementById("opacity_div").style.display = '';
  document.getElementById("loading_div").style.display = '';

  jQuery.post(
    jQuery('#' + form_id).action,
    post_data,

    function (data) {
      var str = jQuery(data).find('#images_table').html();
      jQuery('#images_table').html(str);
      var str = jQuery(data).find('#tablenav-pages').html();
      jQuery('#tablenav-pages').html(str);
      jQuery("#show_hide_weights").val("Hide order column");
      spider_show_hide_weights();
      spider_run_checkbox();
    }
  ).success(function (jqXHR, textStatus, errorThrown) {
      if (ajax_task == 'recover') {
        jQuery('#draganddrop').html("<strong><p>Item Succesfully Recovered.</p></strong>");
      }
      else if (ajax_task == 'image_publish') {
        jQuery('#draganddrop').html("<strong><p>Item Succesfully Published.</p></strong>");
      }
      else if (ajax_task == 'image_publish_all') {
        jQuery('#draganddrop').html("<strong><p>Items Succesfully Published.</p></strong>");
      }
      else if (ajax_task == 'image_unpublish') {
        jQuery('#draganddrop').html("<strong><p>Item Succesfully Unpublished.</p></strong>");
      }
      else if (ajax_task == 'image_unpublish_all') {
        jQuery('#draganddrop').html("<strong><p>Items Succesfully Unpublished.</p></strong>");
      }
      else if (ajax_task == 'image_unpublish') {
        jQuery('#draganddrop').html("<strong><p>Item Succesfully Unpublished.</p></strong>");
      }
      else if (ajax_task == 'image_delete') {
        jQuery('#draganddrop').html("<strong><p>Item Succesfully Deleted.</p></strong>");
      }
      else if (ajax_task == 'image_delete_all') {
        jQuery('#draganddrop').html("<strong><p>Items Succesfully Deleted.</p></strong>");
      }
      else if (ajax_task == 'image_set_watermark') {
        jQuery('#draganddrop').html("<strong><p>Watermarks Succesfully Set.</p></strong>");
      }
      else if (ajax_task == 'image_recover_all') {
        jQuery('#draganddrop').html("<strong><p>Items Succesfully Reset.</p></strong>");
      }
      else {
        jQuery('#draganddrop').html("<strong><p>Items Succesfully Saved.</p></strong>");
      }
      jQuery('#draganddrop').attr("style", "");
      document.getElementById("opacity_div").style.display = 'none';
      document.getElementById("loading_div").style.display = 'none';
    });
  // if (event.preventDefault) {
  // event.preventDefault();
  // }
  // else {
  // event.returnValue = false;
  // }
  return false;
}

function spider_run_checkbox() {
  jQuery("tbody").children().children(".check-column").find(":checkbox").click(function (l) {
    if ("undefined" == l.shiftKey) {
      return true
    }
    if (l.shiftKey) {
      if (!i) {
        return true
      }
      d = jQuery(i).closest("form").find(":checkbox");
      f = d.index(i);
      j = d.index(this);
      h = jQuery(this).prop("checked");
      if (0 < f && 0 < j && f != j) {
        d.slice(f, j).prop("checked", function () {
          if (jQuery(this).closest("tr").is(":visible")) {
            return h
          }
          return false
        })
      }
    }
    i = this;
    var k = jQuery(this).closest("tbody").find(":checkbox").filter(":visible").not(":checked");
    jQuery(this).closest("table").children("thead, tfoot").find(":checkbox").prop("checked", function () {
      return(0 == k.length)
    });
    return true
  });
  jQuery("thead, tfoot").find(".check-column :checkbox").click(function (m) {
    var n = jQuery(this).prop("checked"), l = "undefined" == typeof toggleWithKeyboard ? false : toggleWithKeyboard, k = m.shiftKey || l;
    jQuery(this).closest("table").children("tbody").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked", function () {
      if (jQuery(this).is(":hidden")) {
        return false
      }
      if (k) {
        return jQuery(this).prop("checked")
      } else {
        if (n) {
          return true
        }
      }
      return false
    });
    jQuery(this).closest("table").children("thead,  tfoot").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked", function () {
      if (k) {
        return false
      } else {
        if (n) {
          return true
        }
      }
      return false
    })
  });
}

// Set value by id.
function spider_set_input_value(input_id, input_value) {
  if (document.getElementById(input_id)) {
    document.getElementById(input_id).value = input_value;
  }
}

// Submit form by id.
function spider_form_submit(event, form_id) {
  if (document.getElementById(form_id)) {
    document.getElementById(form_id).submit();
  }
  if (event.preventDefault) {
    event.preventDefault();
  }
  else {
    event.returnValue = false;
  }
}

// Check if required field is empty.
function spider_check_required(id, name) {
  if (jQuery('#' + id).val() == '') {
    alert(name + '* field is required.');
	jQuery('#' + id).focus();
    jQuery('#' + id).attr('style', 'border-style: solid !important;border-color: #FF0000 !important;');
    jQuery('html, body').animate({
      scrollTop:jQuery('#' + id).offset().top - 200
    }, 500);
    return true;
  }
  else {
    return false;
  }
}

// Check if required field is contain http:// or https://
function check_url_to_twitt() {
  var url_to_twitt = jQuery('#url_tr_for').val();
  var true_false_1 = url_to_twitt.indexOf("http://");
  var true_false_2 = url_to_twitt.indexOf("https://");
  if (true_false_1 == -1 && true_false_2 == -1 ) {
   alert('Fill the URL To Tweet field with http:// or https//');
   jQuery('#url_tr_for').focus();
   jQuery('#url_tr_for').attr('style', 'border-style: solid !important;border-color: #FF0000 !important;');
   jQuery('html, body').animate({
      scrollTop:jQuery('#url_tr_for').offset().top - 200
    }, 500);
   return true;
  } 
  else 
   return false; 
}

// Check if required field is contain those matches
function check_hashtag() {
  var stories = jQuery('#stories').val();
  var matches=[",","/","<",">","!",";","'","#","%","&",")","(","-","="];
  for(var i=0; i < matches.length; i++) {
	var true_false=stories.indexOf(matches[i]);	
	if(true_false != -1) {
      alert('Enter "Tweeter Stories:" without \' # . / <  >  ?  !  ;  #  %  &  )  ( -  = \  matches');
      jQuery('#stories').focus();
      jQuery('#stories').attr('style', 'border-style: solid !important;border-color: #FF0000 !important; margin-left:31px');
      jQuery('html, body').animate({
	    scrollTop:jQuery('#stories').offset().top - 200
	  }, 500);
      return true;	
      break;			   
	}		  
  }      
  return false; 
}


// Set uploader to button class.
function spider_uploader(button_id, input_id, delete_id, img_id) {
  if (typeof img_id == 'undefined') {
    img_id = '';
  }
  jQuery(function () {
    var formfield = null;
    window.original_send_to_editor = window.send_to_editor;
    window.send_to_editor = function (html) {
      if (formfield) {
        var fileurl = jQuery('img', html).attr('src');
        if (!fileurl) {
          var exploded_html;
          var exploded_html_askofen;
          exploded_html = html.split('"');
          for (i = 0; i < exploded_html.length; i++) {
            exploded_html_askofen = exploded_html[i].split("'");
          }
          for (i = 0; i < exploded_html.length; i++) {
            for (j = 0; j < exploded_html_askofen.length; j++) {
              if (exploded_html_askofen[j].search("href")) {
                fileurl = exploded_html_askofen[i + 1];
                break;
              }
            }
          }
          if (img_id != '') {
            alert('You must select an image file.');
            tb_remove();
            return;
          }
          window.parent.document.getElementById(input_id).value = fileurl;
          window.parent.document.getElementById(button_id).style.display = "none";
          window.parent.document.getElementById(input_id).style.display = "inline-block";
          window.parent.document.getElementById(delete_id).style.display = "inline-block";
        }
        else {
          if (img_id == '') {
            alert('You must select an audio file.');
            tb_remove();
            return;
          }
          window.parent.document.getElementById(input_id).value = fileurl;
          window.parent.document.getElementById(button_id).style.display = "none";
          window.parent.document.getElementById(delete_id).style.display = "inline-block";
          if ((img_id != '') && window.parent.document.getElementById(img_id)) {
            window.parent.document.getElementById(img_id).src = fileurl;
            window.parent.document.getElementById(img_id).style.display = "inline-block";
          }
        }
        formfield.val(fileurl);
        tb_remove();
      }
      else {
        window.original_send_to_editor(html);
      }
      formfield = null;
    };
    // jQuery('.spider_upload_button').click(function() {
    formfield = jQuery(this).parent().parent().find(".url_input");
    tb_show('', 'media-upload.php?type=image&TB_iframe=true');
    jQuery('#TB_overlay,#TB_closeWindowButton').bind("click", function () {
      formfield = null;
    });
    return false;
    // });
  });
  // jQuery(document).keyup(function(e) {
  // if (e.keyCode == 27) formfield=null;
  // });
}

// Remove uploaded file.
function spider_remove_url(button_id, input_id, delete_id, img_id) {
  if (typeof img_id == 'undefined') {
    img_id = '';
  }
  if (document.getElementById(button_id)) {
    document.getElementById(button_id).style.display = '';
  }
  if (document.getElementById(input_id)) {
    document.getElementById(input_id).value = '';
    document.getElementById(input_id).style.display = 'none';
  }
  if (document.getElementById(delete_id)) {
    document.getElementById(delete_id).style.display = 'none';
  }
  if ((img_id != '') && window.parent.document.getElementById(img_id)) {
    document.getElementById(img_id).src = '';
    document.getElementById(img_id).style.display = 'none';
  }
}

function spider_reorder_items(tbody_id) {
  jQuery("#" + tbody_id).sortable({
    handle:".connectedSortable",
    connectWith:".connectedSortable",
    update:function (event, tr) {
      spider_sortt(tbody_id);
    }
  });
}

function spider_sortt(tbody_id) {
  var str = "";
  var counter = 0;
  jQuery("#" + tbody_id).children().each(function () {
    str += ((jQuery(this).attr("id")).substr(3) + ",");
    counter++;
  });
  jQuery("#albums_galleries").val(str);
  if (!counter) {
    document.getElementById("table_albums_galleries").style.display = "none";
  }
}

function spider_remove_row(tbody_id, event, obj) {
  var span = obj;
  var tr = jQuery(span).closest("tr");
  jQuery(tr).remove();
  spider_sortt(tbody_id);
}

function spider_jslider(idtaginp) {
  jQuery(function () {
    var inpvalue = jQuery("#" + idtaginp).val();
    if (inpvalue == "") {
      inpvalue = 50;
    }
    jQuery("#slider-" + idtaginp).slider({
      range:"min",
      value:inpvalue,
      min:1,
      max:100,
      slide:function (event, ui) {
        jQuery("#" + idtaginp).val("" + ui.value);
      }
    });
    jQuery("#" + idtaginp).val("" + jQuery("#slider-" + idtaginp).slider("value"));
  });
}

function spider_get_items(e) {
  if (e.preventDefault) {
    e.preventDefault();
  }
  else {
    e.returnValue = false;
  }
  var trackIds = [];
  var titles = [];
  var types = [];
  var tbody = document.getElementById('tbody_albums_galleries');
  var trs = tbody.getElementsByTagName('tr');
  for (j = 0; j < trs.length; j++) {
    i = trs[j].getAttribute('id').substr(3);
    if (document.getElementById('check_' + i).checked) {
      trackIds.push(document.getElementById("id_" + i).innerHTML);
      titles.push(document.getElementById("a_" + i).innerHTML);
      types.push(document.getElementById("url_" + i).innerHTML == "Album" ? 1 : 0);
    }
  }
  window.parent.bwg_add_items(trackIds, titles, types);
}

function preview_watermark() {
  setTimeout(function() {
    watermark_type = window.parent.document.getElementById('watermark_type_text').checked;
    if (watermark_type) {
      watermark_text = document.getElementById('watermark_text').value;
      watermark_link = document.getElementById('watermark_link').value;
      watermark_font_size = document.getElementById('watermark_font_size').value;
      watermark_font = document.getElementById('watermark_font').value;
      watermark_color = document.getElementById('watermark_color').value;
      watermark_opacity = document.getElementById('watermark_opacity').value;
      watermark_position = jQuery("input[name=watermark_position]:checked").val().split('-');
      document.getElementById("preview_watermark").style.verticalAlign = watermark_position[0];
      document.getElementById("preview_watermark").style.textAlign = watermark_position[1];
      stringHTML = (watermark_link ? '<a href="' + watermark_link + '" target="_blank" style="text-decoration: none;' : '<span style="cursor:default;') + 'margin:4px;font-size:' + watermark_font_size + 'px;font-family:' + watermark_font + ';color:#' + watermark_color + ';opacity:' + (watermark_opacity / 100) + ';" class="non_selectable">' + watermark_text + (watermark_link ? '</a>' : '</span>');
      document.getElementById("preview_watermark").innerHTML = stringHTML;
    }
    watermark_type = window.parent.document.getElementById('watermark_type_image').checked;
    if (watermark_type) {
      watermark_url = document.getElementById('watermark_url').value;
      watermark_link = document.getElementById('watermark_link').value;
      watermark_width = document.getElementById('watermark_width').value;
      watermark_height = document.getElementById('watermark_height').value;
      watermark_opacity = document.getElementById('watermark_opacity').value;
      watermark_position = jQuery("input[name=watermark_position]:checked").val().split('-');
      document.getElementById("preview_watermark").style.verticalAlign = watermark_position[0];
      document.getElementById("preview_watermark").style.textAlign = watermark_position[1];
      stringHTML = (watermark_link ? '<a href="' + watermark_link + '" target="_blank">' : '') + '<img class="non_selectable" src="' + watermark_url + '" style="margin:0 4px 0 4px;max-width:' + watermark_width + 'px;max-height:' + watermark_height + 'px;opacity:' + (watermark_opacity / 100) + ';" />' + (watermark_link ? '</a>' : '');
      document.getElementById("preview_watermark").innerHTML = stringHTML;
    }
  }, 50);
}

function preview_built_in_watermark() {
  setTimeout(function(){
  watermark_type = window.parent.document.getElementById('built_in_watermark_type_text').checked;
  if (watermark_type) {
    watermark_text = document.getElementById('built_in_watermark_text').value;
    watermark_font_size = document.getElementById('built_in_watermark_font_size').value * 400 / 500;
    watermark_font = 'bwg_' + document.getElementById('built_in_watermark_font').value.replace('.TTF', '').replace('.ttf', '');
    watermark_color = document.getElementById('built_in_watermark_color').value;
    watermark_opacity = document.getElementById('built_in_watermark_opacity').value;
    watermark_position = jQuery("input[name=built_in_watermark_position]:checked").val().split('-');
    document.getElementById("preview_built_in_watermark").style.verticalAlign = watermark_position[0];
    document.getElementById("preview_built_in_watermark").style.textAlign = watermark_position[1];
    stringHTML = '<span style="cursor:default;margin:4px;font-size:' + watermark_font_size + 'px;font-family:' + watermark_font + ';color:#' + watermark_color + ';opacity:' + (watermark_opacity / 100) + ';" class="non_selectable">' + watermark_text + '</span>';
    document.getElementById("preview_built_in_watermark").innerHTML = stringHTML;
  }
  watermark_type = window.parent.document.getElementById('built_in_watermark_type_image').checked;
  if (watermark_type) {
    watermark_url = document.getElementById('built_in_watermark_url').value;
    watermark_size = document.getElementById('built_in_watermark_size').value;
    watermark_position = jQuery("input[name=built_in_watermark_position]:checked").val().split('-');
    document.getElementById("preview_built_in_watermark").style.verticalAlign = watermark_position[0];
    document.getElementById("preview_built_in_watermark").style.textAlign = watermark_position[1];
    stringHTML = '<img class="non_selectable" src="' + watermark_url + '" style="margin:0 4px 0 4px;max-width:95%;width:' + watermark_size + '%;" />';
    document.getElementById("preview_built_in_watermark").innerHTML = stringHTML;
  }
  }, 50);
}

function bwg_watermark(watermark_type) {
  jQuery("#" + watermark_type).attr('checked', 'checked');
  jQuery("#tr_watermark_url").css('display', 'none');
  jQuery("#tr_watermark_width_height").css('display', 'none');
  jQuery("#tr_watermark_opacity").css('display', 'none');
  jQuery("#tr_watermark_text").css('display', 'none');
  jQuery("#tr_watermark_link").css('display', 'none');
  jQuery("#tr_watermark_font_size").css('display', 'none');
  jQuery("#tr_watermark_font").css('display', 'none');
  jQuery("#tr_watermark_color").css('display', 'none');
  jQuery("#tr_watermark_position").css('display', 'none');
  jQuery("#tr_watermark_preview").css('display', 'none');
  jQuery("#preview_watermark").css('display', 'none');
  switch (watermark_type) {
    case 'watermark_type_text':
    {
      jQuery("#tr_watermark_opacity").css('display', '');
      jQuery("#tr_watermark_text").css('display', '');
      jQuery("#tr_watermark_link").css('display', '');
      jQuery("#tr_watermark_font_size").css('display', '');
      jQuery("#tr_watermark_font").css('display', '');
      jQuery("#tr_watermark_color").css('display', '');
      jQuery("#tr_watermark_position").css('display', '');
      jQuery("#tr_watermark_preview").css('display', '');
      jQuery("#preview_watermark").css('display', 'table-cell');
      break;
    }
    case 'watermark_type_image':
    {
      jQuery("#tr_watermark_url").css('display', '');
      jQuery("#tr_watermark_link").css('display', '');
      jQuery("#tr_watermark_width_height").css('display', '');
      jQuery("#tr_watermark_opacity").css('display', '');
      jQuery("#tr_watermark_position").css('display', '');
      jQuery("#tr_watermark_preview").css('display', '');
      jQuery("#preview_watermark").css('display', 'table-cell');
      break;
    }
  }
}

function bwg_built_in_watermark(watermark_type) {
  jQuery("#built_in_" + watermark_type).attr('checked', 'checked');
  jQuery("#tr_built_in_watermark_url").css('display', 'none');
  jQuery("#tr_built_in_watermark_size").css('display', 'none');
  jQuery("#tr_built_in_watermark_opacity").css('display', 'none');
  jQuery("#tr_built_in_watermark_text").css('display', 'none');
  jQuery("#tr_built_in_watermark_font_size").css('display', 'none');
  jQuery("#tr_built_in_watermark_font").css('display', 'none');
  jQuery("#tr_built_in_watermark_color").css('display', 'none');
  jQuery("#tr_built_in_watermark_position").css('display', 'none');
  jQuery("#tr_built_in_watermark_preview").css('display', 'none');
  jQuery("#preview_built_in_watermark").css('display', 'none');
  switch (watermark_type) {
    case 'watermark_type_text':
    {
      jQuery("#tr_built_in_watermark_opacity").css('display', '');
      jQuery("#tr_built_in_watermark_text").css('display', '');
      jQuery("#tr_built_in_watermark_font_size").css('display', '');
      jQuery("#tr_built_in_watermark_font").css('display', '');
      jQuery("#tr_built_in_watermark_color").css('display', '');
      jQuery("#tr_built_in_watermark_position").css('display', '');
      jQuery("#tr_built_in_watermark_preview").css('display', '');
      jQuery("#preview_built_in_watermark").css('display', 'table-cell');
      break;
    }
    case 'watermark_type_image':
    {
      jQuery("#tr_built_in_watermark_url").css('display', '');
      jQuery("#tr_built_in_watermark_size").css('display', '');
      jQuery("#tr_built_in_watermark_position").css('display', '');
      jQuery("#tr_built_in_watermark_preview").css('display', '');
      jQuery("#preview_built_in_watermark").css('display', 'table-cell');
      break;
    }
  }
}

function bwg_change_option_type(type) {
  type = (type == '' ? 1 : type);
  document.getElementById('type').value = type;
  for (var i = 1; i <= 8; i++) {
    if (i == type) {
      document.getElementById('div_content_' + i).style.display = '';
      document.getElementById('div_' + i).style.background = '#C5C5C5';
    }
    else {
      document.getElementById('div_content_' + i).style.display = 'none';
      document.getElementById('div_' + i).style.background = '#F4F4F4';
    }
  }
}

function bwg_inputs() {
  jQuery(".spider_int_input").keypress(function (event) {
    var chCode1 = event.which || event.paramlist_keyCode;
    if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57) && (chCode1 != 46) && (chCode1 != 45)) {
      return false;
    }
    return true;
  });
}

function bwg_enable_disable(display, id, current) {
  jQuery("#" + current).attr('checked', 'checked');
  jQuery("#" + id).css('display', display);
}

function spider_check_isnum(e) {
  var chCode1 = e.which || e.paramlist_keyCode;
  if (chCode1 > 31 && (chCode1 < 48 || chCode1 > 57) && (chCode1 != 46) && (chCode1 != 45))
    return false;
  return true;
}

function bwg_change_theme_type(type) {
  var button_name = jQuery("#button_name").val();
  jQuery("#Thumbnail").hide();
  jQuery("#Masonry").hide();
  jQuery("#Slideshow").hide();
  jQuery("#Compact_album").hide();
  jQuery("#Extended_album").hide();
  jQuery("#Image_browser").hide();
  jQuery("#Blog_style").hide();
  jQuery("#Lightbox").hide();
  jQuery("#Navigation").hide();
  jQuery("#" + type).show();
  jQuery("#current_type").val(type);

  jQuery("#type_Thumbnail").attr("style", "background-color: #F4F4F4;");
  jQuery("#type_Masonry").attr("style", "background-color: #F4F4F4;");
  jQuery("#type_Slideshow").attr("style", "background-color: #F4F4F4;");
  jQuery("#type_Compact_album").attr("style", "background-color: #F4F4F4;");
  jQuery("#type_Extended_album").attr("style", "background-color: #F4F4F4;");
  jQuery("#type_Image_browser").attr("style", "background-color: #F4F4F4;");
  jQuery("#type_Blog_style").attr("style", "background-color: #F4F4F4;");
  jQuery("#type_Lightbox").attr("style", "background-color: #F4F4F4;");
  jQuery("#type_Navigation").attr("style", "background-color: #F4F4F4;");
  jQuery("#type_" + type).attr("style", "background-color: #CFCBCB;");
  }