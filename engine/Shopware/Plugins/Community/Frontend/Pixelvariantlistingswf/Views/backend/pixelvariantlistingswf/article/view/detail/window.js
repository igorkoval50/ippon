//{block name="backend/article/view/detail/window" append}

  


Ext.override(Shopware.apps.Article.view.detail.Window, {



    createMainTabPanel: function () {
        var me = this, result;

        result = me.callParent(arguments);

        // ...when an article is binded to the component, we're dealing with < 4.1
        if (me.hasOwnProperty('article') && me.article) {

            result.add(me.createPixelvariantlistingswfTab());

        } else {



            // We're dealing with >= 4.1, so we use the new way
            me.registerAdditionalTab({
                title: 'Varianten Listing',
                contentFn: function (article, stores, eOpts) {


                    
                    setmovalues = me.createSetPixelvariantlistingswfTab();


                    me.MoTab = Ext.create('Ext.tab.Panel', {
                        name: 'Mooptionenswf-tab',
                        items: [ 
                            setmovalues

                        ]

                    });

                    me.MoHauptTab = Ext.create('Ext.panel.Panel', {
                        name: 'pixelvariantlistingswf-tab',
                        disabled: (me.article.get('id') === null),
                        autoScroll: true,
                        items: [me.MoTab ]
                    });


                    eOpts.tab.add(me.MoHauptTab);
                },


                tabConfig: {
                    disabled: false,
                    layout: {
                        'layout': 'fit', autoScroll: true,

                    },
                    listeners: {
                        activate: function () {

                            this.setDisabled(false);
                        },
                        deactivate: function () {
                            this.setDisabled(false);
                        }
                    }
                },
                scope: me
            });


        }

        return result;
    },

    createPixelvariantlistingswfTab: function () {
        var me = this, setMMmovalues, settings, toolbar;

        
        setMMmovalues = me.createSetPixelvariantlistingswfTab();


        me.MoMMTab = Ext.create('Ext.tab.Panel', {
            name: 'PxPixelvariantlistingswf-tab',
            autoScroll: true,
            items: [ 
                setMMmovalues

            ]

        });


        return Ext.create('Ext.panel.Panel', {
            title: 'Varianten Listing',
            name: 'pixelvariantlistingMMswf-tab',
            disabled: (me.article.get('id') === null   ),
            autoScroll: true,
            items: [me.MoMMTab ]
        });
    },
 

          


    createSetPixelvariantlistingswfTab: function () {
        var me = this, iconlisting;


         
		
		
		


        var MyOptionsForm = Ext.create('Ext.form.Panel', {

            name: 'my-options-form', overflowY: 'scroll',
            width: '100%',
            height: 600,
            baseParams: {
                articleId: me.article.get('id')
            },
            id: 'my-options-form',
            url: '{url controller="Pixelvariantlistingswf" action="saveFormOptionsConfig"}',
            bodyPadding: 10,
            autoScroll: true,
            defaults: {
                labelWidth: 240
            },
            items: [

                {    xtype: 'fieldset',

                    title: 'Standard-Einstellungen',
                    layout: 'fit', width: '100%',
                    defaults: {
                        labelWidth: 240,
                        flex: 1
                    },
                    items: [
                        {
                            xtype: 'checkbox',
                            name: 'pix_am_status',
                            fieldLabel: 'Aktivieren',
                            helpText: 'Aktivieren der Optionen beim Artikel',
                            inputValue: 1,
                            uncheckedValue: 0
                        },
                         
                    ]



                },
                      
            ],
            buttons: [
                {
                    text: 'Speichern',
                    id: 'multioptionsform-button',
                    cls: 'primary',
                    handler: function () {
                        var fp = this.ownerCt.ownerCt,
                            form = fp.getForm();
                        if (form.isValid()) {

                            form.submit({
                                waitMsg: 'Bitte warten...',
                                success: function (f, a) {
                                    Ext.Msg.alert('Success', 'Gespeichert');

                                    MyOptionsForm.getForm().load({
                                        url: '{url controller="Pixelvariantlistingswf" action="FormOptionsConfig"}',
                                        params: {
                                            articleId: me.article.get('id')
                                        },
                                        failure: function (form, action) {
                                            // Ext.Msg.alert("Load failed", action.result.errorMessage);
                                        }
                                    });
                                },
                                failure: function (f, a) {
                                    if (a.failureType === Ext.form.Action.CONNECT_FAILURE) {
                                        Ext.Msg.alert('Failure', 'Server reported:' + a.response.status + ' ' + a.response.statusText);
                                    }
                                    if (a.failureType === Ext.form.Action.SERVER_INVALID) {
                                        Ext.Msg.alert('Warning', a.result.errormsg);
                                    }
                                }
                            });
                        }
                    }
                },
                {
                    text: 'Speichern',
                    id: 'multioptionsform-button',
                    cls: 'primary',
                    handler: function () {
                        var fp = this.ownerCt.ownerCt,
                            form = fp.getForm();
                        if (form.isValid()) {

                            form.submit({
                                waitMsg: 'Bitte warten...',
                                success: function (f, a) {
                                    Ext.Msg.alert('Success', 'Gespeichert');

                                    MyOptionsForm.getForm().load({
                                        url: '{url controller="Pixelvariantlistingswf" action="FormOptionsConfig"}',
                                        params: {
                                            articleId: me.article.get('id')
                                        },
                                        failure: function (form, action) {
                                            // Ext.Msg.alert("Load failed", action.result.errorMessage);
                                        }
                                    });
                                },
                                failure: function (f, a) {
                                    if (a.failureType === Ext.form.Action.CONNECT_FAILURE) {
                                        Ext.Msg.alert('Failure', 'Server reported:' + a.response.status + ' ' + a.response.statusText);
                                    }
                                    if (a.failureType === Ext.form.Action.SERVER_INVALID) {
                                        Ext.Msg.alert('Warning', a.result.errormsg);
                                    }
                                }
                            });
                        }
                    }
                }
            ]
        });

        MyOptionsForm.getForm().load({
            url: '{url controller="Pixelvariantlistingswf" action="FormOptionsConfig"}',

            params: {
                articleId: me.article.get('id')
            },
            failure: function (form, action) {
                // Ext.Msg.alert("Load failed", action.result.errorMessage);
            }
        });


        return Ext.create('Ext.container.Container', {
            items: [ MyOptionsForm ],
            autoScroll: true,
            width: '100%',
            minHeight: 600,
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            name: 'moartikel-listing',
            title: 'Artikeleinstellungen'
        });


    },

                



});
//{/block}