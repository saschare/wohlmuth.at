Ext.aitsu = function() {
	
	var msgCt;
	var registry;
	var spot;

    function createBox(t, s, error){
    	if (error == undefined) {
    		error = '';
    	}
        return ['<div class="msg', error, '">',
                '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
                '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>',
                '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
                '</div>'].join('');
    }
    
    return {
    	msg : function(title, format){
            if(!msgCt){
                msgCt = Ext.DomHelper.insertFirst(document.body, {id:'msg-div'}, true);
            }
            msgCt.alignTo(document, 't-t');
            var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
            var m = Ext.DomHelper.append(msgCt, {html:createBox(title, s)}, true);
            m.slideIn('t').pause(3).ghost("t", {remove:true});
        },
        
    	errmsg : function(title, format){
            if(!msgCt){
                msgCt = Ext.DomHelper.insertFirst(document.body, {id:'msg-div'}, true);
            }
            msgCt.alignTo(document, 't-t');
            var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
            var m = Ext.DomHelper.append(msgCt, {html:createBox(title, s, ' error')}, true);
            m.slideIn('t').pause(3).ghost("t", {remove:true});
        },
        
        confirm : function(o) {
        	Ext.MessageBox.show({
        		title: o.title,
        		msg: o.msg,
        		buttons: Ext.MessageBox.OKCANCEL,
        		fn: function(result) {
        			if (result == 'ok') {
        				o.ok();
        			} else {
        				o.cancel();
        			}
        		}
        	});	
        },
        
        spot : function(id) {
        	if (typeof(spot) == 'undefined') {
        		spot = new Ext.ux.Spotlight({
        			easing: 'easeOut',
        			duration: .3
        		});
        	}
        	if (typeof(id) == 'undefined') {
        		spot.hide();
        	} else {
        		spot.show(id);
        	}
        },
        
        registry : function() {
        	if (typeof(registry) == 'undefined') {
        		registry = new Ext.util.MixedCollection();
        	}
        	return registry;
        },
        
        load : function(urls, callback, context, preserveOrder) {
        	/*for (var i in urls) {
        		if (String(urls[i]).indexOf('?') == -1) {
        			urls[i] = urls[i] + '?cid=' + new Date().getTime();
        		} else {
        			urls[i] = urls[i] + '&cid=' + new Date().getTime();
        		}
        	}
        	Ext.Loader.load(urls, callback, context, preserveOrder);*/
        	var pendingRequests = urls.length;
        	var code = new Array(urls.length);
        	for (var i in urls) {
        		if (i < urls.length) {
		        	Ext.Ajax.request({
		        		url: urls[i],
		        		callback: function(opts, success, response) {
		        			pendingRequests = pendingRequests - 1;
		        			code[opts.index] = response.responseText;
		        			if (pendingRequests == 0) {
		        				eval(code.join(' '));
		        				if (typeof(callback) != 'undefined' && callback != null) {
		        					callback();
		        				}
		        			}
		        		},
		        		disableCaching: true,
		        		index: i,
		        		scope: context
		        	});
        		}
        	}
        }
    };
}();