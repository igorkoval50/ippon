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
 * @subpackage NetiFoundation/Shopware.apps.NetiFoundation.view.form.media.DropZone
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */
//{namespace name="backend/NetiFoundation/media"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundation.view.form.media.DropZone', {
    'extend': 'Ext.container.Container',
    'alias': 'widget.neti_foundation-media-field-drop-zone',
    'cls': Ext.baseCSSPrefix + 'article-image-drop-zone',
    'layout': 'anchor',
    'defaults': {
        'anchor': '100%'
    },
    'snippets': {
        'dropZone': '{s name=upload-drop_zone}Upload images via drag&drop{/s}'
    },
    'dropZoneConfig': {},
    'albumId': '',
    'uploadUrl': '',
    'initComponent': function () {
        var me = this;

        me.items = [
            me.getMediaDropZone()
        ];

        me.registerEvents();
        me.callParent(arguments);
    },

    'registerEvents': function () {
        this.addEvents(
            'addMedia'
        );
    },

    'getMediaDropZone': function () {
        var me = this;

        return me.mediaDropZone || me.createMediaDropZone()
    },

    'createMediaDropZone': function () {
        var me = this,
            defaultConfig = {
                requestURL: me.uploadUrl + '?albumID=' + me.albumId,
                showInput: false,
                padding: 0,
                checkSize: false,
                checkType: false,
                checkAmount: false,
                enablePreviewImage: false,
                dropZoneText: me.snippets.dropZone,
                height: 100
            };

        defaultConfig = Ext.apply(defaultConfig, me.dropZoneConfig);
        me.mediaDropZone = Ext.create('Shopware.app.FileUpload', defaultConfig);

        return me.mediaDropZone;
    }
});
//{/block}
