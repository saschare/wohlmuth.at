Ext.form.LinkField = Ext.extend(Ext.form.TriggerField,  {
	triggerClass : 'x-form-link-trigger',
	onTriggerClick : function() {
		var target = this;
		win = new Ext.Window({
			width:400,
			height:600,

	        items: {
				html: '<p>das ist ein test</p>'
			},

	        buttons: [{
	            text:'Select',
	            handler: function() {
	        		target.setValue('TEST KUMMER');
	        		win.close();
	        	}
	        },{
	            text: 'Close',
	            handler: function(){
	        		win.close();
	            }
	        }]
		});
		win.show(this);
    }
});
Ext.reg('linkfield', Ext.form.LinkField);