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
 * @subpackage NetiFoundation/Shopware.apps.NetiFoundation.view.form.dateTime.Picker
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */

//{namespace name="plugins/neti_foundation/backend/date_time"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundation.view.form.dateTime.Picker', {
    'extend': 'Ext.picker.Date',
    'alias': 'widget.datetimepicker',
    'requires': [
        'Ext.picker.Date',
        'Ext.slider.Single',
        'Ext.form.field.Time',
        'Ext.form.Label'
    ],

    'todayText': '{s name="today_text"}Today{/s}',
    'hourText': '{s name="hour_text"}Hour{/s}',
    'minuteText': '{s name="minute_text"}Minute{/s}',

    'initEvents': function () {
        var me = this,
            eDate = Ext.Date,
            day = eDate.DAY;

        Ext.apply(me.keyNavConfig, {
            up: function (e) {
                if (e.ctrlKey) {
                    if (e.shiftKey) {
                        me.minuteSlider.setValue(me.minuteSlider.getValue() + 1);
                    } else {
                        me.showNextYear();
                    }
                } else {
                    if (e.shiftKey) {
                        me.hourSlider.setValue(me.hourSlider.getValue() + 1);
                    } else {
                        me.update(eDate.add(me.activeDate, day, -7));
                    }
                }
            },

            down: function (e) {
                if (e.ctrlKey) {
                    if (e.shiftKey) {
                        me.minuteSlider.setValue(me.minuteSlider.getValue() - 1);
                    } else {
                        me.showPrevYear();
                    }
                } else {
                    if (e.shiftKey) {
                        me.hourSlider.setValue(me.hourSlider.getValue() - 1);
                    } else {
                        me.update(eDate.add(me.activeDate, day, 7));
                    }
                }
            }
        });
        me.callParent();
    },
    'initComponent': function () {
        var me = this,
            dtAux = me.value ? new Date(me.value) : new Date();

        dtAux.setSeconds(0);

        me.timeFormat = ~me.format.indexOf("h") ? 'h' : 'H';
        me.hourSlider = new Ext.slider.Single({
            fieldLabel: me.hourText,
            labelAlign: 'top',
            labelSeparator: ' ',
            padding: '0 0 10 17',
            focusable: false,
            value: 0,
            minValue: 0,
            maxValue: 23,
            vertical: true,
            listeners: {
                change: me.changeTimeValue,
                scope: me
            }
        });

        me.minuteSlider = new Ext.slider.Single({
            fieldLabel: me.minuteText,
            labelAlign: 'top',
            labelSeparator: ' ',
            padding: '0 10 10 0',
            focusable: false,
            value: 0,
            increment: 1,
            minValue: 0,
            maxValue: 59,
            vertical: true,
            listeners: {
                change: me.changeTimeValue,
                scope: me
            }
        });

        me.callParent();
        me.setValue(new Date(dtAux));
    },
    'afterRender': function () {
        var me = this,
            el = me.el;

        me.timePicker = Ext.create('Ext.panel.Panel', {
            'layout': {
                'type': 'hbox',
                'align': 'stretch'
            },
            'cls': 'datetimepicker',
            'border': false,
            'defaults': {
                'flex': 1
            },
            'width': 130,
            'floating': true,
            'onMouseDown': function (e) {
                e.preventDefault();
            },
            showBy: function(cmp, pos, off) {
                var me = this;

                if (me.floating && cmp) {
                    me.show();

                    // Show may have been vetoed
                    if (me.rendered && !me.hidden) {
                        // Align to Component or Element using alignTo because normal show methods
                        // are container-relative, and we must align to the requested element or
                        // Component:
                        me.alignTo(cmp, pos || me.defaultAlign, off);
                    }
                }
                return me;
            },
            'dockedItems': [{
                'xtype': 'toolbar',
                'dock': 'top',
                'ui': 'default',
                'items': [
                    '->', {
                        'xtype': 'label',
                        'text': me.timeFormat == 'h' ? '12:00 AM' : '00:00'
                    },
                    '->'
                ]
            }],
            'items': [me.hourSlider, me.minuteSlider]
        });

        me.callParent();
    },
    'handleTabClick': function (e) {
        this.handleDateClick(e, this.activeCell.firstChild, true);
    },
    'getSelectedDate': function (date) {
        var me = this,
            t = Ext.Date.clearTime(date, true).getTime(),
            cells = me.cells,
            cls = me.selectedCls,
            cellItems = cells.elements,
            cLen = cellItems.length,
            cell, c;

        cells.removeCls(cls);

        for (c = 0; c < cLen; c++) {
            cell = cellItems[c].firstChild;
            if (cell.dateValue === t) {
                return cell;
            }
        }
        return null;
    },
    'changeTimeValue': function (slider, e, eOpts) {
        var me = this,
            label = me.timePicker.down('label'),
            hourPrefix = '',
            minutePrefix = me.minuteSlider.getValue() < 10 ? '0' : '',
            timeSufix = '',
            hourDisplay = me.hourSlider.getValue(),
            auxDate = new Date();

        if (me.timeFormat == 'h') {
            timeSufix = me.hourSlider.getValue() < 12 ? ' AM' : ' PM';
            hourDisplay = me.hourSlider.getValue() < 13 ? hourDisplay : hourDisplay - 12;
            hourDisplay = hourDisplay || '12';
        }
        hourPrefix = hourDisplay < 10 ? '0' : '';

        label.setText(hourPrefix + hourDisplay + ':' + minutePrefix + me.minuteSlider.getValue() + timeSufix);

        if (me.pickerField && me.pickerField.getValue()) {
            me.pickerField.setValue(new Date(me.pickerField.getValue().setHours(me.hourSlider.getValue(), me.minuteSlider.getValue())));
        }
    },
    'onShow': function () {
        var me = this;
        me.showTimePicker();
        me.callParent();
    },
    'showTimePicker': function () {
        var me = this,
            el = me.el;
        Ext.defer(function () {
            var body = Ext.getBody(),
                bodyWidth = body.getViewSize().width,
                alignTo = (bodyWidth < (el.getX() + el.getWidth() + 140)) ? 'tl' : 'tr',
                xPos = alignTo == 'tl' ? -135 : 5,
                backgroundColor, toolbar;

            me.timePicker.setHeight(el.getHeight());
            me.timePicker.showBy(me, alignTo, [xPos, 0]);

            toolbar = me.timePicker.down('toolbar').getEl();
            backgroundColor = toolbar.getStyle('background-color');
            if (backgroundColor == 'transparent') {
                toolbar.setStyle('background-color', toolbar.getStyle('border-color'));
            }
        }, 1);
    },
    'onHide': function () {
        var me = this;
        me.timePicker.hide();
        me.callParent();
    },
    'beforeDestroy': function () {
        var me = this;

        if (me.rendered) {
            Ext.destroy(
                me.timePicker,
                me.minuteSlider,
                me.hourSlider
            );
        }
        me.callParent();
    },
    'setValue': function (value) {
        value.setSeconds(0);
        this.value = new Date(value);
        return this.update(this.value);
    },
    'selectToday': function () {
        var me = this,
            btn = me.todayBtn,
            handler = me.handler,
            auxDate = new Date;

        if (btn && !btn.disabled) {
            me.setValue(new Date(auxDate.setSeconds(0)));
            me.fireEvent('select', me, me.value);
            if (handler) {
                handler.call(me.scope || me, me, me.value);
            }
            me.onSelect();
        }
        return me;
    },
    'handleDateClick': function (e, t, /*private*/ blockStopEvent) {
        var me = this,
            handler = me.handler,
            hourSet = me.timePicker.items.items[0].getValue(),
            minuteSet = me.timePicker.items.items[1].getValue(),
            auxDate = new Date(t.dateValue);

        if (blockStopEvent !== true) {
            e.stopEvent();
        }

        if (!me.disabled && t.dateValue && !Ext.fly(t.parentNode).hasCls(me.disabledCellCls)) {
            me.doCancelFocus = me.focusOnSelect === false;
            auxDate.setHours(hourSet, minuteSet, 0);
            me.setValue(new Date(auxDate));
            delete me.doCancelFocus;
            me.fireEvent('select', me, me.value);
            if (handler) {
                handler.call(me.scope || me, me, me.value);
            }

            me.onSelect();
        }
    },
    'selectedUpdate': function (date) {
        var me = this,
            dateOnly = Ext.Date.clearTime(date, true),
            currentDate = (me.pickerField && me.pickerField.getValue()) || new Date();

        this.callParent([dateOnly]);

        if (currentDate) {
            Ext.defer(function () {
                me.hourSlider.setValue(currentDate.getHours());
                me.minuteSlider.setValue(currentDate.getMinutes());
            }, 10);

        }

    },
    'fullUpdate': function (date) {
        var me = this,
            dateOnly = Ext.Date.clearTime(date, true),
            currentDate = (me.pickerField && me.pickerField.getValue()) || new Date();

        this.callParent([dateOnly]);

        if (currentDate) {
            Ext.defer(function () {
                me.hourSlider.setValue(currentDate.getHours());
                me.minuteSlider.setValue(currentDate.getMinutes());
            }, 10);

        }

    }
});
//{/block}
