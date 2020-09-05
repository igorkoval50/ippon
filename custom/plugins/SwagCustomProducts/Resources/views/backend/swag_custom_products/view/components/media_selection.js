//

// {namespace name="backend/swag_custom_products/option/fields"}
// {block name="backend/swag_custom_products/components/mediaSelection"}
Ext.define('Shopware.apps.SwagCustomProducts.view.components.MediaSelection', {
    extend: 'Shopware.form.field.Media',

    height: null,

    snippets: {
        selectMessage: '{s name="media/select/no/image/message"}Please select an image{/s}'
    },

    /**
     * Overwrite the init method to set a new layout
     *
     * @overwrite
     */
    initComponent: function () {
        var me = this;

        me.layout = 'fit';

        me.callParent();
    },

    /**
     * Overwrite to set a default border.
     * This prevent a "layout crack" on set a border.
     *
     * @overwrite
     */
    afterRender: function () {
        var me = this,
            child;

        me.callParent(arguments);

        child = me.child();
        child.getEl().setStyle('border', '1px solid #FFF');
        child.doLayout();
    },

    /**
     * @overwrite
     *
     * @param { Ext.button.Button } button
     * @param { Enlight.app.Window } window
     * @param { Array } selection
     */
    onSelectMedia: function (button, window, selection) {
        var me = this,
            record = selection[0];

        me.setMedia(me, record);

        me.callParent(arguments);

        if (me.isValid()) {
            me.hideIsInvalid();
        }
    },

    /**
     * Overwrite to add a new Ext.container Container with a new element to show that
     * it is required to select a image.
     *
     * @overwrite
     * @returns { Ext.container.Container }
     */
    createItems: function () {
        var me = this,
            items = me.callParent(arguments);

        me.errorMessageContainer = me.createErrorMessageContainer();

        items[0].add(me.errorMessageContainer);

        return items;
    },

    /**
     * Create a container with the message text.
     *
     * @returns { Ext.container.Container }
     */
    createErrorMessageContainer: function () {
        var me = this,
            message = [
                '<p style="margin-top: 10px; font-size: 11px; color: red;">* ',
                me.snippets.selectMessage,
                '</p>'
            ].join('');

        return Ext.create('Ext.container.Container', {
            flex: 1,
            hidden: true,
            html: message,
            style: {
                background: '#fff'
            }
        });
    },

    /**
     * Overwrite this method for set the mediaId property after set a value
     *
     * @overwrite
     * @param value
     */
    setValue: function (value) {
        var me = this;

        me.callParent(arguments);

        me.mediaId = me.getValue();
    },

    /**
     * Check if a media is selected
     *
     * @returns { boolean }
     */
    isValid: function () {
        var me = this;

        if (!me.required) {
            return true;
        }

        // MediaId could be a number that represents a "MediaID", "undefined" or "Null".
        // That's the reason why i make a real if check..
        if (!me.mediaId) {
            return false;
        }

        return true;
    },

    /**
     * Show a red border and the container with the error message
     */
    showIsInvalid: function () {
        var me = this,
            child = me.child();

        me.errorMessageContainer.show();

        child.getEl().setStyle('border', '1px dotted #FF0000');
        child.doLayout();
    },

    /**
     * Hide the border and the container with the error message.
     */
    hideIsInvalid: function () {
        var me = this,
            child = me.child();

        me.errorMessageContainer.hide();

        child.getEl().setStyle('border', '1px solid #FFF');
        child.doLayout();
    },

    /**
     * This is a method to overwrite and a helper to get the mediaId
     *
     * @Template
     * @param { Shopware.apps.SwagCustomProducts.view.components.MediaSelection } mediaSelection
     * @param { Media } media
     */
    setMedia: function (mediaSelection, media) {
    }
});
// {/block}
