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
 * @subpackage NetiFoundation/Shopware.apps.NetiFoundation.view.form.media.Field
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */
//{namespace name="backend/NetiFoundation/media"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundation.view.form.media.Field', {
    'extend': 'Ext.panel.Panel',
    'alias': 'widget.neti_foundation-media-field',
    'mixins': {
        'field': 'Ext.form.field.Field'
    },
    'flex': 1,
    'layout': {
        'type': 'vbox',
        'align': 'stretch',
        'pack': 'start'
    },
    'albumId': '',
    'uploadAlbumId': '',
    'uploadUrl': '{url controller="mediaManager" action="upload"}',
    'allowBlank': true,

    'initComponent': function () {
        var me = this;

        if (!me.store) {
            me.store = me.createStore();
        }

        me.items = me.createItems();

        me.addCls(Ext.baseCSSPrefix + 'neti-foundation-media-field');

        //me.control({
        //    'article-detail-window article-image-list': {
        //        mediaSelect: me.onSelectMedia,
        //        mediaDeselect: me.onDeselectMedia,
        //        mediaMoved: me.onMediaMoved,
        //        markPreviewImage: me.onMarkPreviewImage,
        //        removeImage: me.onRemoveImage,
        //        openImageMapping: me.onOpenImageMapping
        //    },
        //    'article-image-mapping-window': {
        //        displayNewRuleWindow: me.onDisplayNewRuleWindow,
        //        saveMapping: me.onSaveMapping,
        //        cancel: me.onMappingCancel
        //    },
        //    'article-image-rule-window': {
        //        nodeCheck: me.onNodeCheck,
        //        createImageMapping: me.onCreateImageMapping
        //    },
        //    'article-image-drop-zone html5fileupload': {
        //        fileUploaded: me.onFileUploaded
        //    },
        //    'article-detail-window article-image-info': {
        //        download: me.onDownload,
        //        saveImageSettings: me.onSaveImageSettings,
        //        translateSettings: me.onTranslateSettings
        //    },
        //    'article-detail-window article-image-upload mediaselectionfield': {
        //        selectMedia: me.onMediaAdded
        //    },
        //    'article-detail-window article-image-upload': {
        //        mediaUpload: me.onMediaUpload
        //    }
        //});

        me.getMediaUpload().addListener(
            'mediaselectionfield-selectMedia',
            me.onMediaAdded,
            me
        );

        me.getMediaUpload().addListener(
            'dropzone-fileUploaded',
            me.onFileUploaded,
            me
        );

        me.getMediaUpload().addListener(
            'mediaUpload',
            me.onMediaUpload,
            me
        );

        me.getMediaList().addListener(
            'mediaSelect',
            me.onSelectMedia,
            me
        );

        me.getMediaList().addListener(
            'mediaDeselect',
            me.onDeselectMedia,
            me
        );

        me.getMediaList().addListener(
            'markPreviewImage',
            me.onMarkPreviewImage,
            me
        );

        me.getMediaList().addListener(
            'removeImage',
            me.onRemoveImage,
            me
        );

        me.getMediaList().addListener(
            'mediaMoved',
            me.onMediaMoved,
            me
        );

        me.getMediaList().addListener(
            'download',
            me.onDownload,
            me
        );

        me.callParent(arguments);
        me.initField();
    },

    'createItems': function () {
        var me = this;

        return [
            me.getMediaUpload(),
            me.getMediaList()
        ];
    },

    'onDownload': function () {
        var me = this,
            mediaList = me.getMediaList(),
            selected = null,
            selectionModel = mediaList.getMediaView().getSelectionModel();

        if (selectionModel && selectionModel.selected && selectionModel.selected.first()) {
            selected = selectionModel.selected.first();
        }

        if (!(selected instanceof Ext.data.Model)) {
            return false;
        }

        window.open(selected.get('original'), selected.get('name'), 'width=1024,height=768');
    },

    'onFileUploaded': function (response) {
        var me = this,
            store = me.getStore(),
            operation = Ext.decode(response.responseText);

        if (operation.success === true) {
            var media = Ext.create('Shopware.apps.NetiFoundation.view.form.media.Model', operation.data);
            media.set('path', operation.data.name);
            media.set('original', operation.data.path);
            media.set('thumbnail', operation.data.path);
            media.set('main', 2);
            media.set('media', operation.data.id);

            if (store.getCount() === 0) {
                media.set('main', 1);
            }
            media.set('id', 0);
            store.add(media);
        }
    },

    'onMediaUpload': function (field) {
        var me = this,
            dropZone = me.getMediaUpload().getDropZone(),
            form;

        if (Ext.isIE || Ext.isSafari) {
            form = field.ownerCt;
            form.submit({
                success: function () {
                    Shopware.Notification.createGrowlMessage(me.snippets.growlMessage, me.snippets.upload.text);
                }
            });
        } else {
            me.uploadMedia(field, dropZone);
        }
    },

    'uploadMedia': function (field, dropZone) {
        var fileField = field.getEl().down('input[type=file]').dom;

        dropZone.mediaDropZone.iterateFiles(fileField.files);
    },

    'onMediaAdded': function (dropZone, images, selModel) {
        var me = this,
            store = me.getStore();

        if (images.length === 0) {
            return true;
        }

        Ext.each(images, function (item) {
            var media = Ext.create('Shopware.apps.NetiFoundation.view.form.media.Model', item.data);
            media.set('path', item.get('name'));
            media.set('main', 2);
            media.set('media', item.get('id'));

            if (store.getCount() === 0) {
                media.set('main', 1);
            }
            media.set('id', 0);
            store.add(media);
        });
    },

    'onSelectMedia': function (dataViewModel, media, previewButton, removeButton, configButton, downloadButton) {
        var me = this;

        me.disableImageButtons(dataViewModel, previewButton, removeButton, configButton, downloadButton);
    },

    'onDeselectMedia': function (dataViewModel, media, previewButton, removeButton, configButton, downloadButton) {
        var me = this;

        me.disableImageButtons(dataViewModel, previewButton, removeButton, configButton, downloadButton);
    },

    'disableImageButtons': function (dataViewModel, previewButton, removeButton, configButton, downloadButton) {
        var me = this, selected = null,
            disabled = (dataViewModel.selected.length === 0);

        if (dataViewModel.selected && dataViewModel.selected.first()) {
            selected = dataViewModel.selected.first();
        }

        removeButton.setDisabled(disabled);
        previewButton.setDisabled(disabled);
        downloadButton.setDisabled(disabled);

        if (!selected || !selected.get('id') > 0) {
            configButton.setDisabled(true);
        } else {
            configButton.setDisabled(disabled);
        }

        if (!disabled) {
            previewButton.setDisabled(selected.get('main') === 1);
        }
    },

    'onMarkPreviewImage': function () {
        var me = this,
            mediaList = me.getMediaList(),
            store = me.getStore(),
            selectionModel = mediaList.getMediaView().getSelectionModel(),
            selected = null;

        if (selectionModel && selectionModel.selected && selectionModel.selected.first()) {
            selected = selectionModel.selected.first();
        }

        if (!(selected instanceof Ext.data.Model)) {
            return false;
        }

        store.each(function (item) {
            item.set('main', 2);
        });
        selected.set('main', 1);
    },

    'onRemoveImage': function () {
        var me = this,
            mediaList = me.getMediaList(),
            store = me.getStore(),
            changeMain,
            selected = null,
            selectionModel = mediaList.getMediaView().getSelectionModel(),
            next;

        if (selectionModel && selectionModel.selected && selectionModel.selected.first()) {
            selected = selectionModel.selected.first();
        }

        if (!(selected instanceof Ext.data.Model)) {
            return false;
        }
        changeMain = (selected.get('main') === 1);

        store.remove(selected);
        if (!changeMain) {
            return true;
        }

        next = store.getAt(0);
        if (next instanceof Ext.data.Model) {
            next.set('main', 1);
        }
    },

    'onMediaMoved': function (mediaStore, draggedRecord, targetRecord) {
        var me = this, index, indexOfDragged;

        if (!mediaStore instanceof Ext.data.Store
            || !draggedRecord instanceof Ext.data.Model
            || !targetRecord instanceof Ext.data.Model) {
            return false;
        }
        index = mediaStore.indexOf(targetRecord);
        indexOfDragged = mediaStore.indexOf(draggedRecord);
        if (index > indexOfDragged) {
            index--;
        }
        mediaStore.remove(draggedRecord);
        mediaStore.insert(index, draggedRecord);

        return true;
    },

    'createStore': function () {
        var me = this;

        return Ext.create('Ext.data.Store', {
            'autoDestroy': true,
            'autoSync': true,
            'model': 'Shopware.apps.NetiFoundation.view.form.media.Model',
            'proxy': {
                'type': 'memory'
            },
            'data': []
        });
    },

    'getStore': function () {
        var me = this;

        return me.store;
    },

    'getMediaUpload': function () {
        var me = this;

        return me.mediaUpload || me.createMediaUpload();
    },

    'createMediaUpload': function () {
        var me = this;

        me.mediaUpload = Ext.create('Shopware.apps.NetiFoundation.view.form.media.Upload', {
            'mediaField': me,
            'uploadUrl': me.uploadUrl,
            'albumId': me.uploadAlbumId ? me.uploadAlbumId : me.albumId,
            'margin': 10,
            'flex': 1,
            'autoScroll': true
        });

        return me.mediaUpload;
    },

    'getMediaList': function () {
        var me = this;

        return me.mediaList || me.createMediaList();
    },

    'createMediaList': function () {
        var me = this;

        me.mediaList = Ext.create('Shopware.apps.NetiFoundation.view.form.media.List', {
            'mediaField': me,
            'albumId': me.albumId,
            'margin': '0 10 10',
            'flex': 1
        });

        return me.mediaList;
    },

    'getValue': function () {
        var me = this,
            values = [],
            store = me.getStore();

        if (store.data) {
            store.each(function (model) {
                values.push(model.getData());
            });
        }

        return values;
    },

    'setValue': function (values) {
        var me = this,
            store = me.getStore(),
            addValues = [];

        store.addListener(
            'clear',
            function () {
                if (Ext.isArray(values)) {
                    Ext.each(values, function (item) {
                        if (item.isModel) {
                            addValues.push(item.getData());
                        } else {
                            addValues.push(item);
                        }
                    });
                    store.add(addValues);
                }
            },
            store,
            {
                'single': true
            }
        );

        store.removeAll();
    },

    'getSubmitValue': function () {
        var me = this;

        return me.getValue();
    },

    'getSubmitData': function () {
        var me = this,
            result = {};

        result[me.getName()] = me.getValue();

        return result;
    },

    'getModelData': function () {
        var me = this;

        return me.getValue();
    },

    'isValid': function () {
        var me = this;

        return me.allowBlank || me.getStore().getCount() > 0;
    }
});
//{/block}
