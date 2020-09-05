// {namespace name="backend/swag_promotion/snippets"}
// {block name="backend/swag_promotion/view/components/MessageBox"}
Ext.define('Shopware.apps.SwagPromotion.view.components.MessageBox', {
    extend: 'Ext.window.Window',

    width: 350,

    possibleButtons: {
        YES_NO: 'YES_NO',
        OK_DELETE_MESSAGE: 'OK_DELETE_MESSAGE'
    },

    initComponent: function() {
        var me = this;

        me.items = me.createItems();
        me.dockedItems = me.createDockedItems();

        me.callParent(arguments);
    },

    /**
     * @return { Array }
     */
    createItems: function() {
        var me = this;

        return [
            me.createContentBox()
        ];
    },

    /**
     * @return { Array }
     */
    createDockedItems: function() {
        var me = this;

        return [
            me.createBottomToolBar()
        ];
    },

    /**
     * @return { Ext.container.Container }
     */
    createContentBox: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            layout: 'hbox',
            style: {
                background: '#F0F2F4'
            },
            items: [
                me.createContent(),
                me.createIconContainer()
            ]
        });
    },

    /**
     * @return { Ext.container.Container }
     */
    createContent: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            padding: '10px 20px 10px 10px',
            flex: 1,
            html: me.msg
        });
    },

    /**
     * @return { Ext.container.Container }
     */
    createIconContainer: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            margin: '20px 10px',
            cls: me.iconClass,
            width: 40,
            height: 40
        });
    },

    /**
     * @return { Ext.toolbar.Toolbar }
     */
    createBottomToolBar: function() {
        var me = this;

        return Ext.create('Ext.toolbar.Toolbar', {
            items: me.createToolbarButtons(),
            dock: 'bottom'
        });
    },

    /**
     * @return { Array }
     */
    createToolbarButtons: function() {
        var me = this;

        if (me.buttonSelection === me.possibleButtons.YES_NO) {
            return [
                '->',
                me.createYesButton(),
                me.createNoButton()
            ];
        }

        if (me.buttonSelection === me.possibleButtons.OK_DELETE_MESSAGE) {
            return [
                '->',
                me.createOKButton(),
                me.createDeleteMessageButton()
            ];
        }

        return [];
    },

    /**
     * @return { Ext.button.Button }
     */
    createYesButton: function() {
        var me = this;

        return Ext.create('Ext.button.Button', {
            cls: 'primary',
            text: '{s name=yesButton}Yes{/s}',
            handler: Ext.bind(me.onYesClick, me)
        });
    },

    /**
     * @return { Ext.button.Button }
     */
    createDeleteMessageButton: function() {
        var me = this;

        return Ext.create('Ext.button.Button', {
            cls: 'secondary',
            text: '{s name=deleteMessageButton}Delete message{/s}',
            handler: Ext.bind(me.onDeleteMessageClick, me)
        });
    },

    /**
     * @return { Ext.button.Button }
     */
    createNoButton: function() {
        var me = this;

        return Ext.create('Ext.button.Button', {
            cls: 'secondary',
            text: '{s name=noButton}No{/s}',
            handler: Ext.bind(me.onNoClick, me)
        });
    },

    /**
     * @return { Ext.button.Button }
     */
    createOKButton: function() {
        var me = this;

        return Ext.create('Ext.button.Button', {
            cls: 'primary',
            text: '{s name=okButton}OK{/s}',
            handler: Ext.bind(me.onOkClick, me)
        });
    },

    /**
     * @param { string }eventName
     */
    notifyAndDestroy: function(eventName) {
        var me = this;

        me.fireEvent(eventName);

        me.destroy();
    },

    /**
     * ClickHandler
     */
    onDeleteMessageClick: function() {
        this.notifyAndDestroy('delete');
    },

    /**
     * ClickHandler
     */
    onOkClick: function() {
        this.notifyAndDestroy('ok');
    },

    /**
     * ClickHandler
     */
    onNoClick: function() {
        this.notifyAndDestroy('no');
    },

    /**
     * ClickHandler
     */
    onYesClick: function() {
        this.notifyAndDestroy('yes');
    }
});
// {/block}
