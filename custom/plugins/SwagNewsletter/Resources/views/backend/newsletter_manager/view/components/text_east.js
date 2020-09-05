//{namespace name="backend/swag_newsletter/main"}
//{block name="backend/newsletter_manager/view/components/text_east"}
Ext.define('Shopware.apps.NewsletterManager.components.TextEast', {
    extend: 'Ext.panel.Panel',
    collapsed: true,
    collapsible: true,
    title: '{s name=personalizeNewsletter}Personalize Newsletter{/s}',
    region: 'east',
    width: 300,
    alias: 'widget.newsletter-components-text-east',
    autoScroll: true,
    /**
     * Init the main detail component, add components
     * @return void
     */
    initComponent: function () {
        var me = this;

        me.items = [me.createContainer()];
        me.callParent(arguments);
    },

    /**
     * Creates the main container, sets layout and adds the components needed
     * @return Ext.container.Container
     */
    createContainer: function () {
        var me = this;

        return Ext.create('Ext.container.Container', {
            border: false,
            padding: 10,

            layout: {
                type: 'vbox',
                align: 'stretch',
                pack: 'start'
            },
            items: [
                me.createInfoText()
            ]
        });
    },

    /**
     * Creates and returns a simple label which will later inform the user about the possible options (voucher / mail)
     * @return Ext.form.Label
     */
    createInfoText: function () {
        var me = this,
            html;

        html = "{s name=personalizeNewsletterContent}{literal}Personalize your newsletter using these variables:<br />\
        <br />\
        <b>Recipient's email address:</b><br />\
        {$sUser.newsletter}<br >\
        <br />\
        <b>Recipient's first name:</b><br />\
        {$sUser.firstname}<br />\
        <br />\
        <b>Recipient's last name:</b><br />\
        {$sUser.lastname}<br />\
        <br />\
        <b>Recipient's salutation:</b><br />\
        {$sUser.salutation}<br />\
            <br />\
            In order to greet your customer depending on his/her sex, you might want to use:<br />\
            <br />\
            {if $sUer.salutation == 'mr'}Mr{/if}{if $sUser.salutation == 'ms'}Ms{/if}\
            \
        <br />\
        <br />\
        <b>Recipient's street:</b><br />\
        {$sUser.street}<br />\
        <br />\
        <b>Recipient's street number:</b><br />\
        {$sUser.streetnumber}<br />\
        <br />\
        <b>Recipient's zip code:</b><br />\
        {$sUser.zipcode}<br />\
        <br />\
        <b>Recipient's city:</b><br />\
        {$sUser.city}{/literal}{/s}";

        me.infoLabel = Ext.create('Ext.form.Label', {
            html: html,
            padding: '0 0 20 0'
        });
        return me.infoLabel;
    }
});
//{/block}
