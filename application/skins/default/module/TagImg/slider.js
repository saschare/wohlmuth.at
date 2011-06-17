$(document).ready(function() {
	//jquery and/or jquery tools need to be installed
	$(".slide div").show();
	$(".slide").scrollable({
		circular : true,
		mousewheel : true,
		speed : 600
	}).navigator({
		// select #flowtabs to be used as navigator
		navi : ".imageslider",
		// select A tags inside the navigator to work as items (not direct
		// children)
		naviItem : 'a',
		// assign "current" class name for the active A tag inside navigator
		activeClass : 'current',
		// make browser's back button work
		history : false
	}).autoscroll(5000);

});