CKEDITOR.plugins.add('shortcodes', {   
    requires : ['richcombo'],
    
    init : function(editor) {
        var shortcodes = [];
                
        $.post('../?renderOnly=Shortcodes.Dropdown', function(data) {
            $.each(data, function(index, shortcode) {
                shortcodes[index] = [shortcode.shortcode, shortcode.label, shortcode.label];
            });
        }, "json");
        
        var config = editor.config,
        lang = editor.lang.format;

        editor.ui.addRichCombo('shortcodes', {
            label : "Insert Shortcode",
            title :"Insert Shortcode",
            voiceLabel : "Insert Shortcode",
            className : 'cke_format',
            multiSelect : false,

            panel : {
                css: [config.contentsCss, CKEDITOR.getUrl(editor.skinPath + 'editor.css')],
                voiceLabel: lang.panelVoiceLabel
            },

            init : function() {
                this.startGroup("Shortcodes");
                for (var this_shortcode in shortcodes){
                    this.add(shortcodes[this_shortcode][0], shortcodes[this_shortcode][1], shortcodes[this_shortcode][2]);
                }
            },

            onClick : function(value) {   
                var random = Math.round(10000+99999*Math.random()*10);
                
                editor.focus();
                editor.fire( 'saveSnapshot' );
                
                value = value.replace(/INDEX/g, random);
                value = value.replace(/\[/g, '_[');
                
                editor.insertHtml(value);
                editor.fire( 'saveSnapshot' );
            }
        });
    }
});
