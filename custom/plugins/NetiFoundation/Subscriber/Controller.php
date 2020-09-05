<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Subscriber;

use Enlight\Event\SubscriberInterface;

/**
 * Class Controller
 *
 * @package NetiFoundation\Subscriber
 */
class Controller implements SubscriberInterface
{
    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatchSecure' => array('onPostDispatch', -14052009)
        );
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     */
    public function onPostDispatch(\Enlight_Event_EventArgs $args)
    {
        $context = $args->get('subject');
        /** @var \Enlight_Controller_Request_RequestHttp $request */
        $request = $context->Request();

        $module = strtolower($request->getModuleName());
        $ctrl   = strtolower($request->getControllerName());
        $axn    = strtolower($request->getActionName());

        /** @var \Enlight_View_Default $view */
        $view = $context->View();
        $view->addTemplateDir(__DIR__ . '/../Views/', 'NetiFoundation_Controller');

        if ('backend' === $module) {
            if ('pluginmanager' === $ctrl && 'load' === $axn && 'dev' === getenv('NETI_SW_ENV')) {
                // suppress opening the login window
                $view->extendsTemplate('backend/neti_foundation/extensions/plugin_manager/controller/plugin.js');
            } elseif ('index' === $ctrl && 'load' === $axn) {
                //26306
                $view->extendsTemplate('backend/neti_foundation/extensions/components/view/grid/association.js');

                //26268
                $view->extendsTemplate('backend/neti_foundation/extensions/components/view/field/grid.js');

                // 26096
                $view->extendsTemplate('backend/neti_foundation/extensions/pagingcombobox.js');

                // Extensions
                $view->extendsTemplate('backend/neti_foundation/extensions/attribute_data.js');

                // 25726
                $view->extendsTemplate('backend/neti_foundation/extensions/export/container.js');
                $view->extendsTemplate('backend/neti_foundation/extensions/export/window.js');

                // #25703
                $view->extendsTemplate('backend/neti_foundation/extensions/helper.js');

                // #25656
                $view->extendsTemplate('backend/neti_foundation/extensions/send_mail/model/attachment.js');
                $view->extendsTemplate('backend/neti_foundation/extensions/send_mail/store/attachment.js');
                $view->extendsTemplate('backend/neti_foundation/extensions/send_mail/attachments.js');
                $view->extendsTemplate('backend/neti_foundation/extensions/send_mail/content_editor.js');
                $view->extendsTemplate('backend/neti_foundation/extensions/send_mail/model/mail.js');
                $view->extendsTemplate('backend/neti_foundation/extensions/send_mail/store/mail.js');
                $view->extendsTemplate('backend/neti_foundation/extensions/send_mail/form.js');
                $view->extendsTemplate('backend/neti_foundation/extensions/send_mail/field.js');
                $view->extendsTemplate('backend/neti_foundation/extensions/send_mail/window.js');

                // #25657
                $view->extendsTemplate('backend/neti_foundation/extensions/components/view/window/detail.js');
                $view->extendsTemplate('backend/neti_foundation/extensions/components/controller/controller.js');
                $view->extendsTemplate('backend/neti_foundation/extensions/components/model/container.js');

                // Support form
                $view->extendsTemplate('backend/neti_foundation/view/form/support.js');
                $view->extendsTemplate('backend/neti_foundation/view/tab/support.js');

                $view->extendsTemplate('backend/neti_foundation/view/form/category/list.js');
                $view->extendsTemplate('backend/neti_foundation/view/form/category/window.js');
                $view->extendsTemplate('backend/neti_foundation/view/form/category/tree.js');

                $view->extendsTemplate('backend/neti_foundation/view/form/media/field.js');
                $view->extendsTemplate('backend/neti_foundation/view/form/media/list.js');
                $view->extendsTemplate('backend/neti_foundation/view/form/media/upload.js');
                $view->extendsTemplate('backend/neti_foundation/view/form/media/model.js');
                $view->extendsTemplate('backend/neti_foundation/view/form/media/drop_zone.js');

                $view->extendsTemplate('backend/neti_foundation/view/form/date_time/picker.js');
                $view->extendsTemplate('backend/neti_foundation/view/form/date_time/field.js');

                $view->extendsTemplate('backend/neti_foundation/view/form/optional/field.js');

                // Variant search - does not work yet
                $view->extendsTemplate('backend/neti_foundation/view/store/variant.js');
                $view->extendsTemplate('backend/neti_foundation/view/form/field/VariantSearch.js');
            }

            if ('index' === $ctrl) {
                $view->extendsTemplate('backend/neti_foundation/extensions/header/css.tpl');
            }
        }
    }
}
