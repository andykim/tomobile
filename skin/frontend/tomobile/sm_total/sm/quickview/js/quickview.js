jQuery.noConflict();
jQuery(function($) {
	var href,qsbtt;
	//console.log(Sm.QuickView.ITEM_CLASS);
	//get IE version
	function ieVersion(){
		var rv = -1; // Return value assumes failure.
		if (navigator.appName == 'Microsoft Internet Explorer'){
			var ua = navigator.userAgent;
			var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null)
				rv = parseFloat( RegExp.$1 );
		}
		return rv;
	}

	//read href attr in a tag
	function readHref(){
		var pathbase = arguments[0];
		var patt = /\/[^\/]{0,}$/ig;
		if( typeof pathbase == 'undefined'){
			var pathbase = 'null';
		}
		if( pathbase[pathbase.length-1]=="/" ){
			pathbase = pathbase.substring(0,pathbase.length-1);
			return (pathbase.match(patt)+"/");
		}
		return pathbase.match(patt);		
	}
	
	//string trim
	function strTrim(){
		return arguments[0].replace(/^\s+|\s+$/g,"");
	}

	function _qsJnit(){
		var selectorObj = arguments[0];
		//select item use quickview button
		var listitem = $(selectorObj.itemClass);
		var qsImg;
		var pathbase = 'quickview/index/view';
		if( Sm.QuickView.BASE_URL.indexOf('index.php') == -1 ){
			pathbase = 'index.php/quickview/index/view';
		}
		var baseUrl = Sm.QuickView.BASE_URL + pathbase;
		
		var de = Sm.QuickView.ITEM_DIS_ENABLE;
		var _qsHref=null;
		
		var qsHandlerImg = $('.sm_quickview_handler');
		$.each(listitem, function(index, value) { 
			var reloadurl = baseUrl;
			//get reload url	
			if( $(value).find('a:first').length > 0 ){
				href = $(value).find('a:first');					
				var itemLink = readHref(href.attr('href'))[0];
				itemLink[0] == "\/" ? itemLink = itemLink.substring(1,itemLink.length) : itemLink;
				itemLink=strTrim(itemLink);
				reloadurl = baseUrl+"/path/"+itemLink;	
				version = ieVersion();	
				if(version < 8.0 && version > -1){
					reloadurl = baseUrl+"/path"+itemLink;
				}
				
				
				if (de==1) {
					_qsHref = "<a style='z-index:999;' class='button sm_quickview_handler' href='"+reloadurl+"'>Quick View</a>";
				}else{
					_qsHref='';
				}	

				$(value).append(_qsHref);	
			}
		});
		//get jquery fancybox (insert quickview popup)		
		$('.sm_quickview_handler').fancybox({
			'width'				: Sm.QuickView.QV_POP_WIDTH,
			'height'			: Sm.QuickView.QV_POP_HEIGHT,
			'autoScale'			: true,
			'padding'			: 20,
			'margin'			: 20,
			'scrolling'         : 'auto',
			'autoDimensions'    : false,
			'type'				: 'inline',
			'transitionIn'		: 'fade',				
			'transitionOut'		: 'fade',				
			'onComplete'        : function() { 
				$('#fancybox-frame').unbind('load');
				$('#fancybox-frame').bind('load', function() {
					$.fancybox.hideActivity();
				});				
			}
		});
	}
	
	_qsJnit({
		//selector items use to quickview
		//itemClass : '.slider .bg-quickview, .products-grid .item-product-image, .products-list li.item .product-image-wrap'
		itemClass : Sm.QuickView.ITEM_CLASS
	});	
});


