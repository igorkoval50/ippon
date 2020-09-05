//

//{namespace name="backend/advisor/main"}
//{block name="backend/advisor/view/details/questions/property"}
Ext.define('Shopware.apps.Advisor.view.details.questions.Property', {
    extend: 'Shopware.apps.Advisor.view.details.questions.AbstractQuestion',

    label: '{s name="filter_propertyLabel"}Property{/s}',

    snippets: {
        showAllCheckbox: {
            fieldLabel: '{s name="show_all_properties_label"}Show all{/s}',
            helpText: '{s name="show_all_properties_helpText"}If this option is active, properties that are not available in the selected product stream are also displayed.{/s}'
        }
    },

    /**
     * @overwrite
     *
     * @returns { string }
     */
    getKey: function () {
        return 'property'
    },

    /**
     * @overwrite
     *
     * @returns { string }
     */
    getLabel: function () {
        return this.label;
    },

    /**
     * @overwrite
     *
     * @param { Shopware.apps.Advisor.model.Advisor } advisor
     * @param { Shopware.apps.Advisor.model.Question } question
     *
     * @returns { *[] }
     */
    createQuestion: function (advisor, question) {
        var me = this;

        question.set('type', me.getKey());

        me.answerGrid = Ext.create('Shopware.apps.Advisor.view.details.ui.AnswerGrid', {
            advisor: advisor,
            question: question,
            refreshGridData: function (advisor, question, store) {
                me.answerGrid.__proto__.refreshGridData.apply(me.answerGrid, arguments);
                me.refreshProperties(advisor, question);
            }
        });

        me.propertySelection = Ext.create('Shopware.apps.Advisor.view.details.ui.PropertySelection', {
            advisor: advisor,
            question: question,
            answerGrid: me.answerGrid
        });

        me.answerGrid.refreshGridData(advisor, question, question.getAnswers());

        me.showAllCheckbox = Ext.create('Ext.form.field.Checkbox', {
            labelWidth: 150,
            inputValue: true,
            uncheckedValue: false,
            fieldLabel: me.snippets.showAllCheckbox.fieldLabel,
            helpText: me.snippets.showAllCheckbox.helpText,
            name: 'showAllProperties',
            advisor: advisor,
            question: question,
            listeners: {
                change: Ext.bind(me.onChangeShowAll, me),
            }
        });

        return [
            me.showAllCheckbox,
            me.propertySelection,
            me.answerGrid
        ]
    },

    onChangeShowAll: function (checkBox, value) {
        var me = this;

        me.propertySelection.comboBox.store.getProxy().extraParams = {
            'showAllProperties': value
        };

        checkBox.question.set('showAllProperties', value);
        me.propertySelection.comboBox.store.load();
        me.refreshProperties(checkBox.advisor, checkBox.question);
    },

    /**
     * @param layout
     */
    updateQuestionViewData: function (layout) {
        var me = this;

        me.answerGrid.reconfigureGrid(layout);
    },

    /**
     * @param { Shopware.apps.Advisor.model.Advisor } advisor
     * @param { Shopware.apps.Advisor.model.Question } question
     */
    refreshProperties: function (advisor, question) {
        var me = this,
            store;

        if (!question.get('configuration')) {
            return;
        }

        store = Ext.create('Ext.data.Store', {
            model: 'Shopware.apps.Advisor.model.Answer',
            proxy: {
                type: 'ajax',
                url: '{url controller=Advisor action=getPropertyValuesAjax}',
                extraParams: {
                    streamId: advisor.get('streamId'),
                    propertyId: question.get('configuration'),
                    showAllProperties: question.get('showAllProperties')
                },
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        }).load();

        me.answerGrid.setPossibleAnswers(store);
    }
});
//{/block}
