;(function($){
	$.fn.extend({
		np_state: function(opts){
			var index = $(this).data('current'), num = $('.items-grid', this).length;
			if (opts.next != null){
				if (index == num-1){
					$(opts.next).addClass('disabled');
				} else {
					$(opts.next).removeClass('disabled');
				}
			}
			if (opts.prev != null){
				if (index == 0){
					$(opts.prev).addClass('disabled');
				} else {
					$(opts.prev).removeClass('disabled');
				}
			}
		},
		slider: function(options){
			var defaults = {
				event: 'click',
				duration: 500,
				easing: 'easeInOutCubic',
				prev: null,
				next: null,
				pager: '.pager-container ul.pages li',
			};
			var options =  $.extend(defaults, options);
			
			return this.each(function(){
				var container = $('.items-container', this);
				var sliderdiv = $('.items-container-inner', container);
				var pages	  = $(options.pager, this);
				var index 	  = sliderdiv.data('current');
				
				$('.items-grid', container).css({width: function(){ return $(this).width(); }});
				container.removeClass('no-js').addClass('js');
				
				if (!index) {
					sliderdiv.data('current', (index=0));
				};
				pages.bind(options.event, function(){
					if( !$('.page', this).hasClass('active') ){
						var index = pages.index(this);
						$('.page', pages).removeClass('active');
						$('.page', this).addClass('active');
						
						var pageLeft = $('.items-grid', sliderdiv).eq(index).position().left;
						sliderdiv.stop().animate({left: -pageLeft}, {
							duration: options.duration,
							easing: options.easing
						});
					}
				});
				if (options.next){
					var $next = $(options.next);
					$next.bind('click', function(){
						if ($(this).hasClass('disabled') || $(this).parent().hasClass('disabled')){
							return;
						}
						if (!sliderdiv.parents('.sel').length){
							return;
						}
						var index = sliderdiv.data('current');
						index++;
						var nextItem = $('.items-grid', sliderdiv).eq(index);
						if (nextItem.length){
							var pageLeft = $('.items-grid', sliderdiv).eq(index).position().left;
							sliderdiv.stop().animate({left: -pageLeft}, {
								duration: options.duration,
								easing: options.easing
							});
							sliderdiv.data('current', index);
							sliderdiv.np_state(options);
						}
					});
				}
				
				if (options.prev){
					var $prev = $(options.prev);
					$prev.bind('click', function(){
						if ($(this).hasClass('disabled') || $(this).parent().hasClass('disabled')){
							return;
						}
						if (!sliderdiv.parents('.sel').length){
							return;
						}
						var index = sliderdiv.data('current');
						index--;
						if (index < 0) return;
						console.log(index);
						var nextItem = $('.items-grid', sliderdiv).eq(index);
						if (nextItem.length){
							var pageLeft = $('.items-grid', sliderdiv).eq(index).position().left;
							sliderdiv.stop().animate({left: -pageLeft}, {
								duration: options.duration,
								easing: options.easing
							});
							sliderdiv.data('current', index);
							sliderdiv.np_state(options);
						}
					});
				}
			});
		},
		tabs: function(options){
			var defaults = {
				tabs: '.tabs-container ul.tabs li',
				contents: '.tabs-content div.tab-content'
			};
			var options =  $.extend(defaults, options);
			
			return this.each(function(){
				var tabs = $(options.tabs, this);
				var contents = $(options.contents, this);
				if (tabs.length != contents.length){
					return;
				}
				var _next = $('.slider-control .next', this), _prev = $('.slider-control .prev', this);
				
				contents.slider({
					next: _next,
					prev: _prev
				});
				
				/* make tabs sliding */
				var tabs_holder = tabs.eq(0).parent();
				var tabs_container = tabs_holder.parent();
								
				if (tabs_container && tabs_holder){
					tabs_container.css({height: function(){
						return tabs_container.height();
					}});
					tabs_container.css({position: 'relative'});
					tabs_holder.css({position: 'absolute'});
				}
				var viewport   = {};
				viewport.left  = 0;
				viewport.right = tabs_container.width()+2;
				
				var visible   = {};
				visible.left  = viewport.left  - parseInt( tabs_holder.position().left );
				visible.right = viewport.right - parseInt( tabs_holder.position().left );
				
				var posUpdate = function(){
					visible.left  = viewport.left  - parseInt( tabs_holder.position().left );
					visible.right = viewport.right - parseInt( tabs_holder.position().left );
				};
				
				var slidingTo = function(index, d){
					if (!d){
						d = 500
					}
					if (index >= 0){
						if (index == 0){
							index = 1;
						}
						var prevLeft = tabs.eq(index-1).position().left;
						if (prevLeft < visible.left){
							tabs_holder.animate({
								left: '+='+(visible.left-prevLeft)+'px'
							}, {
								duration: d,
								complete: posUpdate
							});
						}
					}
					if (index < tabs.length){
						if (index == tabs.length-1){
							index = tabs.length-2;
						}
						var nextRight = tabs.eq(index+1).position().left + tabs.eq(index+1).width();
						if (nextRight > visible.right){
							tabs_holder.animate({
								left: '-='+(nextRight-visible.right)+'px'
							}, {
								duration: d,
								complete: posUpdate
							});
						}
					}
				};
				
				tabs.each(function(i, tab){
					$(tab).click(function(){
						if ( $(this).hasClass('sel') ){
							return;
						}
						// change tab selected
						$(tabs).removeClass('sel');
						$(this).addClass('sel');
						
						var tab_index = tabs.index(this);

						if (contents[tab_index]){
							// content current
							var ccurrent = $(contents[tab_index]);
							contents.removeClass('sel');
							ccurrent.addClass('sel');
							
							$('.items-container-inner', ccurrent).np_state({next: _next, prev: _prev});
						}
						try{
							tabs_holder.stop();
							posUpdate();
							slidingTo(tab_index);	
						} catch(e){
							console.log(e);
						}
					});
				});
				
				var selected_index = tabs.filter('.sel').index();
				slidingTo(selected_index);	
			});
		}
	});
})($jsmart||jQuery);