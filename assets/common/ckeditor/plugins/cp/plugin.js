CKEDITOR.plugins.add( 'cp',
    {
        requires : ['richcombo'],
        init: function( editor )
        {



            // add the menu to the editor
            editor.ui.addRichCombo('cp',
                {
                    label:     'Campo Personalizado',
                    title:     'Campo Personalizado',
                    voiceLabel: 'Campo Personalizado',
                    className:   'cke_format',

                    multiSelect:false,
                    panel:
                    {
                        css: [ editor.config.contentsCss, CKEDITOR.skin.getPath('editor') ],
                        voiceLabel: editor.lang.panelVoiceLabel
                    },

                    init: function()
                    {
                        this.startGroup( "Inserir Campo Personalizado" );
                        for (var i in cp_data)
                        {
                            this.add(cp_data[i][0], cp_data[i][1], cp_data[i][2]);
                        }

                    },


                    onClick: function( value )
                    {
                        editor.focus();
                        editor.fire( 'saveSnapshot' );
                        editor.insertHtml(value);
                        editor.fire( 'saveSnapshot' );
                    }
                });
        }
});