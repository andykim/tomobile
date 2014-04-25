Combination = {
    restriction_tr_template     :   "<tr><td><a class=\"restriction-delete\" href=\"javascript:void(0);\"></a></td>\n\
                                    <td class=\"a-center\"><a class=\"restriction-up\" href=\"javascript:void(0);\">&uarr;&darr;</a></td>\n\
                                    <td>#{attribute_select}</td><td class=\"a-center\">#{operation_select}</td><td> #{option_select}</td></tr>",
    option_tag_template         :   "<option value=\"#{value}\" #{selected}>#{label}</option>",
    select_tag_template         :   "<select class=\"#{class}\" #{multiselect} >#{options}</select>",
    all_operations              :   {'eq' : '=','in' : 'IN'},
    
    combinationToString: function(combination){
        var template = "#{attribute_label} <b>#{operation}</b> #{options}";
        var restrictions = [];
        for(var attribute_id in combination){
            var restriction = combination[attribute_id];
            var operation = '=' //eq;            
            if(restriction['operation'].toLowerCase() == 'in'){
                operation = 'in';
            };
            var options = restriction['options'];
            
            
            var option_labels = [];
            options.each((function(option_id){
                option_labels.push(this.attributes_data[attribute_id]['options'][option_id]);
            }).bind(this));
            
            restrictions.push(new Template(template).evaluate({
                'attribute_label'   : this.attributes_data[attribute_id]['frontend_label'],
                'operation'         : operation,
                'options'           : option_labels.join(",")
            }));
            
        }
        return restrictions.join(" <span class='and'>and</span> ");
    },    
    renderInput: function(input){           
        var data = input.value;
        
        var combinations = [];
        try{
             combinations = data.evalJSON();
        }catch(e){}        
        
        var combination_strings = [];        
        
        var template = "<li><div class='f-left'>#{adopted_index}.</div>\n\
<a class=\"delete\" id=\"delete__#{index}__#{input_id}\" title=\"#{delete_caption}\" href=\"javascript:void(0);\">&nbsp</a>\n\
<a class=\"edit\" id=\"edit__#{index}__#{input_id}\" title=\"#{edit_caption}\" href=\"javascript:void(0);\">#{combination}</a>\n\
</li>";
        
        combinations.each((function(combination, index){
            var combination_html = new Template(template).evaluate({
                'combination'       : this.combinationToString(combination),
                'index'             : index,
                'input_id'          : input.id,
                'delete_caption'    : Translator.translate('Delete'),
                'edit_caption'      : Translator.translate('Edit'),
                'adopted_index'     : index+1
            });                
            combination_strings.push(combination_html);
        }).bind(this));        
        
        var add_link_html = '<a class="add" id="add__'+input.id+'" href="javascript:void(0);">'+Translator.translate('Add')+"</a>";
        
        return add_link_html+"<ul class=\"combinations-list\">"+combination_strings.join("")+"</ul>";
    },
    
    initGridEditHandler: function(edit_handle){
        var data = edit_handle.id.split("__");
        if(data.length == 3){
            Event.observe(edit_handle, 'click', (function(){
                var index = data[1];
                var input_id = data[2];

                var input = $(input_id);
                var combinations = input.value.evalJSON();

                var combination = combinations[index];
                var caption = edit_handle.up('.combination-column').select("input.name-hint")[0].value;

                this.current_combination_index = index;
                this.current_input = input;
                this.showCombinationPopup(combination, caption);
            }).bind(this));
        }
    },
    
    initGridDeleteHandler: function(delete_handle){
        var data = delete_handle.id.split("__");
        if(data.length == 3){
            Event.observe(delete_handle, 'click', (function(){
                var index = data[1];
                var input_id = data[2];

                var input = $(input_id);
                var combinations = input.value.evalJSON();

                combinations.splice(index,1);

                input.value = Object.toJSON(combinations);

                var combinations_container=delete_handle.up(".combinations-list").up();
                combinations_container.update(this.renderInput(input));
                this.initCombinationsHandlers(combinations_container);
            }).bind(this));
        }
    },
    
    initGridAddHandler: function(add_handle){
        var data = add_handle.id.split("__");
        if(data.length == 2){
            Event.observe(add_handle, 'click', (function(){
                var input_id = data[1];

                var input = $(input_id);
                var combinations = [];  
                try{
                    combinations = input.value.evalJSON();
                }catch(e){
                    input.value = "[]";
                }
                                
                var index = combinations.length;               
                
                //add new combination placeholder
                combinations.push({});

                var combination = combinations[index];
                var caption = add_handle.up('.combination-column').select("input.name-hint")[0].value;

                this.current_combination_index = index;
                this.current_input = input;
                this.showCombinationPopup(combination, caption);
            }).bind(this));
        }
    },
    
    initGridObservers: function(){
        $$('a.delete').each((function(delete_handle){            
            this.initGridDeleteHandler(delete_handle);
        }).bind(this));
        
        
        $$('a.edit').each((function(edit_handle){            
            this.initGridEditHandler(edit_handle);
        }).bind(this));
        
        $$('a.add').each((function(add_handle){            
            this.initGridAddHandler(add_handle);
        }).bind(this));        
                        
    },
    
    showCombinationPopup: function(combination, caption){       
        var attribute_ids = Object.keys(combination);
        
        var template = this.restriction_tr_template;
        var option_template = this.option_tag_template;
        var select_template = this.select_tag_template;
        
        var html = '';
        var counter = 0;            
        for(var attribute_id in combination){
            counter ++;
            //prepare attributes select
            var attribute_select = '';           
            for(var _attribute_id in this.attributes_data){
                var selected = '';
                if(attribute_ids.indexOf(_attribute_id) != -1 && attribute_id != _attribute_id){
                    continue;
                }else if(attribute_id == _attribute_id){
                    selected = 'selected="selected"';
                }
                
                var attribute_data = this.attributes_data[_attribute_id];
                attribute_select +=  new Template(option_template).evaluate({
                    'label'     :   attribute_data['frontend_label'],
                    'value'     :   _attribute_id,
                    'selected'  :   selected
                }) 
            }
            attribute_select = new Template(select_template).evaluate({
                'options'   :   attribute_select,
                'class'     :   'current-attribute-select'
            });
            
            //prepare operation select            
            var operation_select = '';
            var all_operations = this.all_operations;
            
            for(var operation in all_operations){                
                var selected = '';
                if(combination[attribute_id]['operation'].toLowerCase() == operation ){
                    selected = 'selected="selected"';
                }
                
                operation_select += new Template(option_template).evaluate({
                    'label'     :   all_operations[operation],
                    'value'     :   operation,
                    'selected'  :   selected
                });
            }
            operation_select = new Template(select_template).evaluate({
                'options'   :   operation_select,
                'class'     :   'current-operation-select'
            })
            
                        
            //prepare options select
            var option_select = '';
            for(var option_id in this.attributes_data[attribute_id]['options']){
                var selected = '';
                if(combination[attribute_id]['options'].indexOf(option_id) != -1 ){
                    selected = 'selected="selected"';
                }
                
                option_select += new Template(option_template).evaluate({
                    'label'     :   this.attributes_data[attribute_id]['options'][option_id],
                    'value'     :   option_id,
                    'selected'  :   selected
                });
            }
            
            var multiple = '';
            if(combination[attribute_id]['operation'].toLowerCase() == 'in'){
                multiple = 'multiple size="5"';
            }
            option_select = new Template(select_template).evaluate({
                'options'       :   option_select,
                'multiselect'   :   multiple,
                'class'         :   'current-option-select'
            })                  
            
            
            // generate combination html            
            html += new Template(template).evaluate({
                    'attribute_select'  :   attribute_select,
                    'operation_select'  :   operation_select,
                    'option_select'     :   option_select,
                }) 
        }
        
        var headings = [Translator.translate("Remove"),Translator.translate("Move"),Translator.translate("Attribute"),Translator.translate("Operation"),Translator.translate("Options")];
        var colgroup = "<colgroup><col width=\"1\"/><col width=\"1\"/><col/><col/><col/></colgroup>";
            
        var table_heading = '';
        headings.each(function(heading){
            table_heading += '<th>'+heading+'</th>';
        });        
        table_heading = colgroup+'<thead><tr class="headings">'+table_heading+'</tr></thead>';
        html = '<div class="table-wrapper  grid"><table cellspacing="0" class="data" id="restrictions-list">'+table_heading+'<tbody>'+html+'</tbody></table></div>';
        html += '<div class=\"buttons\"><button id="add-restriction" class=\"add\"><span>'+Translator.translate("Add restriction")+'</span></button>';
        html += '<button id="done" class=\"save\"><span>'+Translator.translate("Done")+'</span></button></div>';
        
        var heading = '<h3>'+caption+'</h3>';
        heading += '<a class="closer" href="javascript:void(0);" onclick="$(\'combination-edit-popup\').remove()">&#9746;</a>';
        
        var content = new Template(
            '<div id="combination-edit-popup">\n\
                <div class="overlay"></div>\n\
                <div class="inner">\n\
                    <div class="heading">#{heading}</div>\n\
                    #{content}\n\
                </div>\n\
            </div>').evaluate({
                'content'   :   html,
                'heading'    :   heading
            });

        new Insertion.Bottom($$('body')[0], content);
        this.initPopupObservers();
    },
    
    fillOptionsSelect: function(element, options){
        element.select("option").invoke('remove');        
        for(var option_id in options){
            element.insert('<option value="'+option_id+'">' +options[option_id] + '</option>');
        }
    },
    
    removeOptionFromSelects: function(selects, option_id){
        selects.each((function(_select){
            if(_select.select("option[value="+option_id+"]").length > 0 && _select.value != option_id ){
                _select.select("option[value="+option_id+"]")[0].remove();
            }
        }).bind(this));
    },
    
    addOptionToSelects: function(selects, option_element){
        selects.each((function(_select){
            if(_select.select("option[value="+option_element.value+"]").length == 0){
                var element_to_insert = option_element.cloneNode(true);
                element_to_insert.selected = false;
                _select.insert(element_to_insert);
            }
        }).bind(this));
    },
    
    initAttributeSelectObservers: function(attribute_select){
        Event.observe(attribute_select, 'focus', function(e){
            //preserve value in order to use in onchange below
            attribute_select.old_value = attribute_select.value;
        })
        Event.observe(attribute_select, 'change', (function(e){
            //remove newly selected option from other selects
            //AND add newly deselected option to other selects
            var old_option_id = attribute_select.old_value;
            var new_option_id = attribute_select.value;
            attribute_select.old_value = new_option_id;

            var old_option = attribute_select.select("option[value="+old_option_id+"]")[0];

            var selects = $$("#combination-edit-popup .current-attribute-select");
            this.addOptionToSelects(selects,old_option);
            this.removeOptionFromSelects(selects, new_option_id);                                

            //refresh options select
            var option_select = attribute_select.up('tr').select(".current-option-select")[0];
            this.fillOptionsSelect(option_select, this.attributes_data[attribute_select.value]['options']);                                                                
        }).bind(this));
    },
    
    initCombinationsHandlers: function(combinations_container){
        this.initGridAddHandler(combinations_container.select('a.add')[0]);
        combinations_container.select('a.edit').each((function(edit_handle){
            this.initGridEditHandler(edit_handle)
        }).bind(this));
        combinations_container.select('a.delete').each((function(delete_handle){
            this.initGridDeleteHandler(delete_handle)
        }).bind(this));        
    },
    
    initOperationSelectObservers: function(operation_select){
        Event.observe(operation_select, 'change', (function(e){
            //refresh options select
            var option_select = operation_select.up('tr').select(".current-option-select")[0];
            if(operation_select.value.toLowerCase() == 'eq'){                                        
                option_select.removeAttribute('multiple');
                option_select.removeAttribute('size');

            }else if(operation_select.value.toLowerCase() == 'in'){
                option_select.multiple = true;
                option_select.size = 5;
            }
        }).bind(this));
    },    
    
    handleOddEvenRestrictions: function(){
        //handle odd/even
        var counter=0;
        $$("#combination-edit-popup table tbody tr").each(function(_tr){
            counter++;
            if(counter%2 == 1){
                _tr.addClassName('even');
            }else{
                _tr.removeClassName('even');
            }
        });
    },
    
    initRestrictionObservers: function(tr){
        tr.select('.restriction-delete')[0].observe('click', (function(){            
            var attribute_select = tr.select('.current-attribute-select')[0];
            var option = attribute_select.select("option[value="+attribute_select.value+"]")[0];
            
            var selects = $$("#combination-edit-popup .current-attribute-select");
            this.addOptionToSelects(selects,option);
            tr.remove();
            this.handleOddEvenRestrictions();
        }).bind(this));
        this.initAttributeSelectObservers(tr.select('.current-attribute-select')[0]);
        this.initOperationSelectObservers(tr.select('.current-operation-select')[0]);                
    },
    
    initPopupObservers: function(){
        $$("#combination-edit-popup #restrictions-list tbody tr").each((function(tr){
            this.initRestrictionObservers(tr);
        }).bind(this));
                
                                
        //add restriction row
        Event.observe($("add-restriction"), 'click', (function(){            
            var template = this.restriction_tr_template;
            var option_template = this.option_tag_template;
            var select_template = this.select_tag_template;
            
            //attribute select
            var attribute_select = '';            
            var first_flag = true;            
            var existing_attribute_ids = [];
            $$('.current-attribute-select').each(function(_attribute_select){
                existing_attribute_ids.push(_attribute_select.value);
            })
            
            var selected_attribute_id;
            for(var attribute_id in this.attributes_data){
                if(existing_attribute_ids.indexOf(attribute_id) != -1){
                    continue;
                }
                
                var selected = '';
                if(first_flag){
                    selected = 'selected="selected"';
                    var selects = $$("#combination-edit-popup .current-attribute-select");
                    this.removeOptionFromSelects(selects, attribute_id);                                
                    selected_attribute_id = attribute_id;
                    first_flag = false;
                }
                
                var attribute_data = this.attributes_data[attribute_id];
                attribute_select +=  new Template(option_template).evaluate({
                    'label'     :   attribute_data['frontend_label'],
                    'value'     :   attribute_id,
                    'selected'  :   selected
                }) 
            }
            if(!selected_attribute_id){
                return;
            }
                
            
            attribute_select = new Template(select_template).evaluate({
                'options'   :   attribute_select,
                'class'     :   'last current-attribute-select'
            });
            
            $$('.current-attribute-select').invoke("removeClassName", 'last');
            
            //operation select
            var operation_select = '';
            first_flag = true;
            var all_operations = this.all_operations;
            for(var operation in all_operations){
                selected = '';
                if(first_flag){
                    selected = 'selected="selected"';
                    first_flag = false;
                }
                
                operation_select += new Template(option_template).evaluate({
                    'label'     :   all_operations[operation],
                    'value'     :   operation,
                    'selected'  :   selected
                });
            }
            operation_select = new Template(select_template).evaluate({
                'options'   :   operation_select,
                'class'     :   'last current-operation-select'
            })
            
            $$('.current-operation-select').invoke("removeClassName", 'last');
            
            //options select            
            var option_select = '';
            for(var option_id in this.attributes_data[selected_attribute_id]['options']){                
                
                option_select += new Template(option_template).evaluate({
                    'label'     :   this.attributes_data[selected_attribute_id]['options'][option_id],
                    'value'     :   option_id,
                    'selected'  :   ''
                });
            }                        
            option_select = new Template(select_template).evaluate({
                'options'       :   option_select,
                'multiselect'   :   '',
                'class'         :   'last current-option-select'
            })
            
            $$('.current-option-select').invoke("removeClassName", 'last');
                        
            var row = new Template(template).evaluate({
                    'attribute_select'  :   attribute_select,
                    'operation_select'  :   operation_select,
                    'option_select'     :   option_select,
                });
                
            $$("table#restrictions-list tbody")[0].insert({'bottom':row});
                        
            this.initRestrictionObservers($$("table#restrictions-list tr").last());            
            this.handleOddEvenRestrictions();
            
        }).bind(this));
        
        Event.observe($("done"), 'click', (function(){
            var combination = {};
            $$("table#restrictions-list tbody tr").each(function(tr){
                var attribute_id = tr.select('.current-attribute-select')[0].value;
                var operation = tr.select('.current-operation-select')[0].value;
                var options = tr.select('.current-option-select')[0].getValue();
                
                if(!options instanceof Array || typeof options == 'string'){
                    //alert(typeof options);
                    options = [options];
                }
                
                combination[attribute_id]={'operation':operation, 'options':options};                
                
            });
                        
            if(typeof this.current_input !== 'undefined' && typeof this.current_combination_index !=='undefined'){
                var combinations = this.current_input.value.evalJSON();
                if($$("table#restrictions-list tbody tr").length){//combination not empty
                    combinations[this.current_combination_index] = combination;
                }else{//combination empty => remove
                    combinations.splice(this.current_combination_index,1);
                }
                this.current_input.value = Object.toJSON(combinations);
                
                var combinations_container=this.current_input.next(".grid-combination-preview");
                combinations_container.update(this.renderInput(this.current_input));                
                this.initCombinationsHandlers(combinations_container);
                /*
                this.initGridAddHandler(combinations_container.select('a.add').last());
                this.initGridEditHandler(combinations_container.select('a.edit')[this.current_combination_index]);
                this.initGridDeleteHandler(combinations_container.select('a.delete')[this.current_combination_index]);
                */
                
                this.current_input = null;
                this.current_combination_index = null;
            }
            $('combination-edit-popup').remove();
            
                        
        }).bind(this));
        this.handleOddEvenRestrictions();
    },
    
    init:function(attributes_data){
        this.attributes_data = attributes_data;
    },
    test: function(data){
        alert(data);
    }
}