/**
 * Copyright notice
 *
 * (c) 2009-2016 Net Inventors - Agentur für digitale Medien GmbH
 * All rights reserved
 *
 * This script is part of the Spirit-Project.
 * The Spirit-Project is property of the Net Inventors GmbH and
 * may not be used in projects not related to the Net Inventors
 * without explicit permission by the authors.
 *
 * PHP version 5
 *
 * @package    NetiFoundation
 * @subpackage NetiFoundation/Shopware.apps.NetiFoundation.view.form.media.List
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */
//{namespace name="backend/NetiFoundation/media"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundation.view.form.media.List', {
    'extend': 'Ext.panel.Panel',
    'cls': Ext.baseCSSPrefix + 'article-image-list',
    'style': 'background: #fff',
    'autoScroll': true,
    'snippets': {
        'title': '{s name=list-title}Assigned images{/s}',
        'comboBox': '{s name=list-combo_box}Images per page{/s}',
        'previewButton': '{s name=list-preview_button}Mark selected image as preview image{/s}',
        'removeButton': '{s name=list-remove_button}Remove selected image{/s}',
        'downloadButton': '{s name=list-download_button}Download selected image{/s}',
        'configButton': '{s name=list-config_button}Open configuration{/s}',
        'mainImage': '{s name=list-main_image}Preview{/s}',
        'sizes': {
            'small': '{s name=list-size_small}Small{/s}',
            'middle': '{s name=list-size_medium}Medium{/s}',
            'big': '{s name=list-size_large}Large{/s}'
        }
    },
    'dragOverCls': 'drag-over',
    'imageSize': 1,
    'sizes': [
        'small',
        'middle',
        'big'
    ],

    'initComponent': function () {
        var me = this;

        me.mediaStore = me.getMediaField().getStore();

        me.title = me.snippets.title;
        me.tbar = me.createActionToolbar();
        me.items = [{
            'xtype': 'container',
            'style': 'background: #fff',
            'autoScroll': true,
            'items': [
                me.getMediaView()
            ]
        }];
        me.registerEvents();
        me.callParent(arguments);
    },

    'registerEvents': function () {
        var me = this;

        me.addEvents(
            'mediaSelect',
            'mediaDeselect',
            'mediaMoved',
            'markPreviewImage',
            'removeImage',
            'openImageMapping',
            'download'
        );
    },

    'createMediaViewTemplate': function () {
        var me = this;

        return new Ext.XTemplate(
            '{literal}<tpl for=".">',
            '<tpl if="main===1">',
            '<div class="article-thumb-wrap main ' + me.sizes[me.imageSize] + '" >',
            '</tpl>',
            '<tpl if="main!=1">',
            '<div class="article-thumb-wrap ' + me.sizes[me.imageSize] + '" >',
            '</tpl>',

            // If the type is image, then show the image
            '<div class="thumb">',
            '<div class="inner-thumb"><img src="{literal}{thumbnail}{/literal}" /></div>',
            '<tpl if="main===1">',
            '<div class="preview"><span>' + me.snippets.mainImage + '</span></div>',
            '</tpl>',
            '<tpl if="hasConfig">',
            '<div class="mapping-config">&nbsp;</div>',
            '</tpl>',
            '</div>',
            '</div>',
            '</tpl>',
            '<div class="x-clear"></div>{/literal}'
        );
    },

    'getMediaView': function () {
        var me = this;

        return me.dataView || me.createMediaView();
    },

    'createMediaView': function () {
        var me = this, model;

        me.dataView = Ext.create('Ext.view.View', {
            'itemSelector': '.article-thumb-wrap',
            'name': 'image-listing',
            'emptyText': 'No Media found',
            'multiSelect': false,
            'padding': '10 10 20',
            'store': me.mediaStore,
            'tpl': me.createMediaViewTemplate()
        });

        me.dataView.getSelectionModel().on('select', function (dataViewModel, media) {
            me.fireEvent('mediaSelect', dataViewModel, media, me.previewButton, me.removeButton, me.configButton, me.downloadButton);
        });

        me.dataView.getSelectionModel().on('deselect', function (dataViewModel, media) {
            me.fireEvent('mediaDeselect', dataViewModel, media, me.previewButton, me.removeButton, me.configButton, me.downloadButton);
        });
        me.initDragAndDrop();

        return me.dataView;
    },

    'initDragAndDrop': function () {
        var me = this;

        me.dataView.on('afterrender', function (v) {
            me.dataView.dragZone = new Ext.dd.DragZone(v.getEl(), {
                'getDragData': function (e) {
                    //Use the DataView's own itemSelector to
                    //test if the mousedown is within one of the DataView's nodes.
                    var sourceEl = e.getTarget(v.itemSelector, 10);

                    //If the mousedown is within a DataView node, clone the node to produce
                    //a ddel element for use by the drag proxy. Also add application data
                    //to the returned data object.
                    if (sourceEl) {
                        var d = sourceEl.cloneNode(true);
                        d.id = Ext.id();

                        var result = {
                            'ddel': d,
                            'sourceEl': sourceEl,
                            'repairXY': Ext.fly(sourceEl).getXY(),
                            'sourceStore': v.store,
                            'draggedRecord': v.getRecord(sourceEl)
                        };
                        return result;
                    }
                },
                'getRepairXY': function () {
                    return this.dragData.repairXY;
                }
            });

            me.dataView.dropZone = new Ext.dd.DropZone(me.dataView.getEl(), {
                //If the mouse is over a grid row, return that node. This is
                //provided as the "target" parameter in all "onNodeXXXX" node event handling functions
                'getTargetFromEvent': function (e) {
                    return e.getTarget(me.dataView.itemSelector);
                },

                //On entry into a target node, highlight that node.
                'onNodeEnter': function (target, dd, e, data) {
                    var record = me.dataView.getRecord(target);
                    if (record !== data.draggedRecord) {
                        Ext.fly(target).addCls(me.dragOverCls);
                    }
                },

                //On exit from a target node, unhighlight that node.
                'onNodeOut': function (target, dd, e, data) {
                    Ext.fly(target).removeCls(me.dragOverCls);
                },

                //While over a target node, return the default drop allowed class which
                //places a "tick" icon into the drag proxy.
                'onNodeOver': function (target, dd, e, data) {
                    return (data.draggedRecord instanceof Ext.data.Model);
                },

                //On node drop we can interrogate the target to find the underlying
                //application object that is the real target of the dragged data.
                //In this case, it is a Record in the GridPanel's Store.
                //We can use the data set up by the DragZone's getDragData method to read
                //any data we decided to attach in the DragZone's getDragData method.
                'onNodeDrop': function (target, dd, e, data) {
                    var record = me.dataView.getRecord(target);
                    me.fireEvent('mediaMoved', me.mediaStore, data.draggedRecord, record)
                }
            });

        });
    },

    'createActionToolbar': function () {
        var me = this;

        //the size slider handles the displayed thumbnail size in the image listing.
        me.sizeSlider = Ext.create('Ext.slider.Single', {
            'width': 120,
            'value': 1,
            'animate': false,
            'fieldLabel': me.snippets.slider,
            'increment': 1,
            'minValue': 0,
            'maxValue': 2,
            'tipText': function (thumb) {
                return Ext.String.format('<b>[0]</b>', me.snippets.sizes[me.sizes[thumb.value]]);
            },
            'listeners': {
                'changecomplete': function (slider, newValue) {
                    me.imageSize = newValue;
                    me.dataView.tpl = me.createMediaViewTemplate();
                    me.dataView.refresh();
                }
            }
        });

        //the preview button, marks the selected image in the listing as preview.
        //the event will be handled in the media controller
        me.previewButton = Ext.create('Ext.button.Button', {
            'text': me.snippets.previewButton,
            'action': 'previewImage',
            'disabled': true,
            'iconCls': 'sprite-camera-lens',
            'handler': function () {
                me.fireEvent('markPreviewImage');
            }
        });

        //the remove button, removes the selected item from the image listing.
        me.removeButton = Ext.create('Ext.button.Button', {
            'text': me.snippets.removeButton,
            'action': 'removeImage',
            'disabled': true,
            'iconCls': 'sprite-minus-circle-frame',
            'handler': function () {
                me.fireEvent('removeImage');
            }
        });

        //the config button, opens the config window for the image mapping
        me.configButton = Ext.create('Ext.button.Button', {
            'text': me.snippets.configButton,
            'disabled': true,
            'iconCls': 'sprite-gear',
            'handler': function () {
                me.fireEvent('openImageMapping');
            }
        });

        me.downloadButton = Ext.create('Ext.button.Button', {
            'text': me.snippets.downloadButton,
            'disabled': true,
            'iconCls': 'sprite-download',
            'handler': function() {
                me.fireEvent('download');
            }
        });

        return Ext.create('Ext.toolbar.Toolbar', {
            items: [
                me.previewButton,
                {
                    'xtype': 'tbspacer',
                    'width': 12
                },
                me.removeButton,
                {
                    'xtype': 'tbspacer',
                    'width': 12
                },
                //me.configButton,
                me.downloadButton,
                '->',
                me.sizeSlider,
                {
                    'xtype': 'tbspacer',
                    'width': 12
                }
            ]
        });
    },

    'getMediaField': function () {
        var me = this;

        return me.mediaField;
    }
});
//{/block}
