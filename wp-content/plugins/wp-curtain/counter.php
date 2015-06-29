<div class="clock"></div>
<script type="text/javascript">
	var clock;
	$(document).ready(function() {
		var currentDate = new Date();
		var timestamp = Date.parse(futureDateString);
		if (isNaN(timestamp))
			return;
		var futureDate  = new Date(futureDateString);
		var diff = futureDate.getTime() / 1000 - currentDate.getTime() / 1000;
		if(diff<0)
			return;
		if(diff>86400){
			clock = $('.clock').FlipClock(diff, {clockFace: 'DailyCounter',countdown: true});
		}
		else{
			clock = $('.clock').FlipClock(diff, {countdown: true});
			$('.clock').css('width', 460);
		}
	});
</script>