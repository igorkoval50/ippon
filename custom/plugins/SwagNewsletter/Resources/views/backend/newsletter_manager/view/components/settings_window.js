//{namespace name="backend/swag_newsletter/main"}
/**
 * Shopware UI - NewsletterManager Settings Window
 */
//{block name="backend/newsetter_manager/view/component/settings_window"}
Ext.define('Shopware.apps.NewsletterManager.view.components.SettingsWindow', {
    extend: 'Enlight.app.Window',
    alias: 'widget.newsletter-settings-window',
    border: false,
    layout: 'fit',
    autoShow: true,
    height: 550,
    width: 850,
    stateful: true,
    stateId: 'newsletter-settings-window',

    /**
     * Initializes the component and builds up the main interface
     *
     * @return void
     */
    initComponent: function() {
        var me = this;

        // Set the window title
        me.title = me.settings.component.get('name');

        // Build up the items
        me.items = [
            {
                xtype: me.settings.component.get('xType') || 'newsletter-components-base',
                settings: me.settings
            }
        ];

        // Build the action toolbar
        me.dockedItems = [
            {
                dock: 'bottom',
                xtype: 'toolbar',
                ui: 'shopware-ui',
                cls: 'shopware-toolbar',
                items: me.createActionButtons()
            }
        ];

        me.callParent(arguments);
    },

    /**
     * Registers additional component events.
     */
    registerEvents: function() {
        this.addEvents(
            /**
             * Fired when the user clicks the save button to save the component settings
             *
             * @event
             * @param [object] The component form panel
             * @param [object] The component record
             */
            'saveComponent'
        );
    },

    createActionButtons: function() {
        var me = this;

        return [
            '->', {
                xtype: 'button',
                cls: 'secondary',
                text: '{s name=settings_window/cancel}Cancel{/s}',
                action: 'newsletter-settings-window-cancel',
                handler: function(button) {
                    var win = button.up('window');
                    win.destroy();
                }
            }, {
                xtype: 'button',
                cls: 'primary',
                text: '{s name=settings_window/save}Save{/s}',
                action: 'newsletter-settings-window-save',
                handler: function() {
                    if (me.settings.component.get('xType') == 'newsletter-components-links') {
                        var grid = me.down('#newsletterLinkGrid');
                        if (grid.getStore().getCount() < 1) {
                            Shopware.Notification.createGrowlMessage(
                                me.title,
                                '{s name=error/not_all_required_fields_filled}Please fill out all required fields to save the component settings.{/s}'
                            );

                            return;
                        }
                    }

                    if (me.isFired) {
                        return;
                    }

                    me.isFired = true;
                    me.fireEvent('saveComponent', me, me.settings.record, me.settings.fields);

                    Ext.Function.defer(function() {
                        me.isFired = false;
                    }, 500);
                }
            }
        ];
    }
});
//{/block}
