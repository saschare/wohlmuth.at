Ext.ux.form.LinkField = Ext.extend(Ext.form.TriggerField,  {
	triggerClass : 'x-form-link-trigger',
	dataSource: '',
	cancelButtonText: 'Cancel',
	selectButtonText: 'Select',
	onTriggerClick : function() {
		var target = this;
		var dataSource = this.dataSource;
		var linkWin = new Ext.Window({
			width:400,
			height:400,
			layout: 'fit',
			modal: true,
			
	        items: new Ext.tree.TreePanel({
	        	id: 'linkfield-page-tree',
	        	useArrows: true,
	        	autoScroll: true,
	        	animate: true,
	        	containerScroll: true,
	        	border: false,
	        	dataUrl: dataSource + '?showpages=all',
	        	rootVisible: false,
	        	singleExpand: true,
	        	stopRestoring: false,
	        	root: {
	        		nodeType: 'async',
	        		text: 'Root',
	        		draggable: false,
	        		id: '0'
	        	},
	        	listeners: {
	        		click: function(node, event) {
	        			node.expand();
	        		}
	        	}
	        }),

	        buttons: [{
	            text: this.cancelButtonText,
	            handler: function(){
	        		win.close();
	            }
	        }, {
	            text: this.selectButtonText,
	            handler: function() {
	        		var node = Ext.getCmp('linkfield-page-tree').getSelectionModel().getSelectedNode();
	        		if (node.attributes.type == 'category') {
	        			target.setValue('idcat ' + node.attributes.idcat);
	        		} else {
	        			target.setValue('idart ' + node.attributes.idart);
	        		}
	        		linkWin.close();
	        	}
	        }]
		});
		linkWin.show(this);
    }
});
Ext.reg('linkfield', Ext.ux.form.LinkField);