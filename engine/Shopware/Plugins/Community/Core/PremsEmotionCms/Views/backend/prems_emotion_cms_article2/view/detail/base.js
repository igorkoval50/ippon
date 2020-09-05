//{namespace name=backend/prems_emotion_cms/article/view/detail}
Ext.define('Shopware.apps.PremsEmotionCmsArticle2.view.detail.Base', {
    extend: 'Shopware.model.Container',
    padding: 20,

    positionData: [
        [ 0, '{s name=view/detail/position_above_site_description}{/s}' ],
        [ 1, '{s name=view/detail/position_below_site_description}{/s}' ],
        [ 2, '{s name=view/detail/position_own_block}{/s}' ]
    ],

    beforeDefaultData: [
        [ 0, '{s name=view/detail/before_default_emotions}{/s}' ],
        [ 1, '{s name=view/detail/after_default_emotions}{/s}' ],
    ],

    configure: function() {
        var me = this;

        return {
            controller: 'PremsEmotionCmsArticle2',
            associations: [
                'emotions', 'articles'
            ],
            fieldSets: [
                {
                    title: 'Basiseinstellungen',
                    fields: {
                        name: {
                            allowBlank: false
                        },
                        beforeDefault: {
                            xtype: 'combobox',
                            fieldLabel: 'Anzeigeposition Einkaufswelten',
                            store: new Ext.data.SimpleStore({
                                fields: [ 'id', 'text', 'tip' ], data: this.beforeDefaultData
                            }),
                            valueField: 'id',
                            displayField: 'text',
                            mode: 'local',
                            editable: false,
                            allowBlank: false
                        },
                        position: {
                            xtype: 'combobox',
                            fieldLabel: 'Position',
                            store: new Ext.data.SimpleStore({
                                fields: [ 'id', 'text', 'tip' ], data: this.positionData
                            }),
                            valueField: 'id',
                            displayField: 'text',
                            mode: 'local',
                            editable: false,
                            allowBlank: false
                        },
                    }
                }
            ]
        };
    },
});