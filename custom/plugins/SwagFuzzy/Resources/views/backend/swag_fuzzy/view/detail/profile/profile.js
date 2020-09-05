// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/detail/profile/profile"}
Ext.define('Shopware.apps.SwagFuzzy.view.detail.profile.Profile', {
    extend: 'Shopware.model.Container',
    alias: 'widget.profile-detail-container',
    padding: 10,

    configure: function () {
        return {
            controller: 'SwagFuzzyProfiles',
            splitFields: false,
            fieldSets: {
                fields: {
                    name: {
                        fieldLabel: '{s name=profiles/profileNameColumn}Profile name{/s}',
                        allowBlank: false
                    }
                }
            }
        };
    }
});
// {/block}
