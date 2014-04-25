$jsmart(document).ready(function($){  
    var time_out = 300;
    var yt_cpanel = $('#cpanel_wrapper');
    var btn_show = $('.yt-btn-show');
    var btn_close = yt_cpanel.find('.yt-btn-close'); 
   /* if($.cookie('ytcpanel_closed') == 1){
        hideCpanel(0);
    }else{
        showCpanel(0);
    }*/
    function hideCpanel(time){
        yt_cpanel.animate({
            'right': '-200px' 
        },time, function() {
            btn_show.show().animate({
                'right':'0px'
            },time);
            $.cookie('ytcpanel_closed', 1);
        }); 
    }    
    function showCpanel(time){
        btn_show.animate({
            'right':'-53px'  
        },time, function(){
            yt_cpanel.show().animate({
                'right':'0px'
            });
            $.cookie('ytcpanel_closed', 0);
        });
    }
    btn_close.bind("click", function() {
        hideCpanel(300);
    }); 
    btn_show.bind("click", function() {
        showCpanel(300);
    }); 
	function clickPartern(urlimage, tmpl, val ){
        $('#bd').css('background-image', 'url('+urlimage+')');
		createCookie(tmpl+'_yt_themebodybgimage', val, 365);
    }
	$('.partern-image').bind("click", function() {
    	id = $(this).attr('id'); 
		clickPartern(SKIN_URL+'/images/parttern/'+id+'.png', TMPL_NAME, id);		
    }); 
	function clickHeaderPartern(urlimage, tmpl, val ){
        $('#yt_header').css('background-image', 'url('+urlimage+')');
		createCookie(tmpl+'_yt_themehbgimage', val, 365);
    }
	$('.hpartern-image').bind("click", function() {
    	id = $(this).attr('id'); 
		clickHeaderPartern(SKIN_URL+'/images/hparttern/'+id+'.png', TMPL_NAME, id);		
    }); 
	function clickFooterPartern(urlimage, tmpl, val ){
        $('#yt_footer').css('background-image', 'url('+urlimage+')');
		createCookie(tmpl+'_yt_themefbgimage', val, 365);
    }
	$('.fpartern-image').bind("click", function() {
    	id = $(this).attr('id'); 
		clickFooterPartern(SKIN_URL+'/images/fparttern/'+id+'.png', TMPL_NAME, id);		
    }); 
});



function onResetDefault111(tmpl_name){
	///alert(tmpl_name.escapeRegExp());
	var matches = document.cookie.match('(?:^|;)\\s*' + tmpl_name/*.escapeRegExp()*/ + '_([^=]*)=([^;]*)', 'g');
	if (!matches) return;
	for (i=0;i<matches.length;i++) { //alert(matches[i]);
		var ck = matches[i].match('(?:^|;)\\s*' + tmpl_name/*.escapeRegExp()*/ + '_([^=]*)=([^;]*)');
		if (ck) {
			createCookie (tmpl_name+'_'+ck[1], '', -1);
		}
	}
	
	if (window.location.href.indexOf ('?')>-1) window.location.href = window.location.href.substr(0,window.location.href.indexOf ('?'));
	else window.location.reload();
}

function onApply (tmpl_name) {
	var elems = document.getElementById('cpanel_wrapper').getElementsByTagName ('*');
	
	var usersetting = {};
	for (i=0;i<elems.length;i++) {
		var el = elems[i]; 
	    if (el.name && (match=el.name.match(/^ytcpanel_(.*)$/))) {
	        var name = match[1];	        
	        var value = '';
	        if (el.tagName.toLowerCase() == 'input' && (el.type.toLowerCase()=='radio' || el.type.toLowerCase()=='checkbox')) {
	        	if (el.checked) value = el.value;
	        } else {
	        	value = el.value;
	        }
	        if (usersetting[name]) {
	        	if (value) usersetting[name] = value + ',' + usersetting[name];
	        } else {
	        	usersetting[name] = value;
	        }
	    }
	}
	
	for (var k in usersetting) {
		name = tmpl_name + '_' + k; //alert(name);
		value = usersetting[k];
		createCookie(name, value, 365);
	}
	
	if (window.location.href.indexOf ('?')>-1) window.location.href = window.location.href.substr(0,window.location.href.indexOf ('?'));
	else window.location.reload();
}