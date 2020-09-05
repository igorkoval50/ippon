<?php
/**
 * Copyright (c) TreoLabs GmbH
 *
 * This Software is the property of TreoLabs GmbH and is protected
 * by copyright law - it is NOT Freeware and can be used only in one project
 * under a proprietary license, which is delivered along with this program.
 * If not, see <https://treolabs.com/eula>.
 *
 * This Software is distributed as is, with LIMITED WARRANTY AND LIABILITY.
 * Any unauthorised use of this Software without a valid license is
 * a violation of the License Agreement.
 *
 * According to the terms of the license you shall not resell, sublicense,
 * rent, lease, distribute or otherwise transfer rights or usage of this
 * Software or its derivatives. You may modify the code of this Software
 * for your own needs, if source code is provided.
 */

namespace TlsNewsletterGroup\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs;
use Enlight_Controller_Front;
use Enlight_Event_EventArgs;
use Enlight_Hook_HookArgs;
use Exception;
use TlsNewsletterGroup\Components\GroupService;

class Frontend implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginDir;
    /**
     * @var GroupService
     */
    private $groupService;
    /**
     * @var Enlight_Controller_Front
     */
    private $front;

    /**
     * Frontend constructor.
     * @param string $pluginDir
     * @param GroupService $groupService
     * @param Enlight_Controller_Front $front
     */
    public function __construct($pluginDir, GroupService $groupService, Enlight_Controller_Front $front)
    {
        $this->pluginDir = $pluginDir;
        $this->groupService = $groupService;
        $this->front = $front;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'assignGroupList',
            'Shopware_Modules_Admin_Newsletter_Registration_Success' => 'onRegistrationSuccess',
            'Shopware_Modules_Admin_sUpdateNewsletter_Subscribe' => 'onRegistrationSuccess',
            'sAdmin::sUpdateNewsletter::after' => 'aftersUpdateNewsletter',
        );
    }

    /**
     * @param Enlight_Controller_ActionEventArgs $args
     */
    public function assignGroupList(Enlight_Controller_ActionEventArgs $args)
    {
        $controller = $args->getSubject();
        $view = $controller->View();

        $view->addTemplateDir($this->pluginDir . '/Resources/views');

        $request = $controller->Request();
        $email = null;
        if ($request->getControllerName() === 'account' && $request->getActionName() === 'index') {
            $email = $view->getAssign('sUserData')['additional']['user']['email'] ?? null;
        }
        $view->assign('TlsNewsletterGroupList', $this->groupService->getActiveGroupList($email));
    }

    /**
     * @param Enlight_Event_EventArgs $args
     * @throws Exception
     */
    public function onRegistrationSuccess(Enlight_Event_EventArgs $args)
    {
        $email = $args->get('email');
        $groupID = $args->get('groupID');

        $this->subscribeNewsletter($email, $groupID);
    }

    /**
     * @param Enlight_Hook_HookArgs $args
     * @throws Exception
     */
    public function aftersUpdateNewsletter(Enlight_Hook_HookArgs $args)
    {
        if (!$args->get('status')) {
            return; // unsubscribe
        }
        $email = $args->get('email');
        if (!$args->getReturn()) {
            $this->subscribeNewsletter($email); // update group when email in table
        } else {
            $optInNewsletter  = Shopware()->Config()->get('optinnewsletter');
            $groups = $this->front->Request()->getPost('tls_newsletter_groups', []);

            if ($optInNewsletter && $groups) {
                $this->groupService->updateOptinData($email, $groups); // update Optin data with groups
            }
        }
    }

    /**
     * @param string $email
     * @param int $groupID
     * @throws Exception
     */
    private function subscribeNewsletter($email, $groupID = null)
    {
        $groups = $this->front->Request()->getPost('tls_newsletter_groups', []);

        if ($groups) {
            $voteConfirmed = $this->front->getParam('voteConfirmed');
            $now = $this->front->getParam('optinNow');
            $now = isset($now) ? $now : (new \DateTime())->format('Y-m-d H:i:s');

            $added = $voteConfirmed ? $this->front->getParam('optinDate') : $now;
            $doubleOptInConfirmed = $voteConfirmed ? $now : null;

            $this->groupService->subscribeNewsletter($email, $groups, $groupID, $added, $doubleOptInConfirmed);
        }
    }
}
