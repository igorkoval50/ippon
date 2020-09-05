//{namespace name="backend/swag_newsletter/main"}
//{block name="backend/newsletter_manager/controller/overview"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NewsletterManager.controller.Overview-SwagNewsletter', {

    /**
     * Defines an override applied to a class.
     * @string
     */
    override: 'Shopware.apps.NewsletterManager.controller.Overview',

    /**
     * List of classes that have to be loaded before instantiating this class.
     * @array
     */
    requires: ['Shopware.apps.NewsletterManager.controller.Overview'],

    /**
     * Initializes the class override to provide additional functionality
     * like a new full page preview.
     *
     * @public
     * @return void
     */
    init: function () {
        var me = this;

        me.subApplication.getController('Designer');
        me.subApplication.getController('Analytics');

        me.callOverridden(arguments);
    },

    /**
     * Called when the user clicks the 'start sending' newsletter button in the overview
     * @param record
     */
    onStartSendingNewsletter: function(record) {
        var me = this,
            pos = location.href.search("/backend"),
            url = location.href.substr(0, pos) + "/backend/Newsletter/cron";

        Ext.MessageBox.confirm('{s namespace=backend/newsletter_manager/main name=startSendingNewsletter/title}Start sending{/s}', '{s namespace=backend/newsletter_manager/main name=startSendingNewsletter/message}Do you really want to start sending this newsletter?{/s}', function (response) {
            if (response !== 'yes') {
                return;
            }

            Ext.Ajax.request({
                url: '{url controller=SwagNewsletter action=updatePublish}',
                params: {
                    id: record.get('id')
                },
                success: function() {
                    me.subApplication.mailingStore.load();
                }
            });

            Ext.Msg.show({
                title:'{s namespace=backend/newsletter_manager/main name=startSendingNewsletter/title}Start sending{/s}',
                msg: '{s namespace=backend/newsletter_manager/main name=startSendingNewsletterInfo/message}The newsletter is now queued for sending.<br />Please make sure, that you have set up the newsletter-script as a cron job or run it manually.<br /><br />Do you want to open the newsletter-script in a new window now?{/s}',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function(response) {
                    if(response !== 'yes') {
                        return;
                    }
                    window.open(url);
                }
            });
        });

    },

    onDuplicateNewsletter: function (record) {
        var me = this,
            grid = me.getOverviewGrid(),
            store = grid.getStore();

        // The actual duplication is done in the controller
        record.save({
            params: {
                duplicate: true
            },
            success: function () {
                store.reload();
            }
        });
    },

    /**
     * Called when the edit button in the action column was clicked
     * Will open the newsletter-editor window and load the existing newsletter
     */
    onEditNewsletter: function (record) {
        var me = this;

        Ext.Ajax.request({
            url: '{url controller=SwagNewsletter action=detail}',
            params: {
                id: record.get('id')
            },
            success: function(operation) {
                me.onLoadDetailRecord(operation, record);
            }
        });
    },

    /**
     * Loads the detail data of the newsletter into the detail-component.
     *
     * @param { Object } operation
     * @param { Shopware.apps.NewsletterManager.model.NewsletterElement } record
     */
    onLoadDetailRecord: function (operation, record) {
        var me = this,
            settings = Ext.create('Shopware.apps.NewsletterManager.model.Settings'),
            editor, form, senderMail, elements, senderRecord,
            reader = record.getProxy().getReader(),
            /** @type { Ext.data.ResultSet } resultSet */
            resultSet = reader.read(operation);

        record = resultSet.records[0];

        me.getView('newsletter.Window').create({
            senderStore: me.subApplication.senderStore,                     // available senders
            recipientGroupStore: me.subApplication.recipientGroupStore,     // available newsletter groups + available customer groups
            newsletterGroupStore: me.subApplication.newsletterGroupStore,   // available newsletter groups
            customerGroupStore: me.subApplication.customerGroupStore,       // available customer groups
            shopStore: me.subApplication.shopStore,
            customerStreamStore: me.subApplication.customerStreamStore,
            libraryStore: me.subApplication.libraryStore,
            dispatchStore: me.getStore('MailDispatch'),
            title: Ext.String.format("{s name=newsletterWindowEditTitle}Editing newsletter '{literal}{0}{/literal}{/s}'", record.get('subject')),
            record: record
        });

        //As the existing database table holds some strings where IDs would be needed, we have a additional
        //settings model, which translates between the Newsletter-Model ("Mailing") and the structure needed
        //to set the form up properly.
        settings.set('subject', record.get('subject'));
        settings.set('customerGroup', record.get('customerGroup'));
        settings.set('languageId', record.get('languageId'));
        if (record.get('plaintext') == true) {
            settings.set('dispatch', 2);
        } else {
            settings.set('dispatch', 1);
        }

        editor = me.getNewsletterEditor();
        form = me.getNewsletterSettings();
        senderMail = record.get('senderMail');

        elements = record.getElements();
        settings.set('elements', elements);

        // sender is saved as plain text. need to get the id from senderStore
        senderRecord = me.subApplication.senderStore.findRecord('email', senderMail);
        if (!senderRecord instanceof Ext.data.Model) {
            settings.set('senderId', null);
        } else {
            settings.set('senderId', senderRecord.get('id'));
        }

        form.loadRecord(settings);

        // TinyMCE will be loaded last - it hast some getDoc() us undefined issues
        editor.loadRecord(settings);
    },

    /**
     * Called when the edit button in the action column was clicked
     * Will open the newsletter-editor window and load the existing newsletter
     */
    onCreateNewNewsletter: function () {
        var me = this,
            form,
            settings = Ext.create('Shopware.apps.NewsletterManager.model.Settings');

        me.getView('newsletter.Window').create({
            senderStore: me.subApplication.senderStore,                     // available senders
            recipientGroupStore: me.subApplication.recipientGroupStore,     // available newsletter groups + available customer groups
            newsletterGroupStore: me.subApplication.newsletterGroupStore,   // available newsletter groups
            customerGroupStore: me.subApplication.customerGroupStore,       // available customer groups
            shopStore: me.subApplication.shopStore,
            customerStreamStore: me.subApplication.customerStreamStore,
            libraryStore: me.subApplication.libraryStore,
            dispatchStore: me.getStore('MailDispatch')
        });

        form = me.getNewsletterSettings();

        var senderStore = me.subApplication.senderStore, r;
        if (senderStore instanceof Ext.data.Store) {
            r = me.subApplication.senderStore.first();
            if (r) {
                settings.set('senderId', r.get('id'));
            }

        }

        settings.set('customerGroup', me.subApplication.customerGroupStore.first().get('key'));
        form.loadRecord(settings);
    }
});
//{/block}
