//{namespace name=backend/prems_emotion_cms_site/view/detail}
Ext.define('Shopware.apps.PremsEmotionCmsSite.view.detail.Base', {
    extend: 'Shopware.model.Container',
    padding: 20,

    positionData: [
        [ 0, '{s name=view/detail/position_above_site_description}{/s}' ],
        [ 1, '{s name=view/detail/position_below_site_description}{/s}' ],
        [ 2, '{s name=view/detail/position_own_block}{/s}' ]
    ],

    configure: function() {
        var me = this;

        return {
            controller: 'PremsEmotionCmsSite',
            associations: [ 'emotions', 'sites' ],
            fieldSets: [
                {
                    title: 'Basiseinstellungen',
                    fields: {
                        name: {
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