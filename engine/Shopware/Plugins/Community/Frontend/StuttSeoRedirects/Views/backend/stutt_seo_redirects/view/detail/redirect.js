//{namespace name=backend/stutt_seo_redirects/view/detail}

Ext.define('Shopware.apps.StuttSeoRedirects.view.detail.Redirect', {
    extend: 'Shopware.model.Container',
    padding: 20,

    configure: function() {
        return {
            controller: 'StuttSeoRedirects',
            fieldSets: [{
                layout: 'fit',
                fields: {
                    active: '{s name=active}Aktiviert{/s}',
                    oldUrl: '{s name=old_url}alte URL{/s}',
                    newUrl: '{s name=redirect_target}Weiterleitungs-Ziel{/s}',
                    overrideShopUrl: '{s name=override_shop_url}vorhandene Shopware-URL ersetzen{/s}',
                    temporaryRedirect: '{s name=only_temporary}nur temporär weiterleiten (HTTP 302){/s}',
                    externalRedirect: '{s name=external_redirect}Weiterleitung auf externes Ziel{/s}',
                    shop_id: '{s name=subshop}Subshop{/s}',
                    gone: '{s name=gone}Inhalt entfernt (HTTP 410, keine Weiterleitung){/s}'
                }
            }]
        };
    }
});