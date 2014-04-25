	
$jsmart.cookie = function(name, value, options) {
	if (typeof value != 'undefined') { // name and value given, set cookie
		options = options || {};
		if (value === null) {
			value = '';
			options.expires = -1;
		}
		var expires = '';
		if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
			var date;
			if (typeof options.expires == 'number') {
				date = new Date();
				date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
			} else {
				date = options.expires;
			}
			expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
		}
		// CAUTION: Needed to parenthesize options.path and options.domain
		// in the following expressions, otherwise they evaluate to undefined
		// in the packed version for some reason...
		var path = options.path ? '; path=' + (options.path) : '';
		var domain = options.domain ? '; domain=' + (options.domain) : '';
		var secure = options.secure ? '; secure' : '';
		document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
	} else { // only name given, get cookie
		var cookieValue = null;
		if (document.cookie && document.cookie != '') {
			var cookies = document.cookie.split(';');
			for (var i = 0; i < cookies.length; i++) {
				var cookie = $jsmart.trim(cookies[i]);
				// Does this cookie string begin with the name we want?
				if (cookie.substring(0, name.length + 1) == (name + '=')) {
					cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
					break;
				}
			}
		}
		return cookieValue;

	}
};


/**
 * yt tabs plugin
 */
(function($) { 
	$.fn.ytContentTabs = function (_options){
		return this.each( function() {		
			var container = $( this );
			new $.ytContentTabs().setup( this, container );
		} );
	}
	 $.ytContentTabs = function() { 
		var self = this;					
		this.lastTab = null;
		this.nextTab=null;
		this.setup=function( obj, o ){
			var contentTabs = o.children( 'div.yt-tab-content' );
			var nav = o.children( 'ul.yt-tab-navi' );
			contentTabs.children('div').hide();
			nav.children('li:first').addClass('first').addClass( 'active' );	
			contentTabs.children('div:first').show();
			
			nav.children('li').children('a').click( function() {
				self.lastTab = 	nav.children('li.active').children('a').attr('href');										
				nav.children('li.active').removeClass('active')											
				$(this).parent().addClass('active');
				var currentTab = $(this).attr('href'); 
				self.tabActive( contentTabs, currentTab );		
				return false;
			});	
		};
		this.tabActive=function( contentTabs,  currentTab ){
			if(  this.lastTab != currentTab ){
				contentTabs.children( this.lastTab ).hide();	
			}

			contentTabs.children( currentTab ).show();
		};
	};
})($jsmart);

function switchFontSize (ckname,val){
	var bd = $$('body')[0];
	switch (val) {
		case 'inc':
		if (CurrentFontSize+1 < 7) {
			bd.removeClassName('fs'+CurrentFontSize);
			CurrentFontSize++;
			bd.addClassName('fs'+CurrentFontSize);
		}
		break;
		case 'dec':
		if (CurrentFontSize-1 > 0) {
			bd.removeClassName('fs'+CurrentFontSize);
			CurrentFontSize--;
			bd.addClassName('fs'+CurrentFontSize);
		}
		break;
		default:
		bd.removeClassName('fs'+CurrentFontSize);
		CurrentFontSize = val;
		bd.addClassName('fs'+CurrentFontSize);
	}
	createCookie(ckname, CurrentFontSize,365);
}

function switchTool (ckname, val) {
	createCookie(ckname, val, 365);
	window.location.reload();
}

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g, ""); };

function menuFistLastItem () {
	if ((menu = $('nav')) && (children = menu.childElements()) && (children.length)) {
		children[0].addClassName ('first');
		children[children.length-1].addClassName ('last');
	}
}


// Uodate title module
// function updateTitleBlock (number) {
	// var modules = $$('.yt-col .block-title strong span');
	// if (!modules) return; 
	// modules.each (function(title){ 
		// var html = title.innerHTML;
		// var text = html.stripTags();
		// var pos = text.indexOf(' ');
		// title.setAttribute('title', html);
		// if(html.length > number){
			// html = html.substr(0, number)+'...'
		// }
		// if (pos!=-1) {
			// text = text.substr(0, pos); 
		// }
		// title.update(html.replace(new RegExp (text), '<span class="first-word">'+text+'</span>'));
	// });
// }

// document.observe("dom:loaded", function() {   
	// menuFistLastItem();
	// navMouseHover();
	// updateTitleBlock(20);
// }); 

//Add hover event for li - hack for IE6
function navMouseHover () {
	var lis = $$('#nav li');
	if (lis && lis.length) {
		lis.each (function(li){
			li.onMouseover = toggleMenu (li, 1);
			li.onMouseout = toggleMenu (li, 0);
		});
	}
}

toggleMenu = function (el, over) {
	  var iS = false;
    if (Element.childElements(el) && Element.childElements(el).length > 1) {
	    var uL = Element.childElements(el)[1];
			iS = true;
		}
    if (over) {
        Element.addClassName(el, 'over');
				Element.addClassName (el.down('a'), 'over');
        if(iS){ uL.addClassName('shown-sub')};
    }
    else {
        Element.removeClassName(el, 'over');
				Element.removeClassName (el.down('a'), 'over');
        if(iS){ uL.removeClassName('shown-sub')};
    }
}

function displayChildMenu(id){
	$jsmart("#"+'child_menu'+id).css("display", "block");

	if ( $jsmart("#"+'parent_menu'+id).attr("class").indexOf("active") < 0 ) 
		$jsmart("#"+'parent_menu'+id).addClass("over");
}

function hideAllMenu(){
	menu = $jsmart("[id*=child_menu]");
	
	$jsmart.each(menu, function(){
		$jsmart("#"+this.id).css("display", "none");
		$jsmart("#parent_menu" + this.id.replace("child_menu", "") ).removeClass("over");
	});
}

function rollbackCurrentMenu(){
	hideAllMenu();
	$jsmart("[rel=active_menu]").css("display", "block");
}
