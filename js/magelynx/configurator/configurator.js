zoom_allowed = false;
if (typeof Product == 'undefined') {
    var Product = {};
}

/**************************** CONFIGURABLE PRODUCT **************************/
var timeout;
Product.Configurator = Class.create();
Product.Configurator.prototype = {
    combinationScore: function(combination){
        var configurator = this;
        
        if(typeof combination != 'undefined' && combination != '' && combination != null){        
            combination = combination.evalJSON();
            var best_score = 0;
            combination.each(function(attributes){
                
                var passed_flag = true;
                for(var attribute_id in attributes){                                                            
                    var operation = attributes[attribute_id]['operation'];
                    var options_ids = attributes[attribute_id]['options'];                    
                    if($('item'+attribute_id))
                    {
                        var selected_option = $('item'+attribute_id).select(".item.active");
                        if(selected_option.length){
                            var selected_option_id = selected_option[0].id.replace("option","");
                            if(options_ids.indexOf(selected_option_id)==-1){
                                passed_flag = false;
                                break;
                            }
                        }else{
                            passed_flag = false;
                            break;
                        }
                    }else{
                        passed_flag = false;
                        break;
                    }
                }
                if(passed_flag && Object.keys(attributes).length > best_score){
                    best_score = Object.keys(attributes).length;
                }
            });
            return best_score;
        }else{
            return 0;
        }        
        
    },
    
    deactivateAll: function(options_ul){
        options_ul.select('.item').each(function(item){                
            item.removeClassName('active');
        })
    },
    
    hideIncompatibleOptions: function(){
        var configurator = this;
        for(var attribute_id in this.attributes){            
            var inventory_enabled = typeof this.attributes[attribute_id]['config'] 
                    && typeof this.attributes[attribute_id]['config']['inventory_enabled'] != 'undefined'
                    && this.attributes[attribute_id]['config']['inventory_enabled'] == 1;

            for(var option_id in this.attributes[attribute_id]['items']){
                var option = this.attributes[attribute_id]['items'][option_id];
                //var option_id = option['option_id'];
                
                //hide if option out of stock
                if(inventory_enabled){
                    if(option['qty'] <= 0){
                        //alert(this.attributes[attribute_id]['config']['inventory_enabled']);
                        if($$('.item#option'+option_id).length > 0){
                            $$('.item#option'+option_id)[0].hide();
                        }
                        return;
                    }                    
                }
                
                var state = option['default_state'];
                var points_disable = configurator.combinationScore(option['combination_disable']);
                var points_enable = configurator.combinationScore(option['combination_enable']);

                if(points_enable>points_disable || state == 1 && points_disable == 0){
                    $$('.item#option'+option['option_id'])[0].show();                    
                }else{
                    if($$('.item#option'+option['option_id']).length == 0){
                      //  alert(option_id);
                    }else{
                        $$('.item#option'+option['option_id'])[0].hide();
                    }
                }
            }
        }
        
        /* robert.k custom code */
        if(!$('accordion_substitution')){
            $$('.product-shop .product-name')[0].insert({after : '<div id="accordion_substitution"></div>'});
        }

        $('accordion_substitution').update('');

        var select_boxes = [];
        
        $$(".attributes .options").each((function(options_ul){
            var selectbox_html = '<option value="">'+this.translate('--Please select--')+'</option>';
            var attribute_id = parseInt(options_ul.id.replace('item',''));
			
            options_ul.select('li.item').each((function(option_li){

                if(!option_li.visible()){
                    return;
                }

                var option_id = parseInt( option_li.id.replace('option',''));
                var price_str = '';
				if(attribute_id != '' && option_id != '' && typeof this.attributes[attribute_id] !== 'undefined'){
					if(parseFloat(this.attributes[attribute_id]['items'][option_id]['price'])){
						price_str =  ' +' + new Template(priceFormat).evaluate({
							'price' : parseFloat(this.attributes[attribute_id]['items'][option_id]['price']).toFixed(2)
						});
					}
				
					var selected = '';
					if(option_li.hasClassName('active')){
						selected = " selected='selected' ";
					}
					selectbox_html += '<option value="'+option_id+'" '+selected+'>'+this.attributes[attribute_id]['items'][option_id]['value']+price_str+'</option>';
				}
            }).bind(this));
			if(typeof this.attributes[attribute_id] !== 'undefined'){
				var label_html = "<label>"+this.attributes[attribute_id]['label']+"</label>";
				selectbox_html = "<select id='attr_"+attribute_id+"'>"+selectbox_html+"</select>";                

				var content = "<div class='"+this.attributes[attribute_id]['label']+"'>"+label_html+selectbox_html+"</div>";
				
				select_boxes.push({'attribute_id' : [attribute_id], 'content': content});    
			}		
        }).bind(this));
        
        select_boxes.sort(function(a,b){
            var order = [965,966,967,968];
            
            return order.indexOf(parseInt(a.attribute_id)) > order.indexOf(parseInt(b.attribute_id));
        });
        
        select_boxes.each(function(selectbox){
            $('accordion_substitution').insert({bottom:selectbox.content});
        });
        

        
        $('accordion_substitution').select('select').each((function(selectbox){
            selectbox.observe('change', (function(){
                if(selectbox.value){
                    this.change(attribute_id,selectbox.value);
                }                    
            }).bind(this));
        }).bind(this));        
    },
    
    
    updatePreview: function(data){        
        $('preview').hide();
		//$$('.product-img-box')[0].show();
			
		
        $$('.product-img-box')[0].hide();
        if(!($('attr_965') && $('attr_965').value 
            && $('attr_966') && $('attr_966').value
            && $('attr_967') && $('attr_967').value
            )){
			
            $$('.product-img-box')[0].show();
        }else{
            $('preview').show();
        }
            
        /*if(!this.use_preview || typeof data == 'undefined'){
            return;
        } */       

        if(data['used_colors'].length > 0){
            if($$('.section-head.colors').length){
                $$('.section-head.colors')[0].show();
            }            
            this.handleZoomToggle(true);
        }else{
            $$('.colors').each(function(element){//by default dt.colors, dd.colors
                element.hide();
            });
            this.handleZoomToggle(false);
        }
        

        $("preview").update('');  
     

        try{//could by an array, that's why do it safely
            for(var layer_code in data['parts_srcs']){
                var layer_parts_html = '';
                var pos = data['positioning'][layer_code];
                var style = 'width:'+pos['width']+'px; height:'+pos['height']+'px; left:'+pos['x']+'px; top:'+pos['y']+'px';
                for(var color_index in data['parts_srcs'][layer_code]){
                    var src = data['parts_srcs'][layer_code][color_index];
                    layer_parts_html += '<img class="color-'+color_index+'" src="'+src+'"/>';
                }
                layer_parts_html = '<div class="parts color-'+color_index+'" style="'+style+'">'+layer_parts_html+'</div>';
                $("preview").insert({bottom:layer_parts_html});
            }

            //click on part
            $$("#preview .parts img").each((function(part){
                part.observe('click', (function(){
                    var color_index;
                    $w(part.className).each(function(classname){
                        if(classname.indexOf('color-') != -1){
                            color_index = classname.replace('color-','');
                        }
                    });

                    $$("#preview .parts img").each(function(_part){
                        if(!_part.hasClassName('color-'+color_index)){
                            _part.removeClassName('active');
                        }                                
                    })

                    this.setSelectedPart(color_index);

                }).bind(this));
            }).bind(this))
            
        }catch(e){};


        //insert image                
        var image_html = '<a id="zoomer-link" href="'+data['big']+'"><img class="main-image" src="'+data['big']+'"/></a>';
		
        $("preview").insert({bottom:image_html});
        jQuery('#zoomer-link').easyZoom({
            parent: jQuery("#preview")
        });        

        this.united_colormap = data['united_colormap'];
        this.used_colors = data['used_colors'].join(",");

        $('custom_image').value = data['big'];
        
        this.highlightCurrentParts(false);
    },
    
    highlightCurrentParts: function(show_highlight){
        return;
        if(typeof this.current_color != 'undefined'){
            var color_index = this.current_color;
            $$("#preview .parts img.color-"+color_index).each((function(_part){
                if(show_highlight){
                    _part.addClassName('active');
                }                

                var color_option_id = this.color_data[color_index];
                if(color_option_id){
                    $$('.colors .item').invoke('removeClassName', 'active');
                    $('option'+color_option_id).addClassName('active');                            
                }                        
            }).bind(this));
        }
    },
    
    getValues: function(){
        var result = {};
        this.getActiveOptions().each((function(selected_option){
            if(selected_option.visible()){
                var attribute_id = parseInt(selected_option.up(".options").id.replace("item",""));
                var option_id = parseInt(selected_option.id.replace("option",""));
                
                result[attribute_id] = option_id;                                
            }            
        }).bind(this));
        return result;
    },
    
    translate: function(text) {
        try {
            if(Translator){
               return Translator.translate(text);
            }
        }
        catch(e){}
        return text;
    },
    
    updatePricing: function(){
        if(!$$('.configurator .pricing').length){
            return;
        }        
        
        $$('.configurator .pricing')[0].update('');
        
        var values = this.getValues(); 
        var total_options_price = 0.;
        
        for(var attribute_id in values){
            
            var option_id = values[attribute_id];
            var pricing_enabled = typeof this.attributes[attribute_id] != 'undefined'
                    && typeof this.attributes[attribute_id]['config'] != 'undefined'
                    && typeof this.attributes[attribute_id]['config']['pricing_enabled'] != 'undefined'
                    && this.attributes[attribute_id]['config']['pricing_enabled'] == 1;
                if(pricing_enabled){
                    var option_name = this.attributes[attribute_id]['items'][option_id]['value'];
                    var price = parseFloat(this.attributes[attribute_id]['items'][option_id]['price']);
                    
                    total_options_price +=price;
                    
                    price = new Template(priceFormat).evaluate({
                        'price' : price.toFixed(2)
                    });
                    
                    
                    $$('.configurator .pricing')[0].insert({
                        bottom:'<div class="option-pricing"><span class="attribute-name">'+this.attributes[attribute_id]['label']+':</span>'+
                            '<span class="option-name">'+this.translate(option_name)+'</span>'+
                            '<span class="regular-price option-price"><span class="price">'+price+'</span></span></div>'
                    })
                }
        }
        
        var label = this.translate('Total:');
                
                
        $$('.product-essential .price').each(function(price){
            if(price.up('.option-pricing')){
                return; 
            }
            if(typeof price.originalPrice == 'undefined'){
                var priceInner = price.innerHTML;            
                var priceVal = parseFloat(priceInner.replace(/[^0-9-.,]/g, ''));
                price.originalPrice = priceVal;
            }            
            
            priceVal = price.originalPrice + total_options_price;
            
            price.update(new Template(priceFormat).evaluate({
                'price' : priceVal.toFixed(2)
            }))
        });       
        
        if(total_options_price){
            total_options_price = new Template(priceFormat).evaluate({
                'price' : total_options_price.toFixed(2)
            });
                
            $$('.configurator .pricing')[0].insert({
                bottom:'<div class="total"><span class="option-name">'+label+'</span>'+
                    '<span class="regular-price option-price"><span class="price">'+total_options_price+'</span></span></div>'
            })
        }        
    },
    
    updateState: function(){
        var configurator = this;
        this.hideIncompatibleOptions();                        
        this.updatePricing();
        
        if(!this.use_preview){
            return;
        }
        
        
        
        var parameters_to_send = this.getValues();
        parameters_to_send['id'] = configurator.productId;
        parameters_to_send['colors_data'] = Object.toJSON(configurator.color_data);        
        
        $('predefined_options').value = Object.toJSON(parameters_to_send).split('"').join("||");
        //alert(Object.toJSON(parameters_to_send));                                                        
        $$('.product-img-box')[0].hide();
		$('preview').show();

        $$('.preview-wrapper')[0].addClassName('loading');
        new Ajax.Request(configurator.controller, {
            method:"post",
            evalScripts: true,
            parameters: parameters_to_send,
            onSuccess: function(transport){                
			
                $$('.preview-wrapper')[0].removeClassName('loading');
                var response;
                
                eval('response = '+transport.responseText);		
                configurator.updatePreview(response);
                                
            }

        });        
    },
    change: function(attribute_id,option_id){
        var configurator = this;
        
        var element = $('attribute'+attribute_id);
		//alert(element);
        //element.select('option').find(function(option){return parseInt(option.value) == parseInt(option_id)}).selected = true;        
        //this.configureElement(element);
        var option = $("option"+option_id);
        if(option){
            //robert.k custom code:
            if($$('.parts img').length){
                //alert($$('.parts img')[0].classNames());
                //alert($w($$('.parts img')[0].className)[0]);
                var color = $w($$('.parts img')[0].className)[0].replace('color-','');
                configurator.current_color = color;
            }
            
            /*if(option.up('.inactive')){
                return;
            } */           
                                    
            if(!option.hasClassName('active') || option.up('.colors')){
                
                if(option.up('.colors') && typeof configurator.current_color == 'undefined'){//color is selected, part not selected
                    return;                    
                }else if(option.up('.colors')){//color is selected and part is selected
                    if(configurator.color_data[configurator.current_color] == option_id){
                        return;//nothing to update
                    }
                    configurator.color_data[configurator.current_color] = option_id;
                }else{
                    this.resetSelectedPart(true);
                }
                
                this.deactivateAll(option.up(".options"));
                option.addClassName('active');                                 
                
                configurator.updateState();
            }
        }        
        
    },
    
    getActiveOptions: function(){
        return $$(".configurable-options ul.options li.item.active");
    },
    
    getAllOptions: function(){
        return $$(".configurable-options ul.options li.item");
    },
    
    initMouseOverFunction: function(){
        if(!this.use_preview){
            return;
        }
        var configurator = this;        
        Event.observe($('preview'),'mousemove', function(e){
            //if zoom mode - stop
            if(zoom_allowed){
                return; 
            }
            
            if($('preview').select('.main-image').length){                            
                var container = $('preview').select('.main-image')[0];
                var containerLeft = Position.page(container)[0]+Position.realOffset(container)[0];
                var containerTop = Position.page(container)[1]+Position.realOffset(container)[1];

                //get the mouse coordinates
                var mouseX = Event.pointerX(e);
                var mouseY = Event.pointerY(e);

                //calculate the absolute mouse position in the div,
                //by mouseposition minus left position of the container
                var horizontalPosition = mouseX - containerLeft;
                var verticalPosition = mouseY - containerTop;
                
                clearTimeout(timeout);
                
                timeout = setTimeout(function(){
                    if(typeof configurator.united_colormap != 'undefined' && configurator.united_colormap){
                        var parameters_to_send = {
                            filepath:configurator.united_colormap,
                            x : horizontalPosition,
                            y : verticalPosition                           
                        };
                        if(typeof configurator.used_colors != 'undefined' && configurator.used_colors){
                            parameters_to_send['colors'] = configurator.used_colors;
                        }
                        new Ajax.Request(configurator.colorpickerUrl, {
                            method:"get",
                            parameters: parameters_to_send,
                            onSuccess: function(transport){
                                var  color_index = transport.responseText;
                                               
                                               /* //parts highlightig
                                $$('.parts img').each(function(highlight){
                                    highlight.removeClassName("over");
                                })
                                
                                if($$('.parts img.color-'+color_index).length>0){                                                                        
                                    $$('.parts img.color-'+color_index).each(function(part_img){
                                        part_img.addClassName("over");
                                    });
                                }*/
                            }

                        });                            
                    }
                }, 30);
            }
        });
        
        Event.observe($('preview'),'mouseleave', function(){
            clearTimeout(timeout);                
            $$('.parts img').each(function(highlight){                
                highlight.removeClassName("over");
            })
        });
    },
    
    initialize: function(controller, productId, colorpickerUrl, attributes, color_data, use_preview, preview_data){
        this.controller = controller;
        this.attributes = attributes;
        this.productId = productId;
        this.colorpickerUrl = colorpickerUrl;
        this.color_data = color_data;
        this.use_preview = use_preview;
                
                
        
        document.observe("dom:loaded", this.initMouseOverFunction.bind(this));        
        
        var configurator = this;
        
        this.hideIncompatibleOptions();
        this.updatePricing();
        
        this.getAllOptions().each(function(option){
            Event.observe(option,"click", function(){                    
                var option_id = option.id.replace("option","");
                var attribute_id = option.up(".options").id.replace("item","");
                configurator.change(attribute_id,option_id);                    
            })
        });          
        
        if($('predefined_print')){
            this.change(966, $('predefined_print').value);
        }
        
        
        try{
            this.updatePreview(preview_data);
        }catch(e){};
    },
    
    handleZoomToggle: function(allow){        
        var configurator = this;
        if($("zoomtoggle")){
            $("zoomtoggle").remove();
        }
        return;
        if(allow){
            $$('.configurator .preview-wrapper')[0].insert({top:"<a href='javascript:void(0);'  id='zoomtoggle'>"+Translator.translate("magnify")+"</a>"});
            $("zoomtoggle").observe("click", function(){configurator.zoomToggle();});
            zoom_allowed = false;            
        }else{
            //no colorifier - always zoom
            zoom_allowed = true;                
        }
    },
    
    setSelectedPart: function(value){
        if($$('.colors .options').length){
            $$('.colors .options')[0].removeClassName('inactive');
            this.current_color = value;
        }
        this.highlightCurrentParts(true);
    },
    
    resetSelectedPart: function(flush_current){
        return;
        if($$('.colors .options').length && (typeof this.current_color == 'undefined' || flush_current)){
            $$('.colors .options')[0].addClassName('inactive');                                    
            $$('.colors .options .item').invoke('removeClassName','active');
            if(flush_current){
                this.current_color = null;
            }
        }               
        
        //robert.k custom code
        //this.current_color = this.used_colors[0];
        
    },
    
    zoomToggle: function(){
        if($("zoomer-link").hasClassName('magnify')){
            
            //colorify mode
            $("zoomtoggle").update(Translator.translate("magnify"));
            $("zoomer-link").removeClassName('magnify');            
            zoom_allowed = false;
        }else{
            //zoom mode
            $("zoomtoggle").update(Translator.translate("colorify"));            
            zoom_allowed = true;
            this.resetSelectedPart(true);
            $$(".parts img").invoke('removeClassName','active');
            $("zoomer-link").addClassName('magnify');
        }
    }
}
