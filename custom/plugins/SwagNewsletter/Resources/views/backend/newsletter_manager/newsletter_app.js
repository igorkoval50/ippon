/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

//{block name="backend/newsletter_manager/application"}
    //{$smarty.block.parent}

    /**
     * Controllers
     */
    //{include file="backend/newsletter_manager/controller/designer.js"}
    //{include file="backend/newsletter_manager/controller/analytics.js"}

    /**
     * Models
     */
    //{include file="backend/newsletter_manager/model/component.js"}
    //{include file="backend/newsletter_manager/model/field.js"}
    //{include file="backend/newsletter_manager/model/newsletter_element.js"}
    //{include file="backend/newsletter_manager/model/link.js"}
    //{include file="backend/newsletter_manager/model/article.js"}
    //{include file="backend/newsletter_manager/model/voucher.js"}
    //{include file="backend/newsletter_manager/model/order.js"}
    //{include file="backend/newsletter_manager/model/live_article.js"}


    /**
     * Stores
     */
    //{include file="backend/newsletter_manager/store/library.js"}
    //{include file="backend/newsletter_manager/store/voucher.js"}
    //{include file="backend/newsletter_manager/store/mailing.js"}
    //{include file="backend/newsletter_manager/store/order.js"}
    //{include file="backend/newsletter_manager/store/live_article.js"}

    /**
     * Views
     */
    //{include file="backend/newsletter_manager/view/tabs/statistics.js"}
    //{include file="backend/newsletter_manager/view/tabs/analytics.js"}
    //{include file="backend/newsletter_manager/view/tabs/orders.js"}
    //{include file="backend/newsletter_manager/view/newsletter/designer.js"}

    /**
     * Components
     */
    //{include file="backend/newsletter_manager/view/components/settings_window.js"}

    //{include file="backend/newsletter_manager/view/components/base.js"}
    //{include file="backend/newsletter_manager/view/components/article.js"}
    //{include file="backend/newsletter_manager/view/components/text.js"}
    //{include file="backend/newsletter_manager/view/components/text_east.js"}
    //{include file="backend/newsletter_manager/view/components/link.js"}
    //{include file="backend/newsletter_manager/view/components/live_shopping.js"}

    /**
     * Component fields
     */
    //{include file="backend/newsletter_manager/view/components/fields/article.js"}
    //{include file="backend/newsletter_manager/view/components/fields/article_type.js"}
    //{include file="backend/newsletter_manager/view/components/fields/target_selection.js"}
    //{include file="backend/newsletter_manager/view/components/fields/voucher_selection.js"}
    //{include file="backend/newsletter_manager/view/components/fields/numberfield.js"}
    //{include file="backend/newsletter_manager/view/components/fields/link_field.js"}

//{/block}
