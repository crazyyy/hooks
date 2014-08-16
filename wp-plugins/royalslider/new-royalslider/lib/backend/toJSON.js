/**
 * jQuery-to-JSON plugin by Paul Macek 
 * https://github.com/macek/jquery-to-json
 *
 * Modified for RoyalSlider.
 */
(function($){
    $.fn.toJSON = function(isSimple, convertToSingle){
        
        var self = this,
            json = {},
            push_counters = {},
            patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key":      /[a-zA-Z0-9_\-]+|(?=\[\])/g,
                "push":     /^$/,
                "fixed":    /^\d+$/,
                "named":    /^[a-zA-Z0-9_\-]+$/
            };
        
        
        this.build = function(base, key, value){
            base[key] = value;
            return base;
        };
        
        this.push_counter = function(key, i){
            if(push_counters[key] === undefined){
                push_counters[key] = 0;
            }
			if(i === undefined){
				return push_counters[key]++;
			}
			else if(i !== undefined && i > push_counters[key]){
				return push_counters[key] = ++i;
			}
        };
        $.each($(this), function(){
            
            if(!isSimple) {
                var rsOpt = $(this);
                var input = rsOpt.find(':input');
                var inputEl = input[0];

                if(!inputEl) {
                	return;
                }
                if(!patterns.validate.test(inputEl.name)){
                    return;
                }
                
                var k,
                    keys = inputEl.name.match(patterns.key),
                    merge = inputEl.value,
                    reverse_key = inputEl.name;
                
                if( input.is(':checkbox') && !input.is(':checked') ) {
                	merge = false;
                }
                
                function getFormattedVar(v, type) {
                	if(!type || typeof v !== 'string') return v;
                	switch( type.toLowerCase() ) {
    	            	case 'str':
    	            		return v.toString();
    	            	break;
    	            	case 'num':
    	            		return isNaN(parseFloat(v)) ? '' : parseFloat(v);
    	            	break;
    	            	case 'int':
    	            		return parseInt(v);
    	            	break;
    	            	case 'bool':
    	            		v = v.toLowerCase();
    	            		return (v === '' || v === '1' || v === 'on' || v === 'true' || v === 'yes') ? true : false;
    	            	break;
    	            }
                }


                var dataType = rsOpt.attr('data-type');
                merge = getFormattedVar(merge, dataType);

                
                var defaultValue = getFormattedVar(rsOpt.attr('data-default'), dataType);

            	if(merge === defaultValue) {
            		return;
            	}
            } else {
                if(!$(this).attr('name')) {
                    return;
                }
                if($(this).attr('type') === 'submit') {
                    return;
                }
                var inputEl = $(this).serializeArray();
                inputEl = inputEl[0];
                if(inputEl.value === '') {
                    return;
                }
                var k,
                    keys = inputEl.name.match(patterns.key),
                    merge = inputEl.value,
                    reverse_key = inputEl.name;
            }
            while((k = keys.pop()) !== undefined){
                
                // adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');
                
                // push
                if(k.match(patterns.push)){
                    merge = self.build([], self.push_counter(reverse_key), merge);
                }
                
                // fixed
                else if(k.match(patterns.fixed)){
					self.push_counter(reverse_key, k);
                    merge = self.build([], k, merge);
                }
                
                // named
                else if(k.match(patterns.named)){
                    merge = self.build({}, k, merge);
                }
            }

            json = $.extend(true, json, merge);
             
        });

        return json;
    };
})(jQuery);