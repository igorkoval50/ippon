// {namespace name="backend/swag_promotion/snippets"}
// {block name="backend/promotion/view/components/buttonTypeSelect"}
Ext.define('Shopware.apps.SwagPromotion.view.components.ButtonTypeSelect', {
    extend: 'Shopware.apps.Base.view.element.ProductBoxLayoutSelect',

    fieldLabel: '{s name=buttonTypeLabel}Listing buy button type{/s}',
    helpText: '{s name=buttonTypeHelpText}Select which button(s) should be displayed in the product listing during a promotion. This function only works if the base setting \"Show buy button in listing\" is active.{/s}',

    createStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            fields: ['key', 'label', 'description', 'image'],
            data: [
                {
                    key: 'details',
                    label: '{s name=showDetailButton}Listing buy button type{/s}',
                    description: '{s name=showDetailButtonDescription}The detail button forwards to the product detail page.{/s}',
                    image: '{link file="backend/_resources/images/details.png"}'
                },
                {
                    key: 'buy',
                    label: '{s name=showBuyButton}Show buy button only{/s}',
                    description: '{s name=showBuyButtonDescription}The buy button adds the product to the cart.{/s}',
                    image: '{link file="backend/_resources/images/buy.png"}'
                },
                {
                    key: 'both',
                    label: '{s name=showBothButtons}Show buy and detail buttons{/s}',
                    description: '{s name=showBothButtonsDescription}Both buttons are displayed in listings.{/s}',
                    image: '{link file="backend/_resources/images/both.png"}'
                }
            ]
        });
    }
});
//{/block}
