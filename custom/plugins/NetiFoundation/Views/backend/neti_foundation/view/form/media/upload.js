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
 * @subpackage NetiFoundation/Shopware.apps.NetiFoundation.view.form.media.Upload
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */
//{namespace name="backend/NetiFoundation/media"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundation.view.form.media.Upload', {
    'extend': 'Ext.panel.Panel',
    'snippets': {
        'title': '{s name=upload-title}Upload media{/s}',
        'upload': '{s name=upload-upload}Upload media{/s}',
        'mediaSelection': '{s name=upload-select}Select media{/s}'
    },
    'albumId': '',
    'uploadUrl': '',

    'initComponent': function () {
        var me = this;
        me.items = [
            me.getUploadFields(),
            me.getDropZone()
        ];
        me.registerEvents();
        me.callParent(arguments);
    },

    'registerEvents': function () {
        var me = this;

        me.addEvents(
            'mediaUpload'
        );

        me.relayEvents(
            me.getMediaSelection(),
            [
                'renderMediaManagerButton',
                'beforeOpenMediaManager',
                'afterOpenMediaManager',
                'selectMedia'
            ],
            'mediaselectionfield-'
        );

        me.relayEvents(
            me.getDropZone().getMediaDropZone(),
            [
                'fileUploaded'
            ],
            'dropzone-'
        );
    },

    'getUploadFields': function () {
        var me = this;

        return me.uploadFields || me.createUploadFields();
    },

    'createUploadFields': function () {
        var me = this,
            form;

        me.uploadFields = Ext.create('Ext.container.Container', {
            'layout': 'column',
            'margin': '20 0',
            'defaults': {
                'columnWidth': 0.5
            },
            'items': [
                me.getFileUploadField(),
                me.getMediaSelection()
            ]
        });

        return me.uploadFields;
    },

    'getAllowedExtensions': function () {
        return [
            'gif',
            'png',
            'jpeg',
            'jpg',
            'swf'
        ]
    },

    'getExtensionErrorCallback': function () {
        return 'onExtensionError';
    },

    'getFileUploadField': function () {
        var me = this;

        return me.fileUploadField || me.createFileUploadField();
    },

    'createFileUploadField': function () {
        var me = this;

        me.fileUploadField = Ext.create('Ext.form.field.File', {
            'buttonOnly': false,
            'labelWidth': 100,
            'anchor': '100%',
            'margin': '0 10 0 0',
            'name': 'fileId',
            'buttonText': me.snippets.upload,
            'listeners': {
                'scope': this,
                'afterrender': function (btn) {
                    btn.fileInputEl.dom.multiple = true;
                },
                'change': function (field) {
                    me.fireEvent('mediaUpload', field)
                }
            },
            'buttonConfig': {
                'iconCls': 'sprite-inbox-upload',
                'cls': 'small secondary'
            }
        });

        if (Ext.isIE || Ext.isSafari) {
            form = Ext.create('Ext.form.Panel', {
                'unstyled': true,
                'border': 0,
                'bodyBorder': 0,
                'style': 'background: transparent',
                'bodyStyle': 'background: transparent',
                'url': me.uploadUrl + '?albumID=' + me.albumId,
                'items': [
                    me.fileUploadField
                ]
            });
            me.fileUploadField = form;
        }

        return me.fileUploadField;
    },

    'getMediaSelection': function () {
        var me = this;

        return me.mediaSelection || me.createMediaSelection();
    },

    'createMediaSelection': function () {
        var me = this;

        me.mediaSelection = Ext.create('Shopware.MediaManager.MediaSelection', {
            'fieldLabel': me.snippets.mediaSelection,
            'name': 'media-manager-selection',
            'multiSelect': true,
            'anchor': '100%',
            'buttonText': me.snippets.mediaSelection,
            'buttonConfig': {
                'width': 150
            },
            'albumId': me.albumId,
            'allowBlank': true,
            'validTypes': me.getAllowedExtensions(),
            'validTypeErrorFunction': me.getExtensionErrorCallback()
        });

        return me.mediaSelection;
    },

    'getDropZone': function () {
        var me = this;

        return me.dropZone || me.createDropZone();
    },

    'createDropZone': function () {
        var me = this;

        me.dropZone = Ext.create('Shopware.apps.NetiFoundation.view.form.media.DropZone', {
            'anchor': '100%',
            'albumId': me.albumId,
            'uploadUrl': me.uploadUrl,
            'dropZoneConfig': {
                'hideOnLegacy': true,
                'focusable': false
            }
        });

        return me.dropZone;
    },

    'getMediaField': function () {
        var me = this;

        return me.mediaField;
    }
});
//{/block}
