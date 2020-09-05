/**
 *
 */
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.components.model.Container', {
    'extend': 'Shopware.model.Container',
    'createOneColumnFieldSet': function (title, fields) {
        var me = this,
            items = [],
            fieldSet;

        Ext.Object.each(fields, function (key, config) {
            var field = me.createModelField(
                me.record,
                me.getFieldByName(me.record.fields.items, key),
                me.getConfig('fieldAlias'),
                config
            );

            items.push(field);
        });

        fieldSet = Ext.create('Ext.form.FieldSet', {
            'flex': 1,
            'padding': '10 20',
            'layout': {
                'type': 'vbox',
                'align': 'stretch'
            },
            'items': items,
            'title': title
        });

        return fieldSet;
    },
    'createItems': function () {
        var me = this, items = [], item, config,
            associations, fields, field, keys;

        if (!me.fireEvent(me.eventAlias + '-before-create-items', me, items)) {
            return false;
        }

        //iterate all defined field sets. If no field set configured, the component is used for none model fields.
        Ext.each(me.getConfig('fieldSets'), function (fieldSet) {

            //check for function configuration.
            if (Ext.isFunction(fieldSet)) {
                item = fieldSet.call(me, items, me.record.$className);
                if (item) items.push(item);
                return true;
            }

            fields = [];
            keys = [];

            //now check if the developer configured an offset of fields within the fields object.
            if (Object.keys(fieldSet.fields).length > 0) {
                keys = Object.keys(fieldSet.fields);

                //use only all field names if the only one field set is configured.
            } else if (me.getConfig('fieldSets').length <= 1) {
                keys = me.record.fields.keys;
            }

            //iterate all model field names and create a form field for each field.
            Ext.each(keys, function (key) {
                //check if a custom field config is configured.
                config = fieldSet.fields[key] || {};

                field = me.createModelField(
                    me.record,
                    me.getFieldByName(me.record.fields.items, key),
                    me.getConfig('fieldAlias'),
                    config
                );

                //check if the field was created successfully.
                if (field) fields.push(field);
            });

            switch (fieldSet.columns) {
                case 1:
                    item = me.createSingleColumnModelFieldSet(me.record.$className, fields, fieldSet);
                    break;

                default:
                    item = me.createModelFieldSet(me.record.$className, fields, fieldSet);
                    break;
            }
            items.push(item);
        });

        //get all record associations, which defined in the display config.
        associations = me.getAssociations(
            me.record.$className,
            {
                'associationKey': me.getConfig('associations')
            }
        );

        //the associations will be displayed within this component.
        Ext.each(associations, function (association) {

            //Important row! This call creates each association component which can be defined in the association array.
            item = me.createAssociationComponent(
                me.getComponentTypeOfAssociation(association),
                Ext.create(association.associatedName),
                me.getAssociationStore(me.record, association),
                association,
                me.record
            );

            //check if the component creation was canceled, or throws an exception
            if (item) {
                items.push(item);
                me.associationComponents[association.associationKey] = item;
            }
        });

        me.fireEvent(me.eventAlias + '-after-create-items', me, items);

        return items;
    },

    'createSingleColumnModelFieldSet': function (modelName, fields, customConfig) {
        var me = this,
            fieldSet,
            title = me.getModelName(modelName),
            customConfig = customConfig || {};

        if (customConfig.title) title = customConfig.title;

        if (customConfig.hasOwnProperty('title')) {
            title = customConfig.title;
        }

        fieldSet = Ext.create('Ext.form.FieldSet', Ext.apply({
            'flex': 1,
            'padding': '10 20',
            'layout': {
                'type': 'vbox',
                'align': 'stretch'
            },
            'items': fields,
            'title': title
        }, customConfig));

        return fieldSet;
    },

    'createModelFieldSet': function (modelName, fields, customConfig) {
        var me = this, fieldSet = null,
            title = me.getModelName(modelName),
            model = Ext.create(modelName), items = [], container;

        customConfig = customConfig || {};
        if (customConfig.title) title = customConfig.title;

        if (!me.fireEvent(me.eventAlias + '-before-model-field-set-created', me, fieldSet, items, model)) {
            return fieldSet;
        }

        if (me.getConfig('splitFields')) {
            //create a column container to display the columns in a two column layout
            container = Ext.create('Ext.container.Container', {
                columnWidth: 0.5,
                padding: '0 20 0 0',
                layout: 'anchor',
                items: fields.slice(0, Math.round(fields.length / 2))
            });
            items.push(container);

            container = Ext.create('Ext.container.Container', {
                columnWidth: 0.5,
                layout: 'anchor',
                items: fields.slice(Math.round(fields.length / 2))
            });
            items.push(container);

        } else {
            container = Ext.create('Ext.container.Container', {
                columnWidth: 1,
                layout: 'anchor',
                items: fields
            });
            items.push(container);
        }

        me.fireEvent(me.eventAlias + '-column-containers-created', me, fields, items, model);

        if (customConfig.hasOwnProperty('title')) {
            title = customConfig.title;
        }

        fieldSet = Ext.create('Ext.form.FieldSet', Ext.apply({
            flex: 1,
            padding: '10 20',
            layout: 'column',
            items: items,
            title: title
        }, customConfig));

        me.fireEvent(me.eventAlias + '-after-model-field-set-created', me, fieldSet, model);

        return fieldSet;
    },
});
//{/block}
