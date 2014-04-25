
$jsmart(document).ready(function($) {
	$('#yt_sidenav li.level0 > a').addClass ('subhead');
	$('#yt_sidenav li.level0 > a').after ('<a href="#" title="" class="toggle">&nbsp;</a>');
	// second simple accordion with special markup
	$('#yt_sidenav').accordion({
		active: '.active',
		header: '.toggle',
		navigation: true,
		event: 'click',
		fillSpace: false,
		autoheight: false,
		alwaysOpen: false, 
		animated: 'easeslide'
	});	
});