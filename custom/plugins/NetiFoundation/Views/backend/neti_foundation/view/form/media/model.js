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
 * @subpackage NetiFoundation/Shopware.apps.NetiFoundation.view.form.media.Model
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */
//{namespace name="backend/NetiFoundation/media"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundation.view.form.media.Model', {
    'extend': 'Ext.data.Model',
    'fields': [
        {
            'name': 'id',
            'type': 'int'
        },
        {
            'name': 'media',
            'type': 'int'
        },
        {
            'name': 'main',
            'type': 'int',
            'defaultValue': 2
        },
        {
            'name': 'position',
            'type': 'int'
        },
        {
            'name': 'extension',
            'type': 'string'
        },
        {
            'name': 'path',
            'type': 'string'
        },
        {
            name: 'original',
            type: 'string'
        },
        {
            name: 'thumbnail',
            type: 'string'
        }
    ]
});
//{/block}
