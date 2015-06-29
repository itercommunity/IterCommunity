jQuery(document).ready(function(){
		jQuery('#future_date_date').datepicker({dateFormat : 'DD, d MM, yy'});
		jQuery('.section-options').hide();
		jQuery('.section h3').addClass('inactive');		
		jQuery('.section h3').click(function(){
			if(jQuery(this).hasClass('inactive'))
				{	jQuery(this).removeClass('inactive');
					jQuery(this).addClass('active');
					
				}
			else
				{	jQuery(this).removeClass('active');
					jQuery(this).addClass('inactive');
				}
				
			jQuery(this).parent().next('.section-options').toggle();
		});
});