// Change image when user hover
jQuery("div.item").each(function(){
		
	var item = jQuery(this);
	var firstImg = item.find("img:first-child");
	var secondImg = item.find("img.product-image-hover");

	item.on("mouseenter",function(){
		firstImg.slideUp("fast");
		secondImg.slideDown("fast");
	
	});
	item.on("mouseleave",function(){			
		firstImg.slideDown("fast");
		secondImg.slideUp("fast");
	});

});	