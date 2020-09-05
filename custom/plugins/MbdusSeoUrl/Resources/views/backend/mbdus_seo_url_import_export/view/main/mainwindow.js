/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 *
 * @category   Shopware
 * @package    MbdusSeoUrlImportExport
 * @subpackage Main
 * @version    $Id$
 * @author shopware AG
 */

//{namespace name=backend/mbdus_seo_url_import_export/view/mainwindow}
//{block name="backend/mbdus_seo_url_import_export/view/main/mainwindow"}
Ext.define('Shopware.apps.MbdusSeoUrlImportExport.view.main.Mainwindow', {
    extend: 'Enlight.app.Window',
    alias : 'widget.mbdusseourlimportexport-main-mainwindow',
    width: 500,

    stateful: true,
    stateId: 'shopware-mbdusseourlimportexport-mainwindow',

    height: '50%',
    autoScroll: true,

    layout: {
        type: 'vbox',
        pack: 'start',
        align: 'stretch'
    },

    /**
     * Initializes the component and builds up the main interface
     *
     * @return void
     */
    initComponent: function() {
        var me = this;

        me.title = '{s name="mbdusSeoUrlImportExport"}Seo-Urls Import/Export{/s}';

        me.items = [
            me.getExportMiscForm(),
            me.getImportForm()
        ];

        me.callParent(arguments);
    },

    /**
     * @return [Ext.form.Panel]
     */
    getExportMiscForm: function() {
        var me = this;

        /* {if {acl_is_allowed privilege=export}} */
        var toolbar = Ext.create('Ext.toolbar.Toolbar', {
            dock: 'bottom',
            cls: 'shopware-toolbar',
            items: [ '->',
                {
                    text: '{s name="mbdusSeoExportExport"}Exportieren{/s}',
                    cls: 'primary',
                    formBind: true,
                    handler: function () {
                        var form = this.up('form').getForm();
                        if (!form.isValid()) {
                            return;
                        }

                        var values = form.getValues();
                        var url = '{url module=backend controller=MbdusSeoUrlImportExport action=exportSeoUrls}';

                        form.submit({
                            method: 'GET',
                            url: url
                        });
                    }
                }
            ]
        });
        /* {/if} */

        return Ext.create('Ext.form.Panel', {
            title: '{s name="mbdusTitleSeoExport"}Export Seo-Urls{/s}',
            standardSubmit: true,
            target: 'iframe',
            layout: 'anchor',
        /* {if {acl_is_allowed privilege=export}} */
            dockedItems: toolbar,
        /* {/if} */
            defaults: {
                anchor: '100%',
                labelWidth: 300
            },
            defaultType: 'textfield',
            items: [
               
            ]
        });
    },

    /**
     * @return [Ext.form.Panel]
     */
    getImportForm: function() {
        var me = this;

        var toolbar = Ext.create('Ext.toolbar.Toolbar', {
            dock: 'bottom',
            cls: 'shopware-toolbar',
        /* {if {acl_is_allowed privilege=import}} */
            items: [ '->',
                {
                    text: '{s name="mbdusSeoImportStart"}Importieren{/s}',
                    cls: 'primary',
                    formBind: true,
                    handler: function () {
                        var form = this.up('form').getForm();
                        if (!form.isValid()) {
                            return;
                        }

                        form.submit({
                            url: ' {url module=backend controller=MbdusSeoUrlImportExport action=importSeoUrls}',
                            waitMsg: '{s name=uploading}uploading...{/s}',
                            success: function (fp, o) {
                                Ext.Msg.alert('Result', o.result.message);
                            },
                            failure: function (fp, o) {
                                Ext.Msg.alert('Fehler', o.result.message);
                            }
                        });
                    }
                }
            ]
        /* {/if} */
        });

        return Ext.create('Ext.form.Panel', {
            xtype: 'form',
            title: '{s name="mbdusTitleSeoImport"}Import Seo-Urls{/s}',
            layout: 'anchor',
            dockedItems: toolbar,
            defaults: {
                anchor: '100%',
                labelWidth: 300
            },
            items: [
				{
				    xtype: 'filefield',
				    emptyText: '{s name=choose}Bitte auswählen{/s}',
				    buttonText:  '{s name=choose_button}Wählen{/s}',
				    name: 'file',
				    fieldLabel: '{s name=file}Datei{/s}',
				    allowBlank: false
				}
            ]
        });
    }
});
//{/block}
