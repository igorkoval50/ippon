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
 * @subpackage NetiFoundation/Shopware.apps.NetiFoundation.view.form.dateTime.Field
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */

//{namespace name="plugins/neti_foundation/backend/date_time"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundation.view.form.dateTime.Field', {
    'extend': 'Ext.form.field.Date',
    'alias': 'widget.datetimefield',
    'requires': [
        'Shopware.apps.NetiFoundation.view.form.dateTime.Picker'
    ],

    'format': "d.m.Y H:i",
    'altFormats': "m/d/Y H:i:s|c|m/d/Y H:i",

    'mimicBlur': function (e) {
        var me = this,
            picker = me.picker;

        // ignore mousedown events within the picker element
        if (!picker || !e.within(picker.el, false, true) && !e.within(picker.timePicker.el, false, true)) {
            me.callParent(arguments);
        }
    },
    'triggerBlur': function () {
        return false;
    },
    'collapseIf': function (e) {
        var me = this,
            picker = me.picker;

        if (picker.timePicker && !e.within(picker.timePicker.el, false, true)) {
            me.callParent([e]);
        }
    },
    'createPicker': function () {
        var me = this,
            format = Ext.String.format,
            parentPicker = this.callParent(),
            o = {};
        for (var key in parentPicker) {
            if (parentPicker.hasOwnProperty(key) && parentPicker[key]) {
                o[key] = parentPicker[key]
            }
        }

        return new Shopware.apps.NetiFoundation.view.form.dateTime.Picker(o);
    }//,
    //'getRefItems': function() {
    //    var me = this,
    //        result = me.callParent();
    //
    //    if (me.picker.timePicker){
    //        result.push(me.picker.timePicker);
    //    }
    //
    //    return result;
    //}
});
//{/block}
