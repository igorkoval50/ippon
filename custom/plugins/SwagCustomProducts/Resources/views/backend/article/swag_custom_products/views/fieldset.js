// {namespace name="backend/article/swag_custom_products"}

// {block name="backend/article/swag_custom_products/views/fieldset"}

Ext.define('Shopware.apps.Article.views.Fieldset-SwagCustomProducts', {

    /** @string Extends from the { @link Ext.form.FieldSet } */
    extend: 'Ext.form.FieldSet',

    /** @integer Padding for the body component of the fieldset */
    bodyPadding: 20,

    /** @object Styles for the description component */
    descriptionStyles: {
        fontStyle: 'italic',
        color: '#8698A3',
        margin: '0 0 15px',
        fontSize: '11px'
    },

    /** @object Styles for the name field */
    fieldStyle: {
        margin: '5px 0 0',
        fontSize: '11px'
    },

    /** @object Snippets for the component */
    snippets: {
        title: '{s name=product_module/title}Custom Products{/s}',
        description: '{s name=product_module/description}Custom Products provides you with the ability to create fully customisable products with many different options and settings. You can create customisable coffee cups or t-shirts with just a few mouse clicks.{/s}',
        fieldLabel: '{s name=product_module/fieldLabel}Custom Product template{/s}',
        emptyText: "{s name=product_module/emptyText}A custom product template isn't associated yet{/s}",
        pleaseWaitText: '{s name=product_module/pleaseWaitText}Please wait...{/s}',
        btnNoTemplateFoundText: '{s name=product_module/btnNoTemplateFoundText}Create a new template{/s}',
        btnTemplateFoundText: '{s name=product_module/btnTemplateFoundText}Configure it now{/s}',
        btnTooltip: '{s name=product_module/btnTooltip}The Product must be saved first, before you are able to create a new template.{/s}',
        errorMsg: "{s name=product_module/errorMsg}The Custom Product template can't be loaded.{/s}"
    },

    /** @string Icon for the button as an base64 encoded string */
    btnIcon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABb0lEQVR4AcWQM2AnURCHc74rz7ZvvRsnTdCmi23bduqg72Lbtm3b5iF4maDf969ujff75puR+i9bCfX2dTH1ZiCfeDkrcbiC/vC2gn4/VUy9RQDokChcxXx8X818mgMIAoPRPOLlc+xwDfOZaGC/rtcyn1E5/X6oiHz9CDtcx36hW7gf243cNwQGY2XUuyfY4Ub2m1aHQOy38b9QPft1Gqo/xVVW7xLI8X4ZFnULFGrmvi/CDMR77kqwvAnnc9COGpEV0AAAAHQCQFY0PJjjoTCU5704mO1+0JfmqNrK/4y9MGjivmdgaY8U+q0O5Xr1DGS5VfZnOB/0pNgqw7TNCsnXj0XDw3ne+qPFgScQJuDsAADqSbHbh3buY1b3bxzO9ykDfQ04z64Bq1hh0H4wXhryZyjX0whMtgCArgFRuL3HjZUE748W+R+ABboA9KU7NQNAXH+0KCBmsjLydKI8DMHzP7AZHch09QeD2zjFzwHQ38AXXf7plQAAAABJRU5ErkJggg==',

    /** @string Request URL to load the custom product data for the selected product */
    requestUrl: '{url controller="SwagCustomProductsExtensions" action="getTemplateByProductId"}',

    /** @string Simple template for the custom products */
    fieldTemplate: '[0]',

    /** @string Simple template for the fieldset title */
    titleTemplate: '[0] - [1]',

    /** @null|object Holds the product model when the product has an associated custom product */
    product: null,

    /**
     * Initializes the component, sets the title for the fieldset and adds the elements to the component.
     *
     * @returns void
     */
    initComponent: function() {
        var me = this;

        me.title = me.snippets.title;

        // Due to the nature of the product module we have to be aware that the product will change when the user
        // uses the split view for example.
        me.addEvents('productHasChanged');

        // Custom event which be fired when the product has changed (e.g. store loaded / split view change)
        me.on('productHasChanged', Ext.bind(me.requestCustomProducts, me));

        me.items = [
            me.createDescriptionComponent(),
            me.createLayoutContainer()
        ];

        me.callParent(arguments);
    },

    /**
     * Creates the description component for the fieldset which gives the user a brief overview about the functionality.
     *
     * @returns { Ext.container.Container }
     */
    createDescriptionComponent: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            html: me.snippets.description,
            style: me.descriptionStyles
        });
    },

    /**
     * Creates the two-column layout container of the fieldset.
     *
     * @returns { Ext.container.Container }
     */
    createLayoutContainer: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            layout: 'column',
            defaults: {
                xtype: 'panel',
                columnWidth: 0.5
            },
            items: [
                me.createNameContainer(),
                // If the user has no rights, the create template button would not create.
                /* {if {acl_is_allowed privilege=read resource=swagcustomproducts}} */
                me.createButtonContainer()
                /* {/if} */
            ]
        });
    },

    /**
     * Creates the display field for the custom products name and a container which wraps around the field.
     *
     * @returns { Ext.container.Container }
     */
    createNameContainer: function() {
        var me = this, field;

        me.field = field = Ext.create('Ext.form.field.Display', {
            fieldLabel: me.snippets.fieldLabel,
            labelWidth: 155,
            value: me.snippets.emptyText,
            fieldStyle: me.fieldStyle
        });

        return Ext.create('Ext.container.Container', {
            columnWidth: 0.75,
            margin: '0 10 0 0',
            items: [ field ]
        });
    },

    /**
     * Creates the button which either creates or opens a custom product template (depending of the state) and
     * creates a container which wraps around the button.
     *
     * @returns { Ext.container.Container }
     */
    createButtonContainer: function() {
        var me = this, btn;

        // Assigning the button to the component and local scope, because it has to be accessed later on.
        me.btn = btn = Ext.create('Ext.button.Button', {
            cls: 'secondary small',
            icon: me.btnIcon,
            iconAlign: 'left',
            text: me.snippets.pleaseWaitText,
            disabled: true,
            scale: 'small',
            handler: Ext.bind(me.onButtonClick, me)
        });

        return Ext.create('Ext.container.Container', {
            columnWidth: 0.25,
            margin: '0 0 0 10',
            layout: 'fit',
            items: [ btn ]
        });
    },

    /**
     * Requests if the currently opened product has an associated custom product
     *
     * @param { Shopware.apps.Article.store.Batch } product
     * @returns void
     */
    requestCustomProducts: function(product) {
        var me = this,
            productId = product.get('id');

        me.product = product;

        me.setLoading(true);
        Ext.Ajax.request({
            url: me.requestUrl,
            params: {
                productId: productId
            },
            success: Ext.bind(me.onSuccess, me),
            error: Ext.bind(me.onError, me)
        });
    },

    /**
     * Event handler method which will be triggered when the batch stores of the product module was loaded and the
     * request to get the custom product preset was processed successfully.
     *
     * The methods updates the necessary elements, changes the configuration button text and provides the custom product
     * data globally in the component.
     *
     * @param { Object } response
     * @returns { boolean }
     */
    onSuccess: function(response) {
        var me = this;

        me.setLoading(false);

        if (!me.product.get('id')) {
            me.btn.setText(me.snippets.btnNoTemplateFoundText);
            me.btn.setTooltip(me.snippets.btnTooltip);
            return false;
        }

        response = Ext.JSON.decode(response.responseText);

        me.btn.setDisabled(false);

        if (!Ext.isObject(response.data)) {
            me.btn.setText(me.snippets.btnNoTemplateFoundText);
            me.btn.setTooltip('');
            return false;
        }

        me.customProductsTemplate = response = response.data;

        me.btn.setText(me.snippets.btnTemplateFoundText);
        me.btn.setTooltip('');
        me.field.setValue(Ext.String.format(me.fieldTemplate, response['display_name']));
        me.setTitle(Ext.String.format(me.titleTemplate, me.snippets.title, response['display_name']));
    },

    /**
     * Event listener method which will be triggered when the request to get the custom product preset was not successful.
     *
     * The method deactivates the fieldset (e.g. this component) and disables an error message to notify the user.
     *
     * @returns void
     */
    onError: function() {
        var me = this;

        Shopware.Notification.createGrowlMessage(me.snippets.title, me.snippets.errorMsg);

        me.setLoading(false);
        me.setDisabled(true);
        me.btn.setDisabled(true);
    },

    /**
     * Event handler which will be triggered when the user clicks the configure button. The method terminates if the
     * product has an associated custom product preset. If truthy, the method will open up the custom product
     * module and opens up the preset, otherwise the module will be opened with a new preset which has the product
     * assigned to it.
     *
     * @returns void
     */
    onButtonClick: function() {
        var me = this,
            opts = {
                name: 'Shopware.apps.SwagCustomProducts',
                state: 'existingCustomProduct',
                params: {
                    customProduct: me.customProductsTemplate
                }
            };

        if (!me.hasOwnProperty('customProductsTemplate')) {
            opts.state = 'newCustomProduct';
            opts.params = {
                product: me.product
            };
        }

        Shopware.app.Application.addSubApplication(opts);
    }
});

// {/block}
