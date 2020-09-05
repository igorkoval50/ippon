<?php

namespace MagNewsletterBox\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Plugin\ConfigReader;
use Enlight_Event_EventArgs as EventArgs;
use Shopware\Components\Theme\LessDefinition;

class FrontendSubscriber implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginDirectory;

    /**
     * @var \Enlight_Template_Manager
     */
    private $templateManager;

    /**
     * @var string
     */
    private $pluginConfig;

    /**
     * @param $pluginDirectory
     * @param \Enlight_Template_Manager $templateManager
     */
    public function __construct($pluginDirectory, $pluginName, \Enlight_Template_Manager $templateManager, ConfigReader $configReader)
    {
        $this->pluginDirectory = $pluginDirectory;
        $this->templateManager = $templateManager;

        $shop = false;

        if (Shopware()->Container()->initialized('shop')) {
            $shop = Shopware()->Container()->get('shop');
        }

        if (!$shop) {
            $shop = Shopware()->Container()->get('models')->getRepository(\Shopware\Models\Shop\Shop::class)->getActiveDefault();
        }

        //$this->pluginConfig = $configReader->getByPluginName($pluginName);
        $this->pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('MagNewsletterBox', $shop);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Inheritance_Template_Directories_Collected' => 'onCollectTemplateDir',
            'Theme_Compiler_Collect_Plugin_Less' => 'onCollectLessFiles',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'onCollectJavascriptFiles',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatch',
            'Shopware_Controllers_Frontend_Newsletter::sendMail::replace' => 'onReplaceSendMail',
            'Enlight_Controller_Action_PreDispatch' => 'onPreDispatch'
        ];
    }

    /**
     * @return array
     */
    public function onPreDispatch()
    {
        $this->templateManager->addTemplateDir($this->pluginDirectory . '/Resources/views');
    }

    /**
     * @param EventArgs $args
     */
    public function onCollectTemplateDir(EventArgs $args)
    {
        $dirs = $args->getReturn();
        $dirs[] = $this->pluginDirectory . '/Resources/views';

        $args->setReturn($dirs);
    }

    /**
     * @return array
     */
    private function activatingControllerConfig()
    {
        $return = array();

        $configuredControllers = strtolower($this->pluginConfig['controller']);

        if (strpos($configuredControllers, ',') > 0) {
            $configuredControllers = explode(',', $configuredControllers);
        } else {
            $configuredControllers = array($configuredControllers);
        }

        foreach($configuredControllers as $currentEntry) {
            $currentEntry = trim($currentEntry);

            //backwards compatibility: users were able to enter 'finish' which really meant 'checkout/finish'
            if ($currentEntry == 'finish') {
                $currentEntry = 'checkout#finish';
            }

            //split controller#action entries
            if (substr_count($currentEntry, '#') == 1) {
                $tmp = explode('#', $currentEntry);
                $controllerName = $tmp[0];
                $actionName     = $tmp[1];

                if (!array_key_exists($controllerName, $return)) {
                    $return[$controllerName] = array($actionName);
                } else {
                    //if given action name is not listed and controller is not meant to be "used always"
                    if (is_array($return[$controllerName]) && !in_array($actionName, $return[$controllerName])) {
                        $return[$controllerName][] = $actionName;
                    }
                }
            } elseif (substr_count($currentEntry, '#') == 0) {
                //every action of given controller should trigger newsletter box
                $return[$currentEntry] = true;
            } else {
                //more than one delimiter currently not supported
            }
        }

        //each action of NewsletterBox controller triggers display
        $return['newsletterbox'] = true;

        return $return;
    }

    /**
     * @param EventArgs $args
     */
    public function onPostDispatch(EventArgs $args)
    {
        $controller = $args->getSubject();
        $request = $controller->Request();

        $currentControllerName = strtolower($request->getControllerName());
        $currentActionName = strtolower($request->getActionName());

        $triggerDisplay = false;
        //evaluate only, if newsletter box is activated (speedup things otherwise)
        if ($this->pluginConfig['shownewsletterbox']) {
            $activatingControllerConfig = $this->activatingControllerConfig();
            if (array_key_exists($currentControllerName, $activatingControllerConfig)) {
                //check if current controller OR controller#action is whitelisted
                if (true === $activatingControllerConfig[$currentControllerName]
                    || in_array($currentActionName, $activatingControllerConfig[$currentControllerName])
                ) {
                    $triggerDisplay = true;

                    //if user doesn't like our precious box: don't display it!
                    if (
                        (isset($_COOKIE['mag-newsletterbox']) && $this->pluginConfig['cookielife'] == 0) ||
                        (isset($this->pluginConfig['hideafterregistration']) && isset($_COOKIE['mag-newsletterbox']))
                    ) {
                        $triggerDisplay = false;
                    }
                }
            }
        }

        $checkActiveNL = Shopware()->Db()->fetchRow('SELECT email FROM s_campaigns_mailaddresses WHERE email = ? LIMIT 1', array(Shopware()->Session()->offsetGet('sUserMail')));

        if ($triggerDisplay && empty($checkActiveNL)) {
            /**
             * @var \Enlight_View_Default $view
             */
            $view = $args->get('subject')->View();
            $view->assign('MagNewsletterBoxConfig', $this->pluginConfig);
        }
    }

    /**
     * Send mail method
     * @param string      $recipient
     * @param string      $template
     * @param bool|string $optin
     * @param EventArgs $args
     */
    public function onReplaceSendMail(EventArgs $args)
    {
        $request = $args->getSubject()->Request();

        $recipient = $args->get('recipient');
        $template = $args->get('template');
        $optin = ($args->get('optin') ? $args->get('optin') : false);

        $context = [];

        if (!empty($optin)) {
            $context['sConfirmLink'] = $optin;
        } else {
            // Query to check if e-mail address has ever received a code
            $checkEmail = Shopware()->Db()->fetchOne("SELECT COUNT(id) AS total FROM s_plugin_mag_emarketing_voucher_codes WHERE email = ?", array($recipient));

            // Check if voucher set has been set in plugin configuration
            // Check if e-mail address is already listed in s_plugin_mag_emarketing_voucher_codes otherwise display voucher code
            if (!empty($this->pluginConfig['voucherset']) && !$checkEmail['total']) {
                $getVoucher = Shopware()->Db()->fetchRow('
					SELECT evc.id, evc.code 
					FROM s_emarketing_voucher_codes evc 
					LEFT JOIN s_plugin_mag_emarketing_voucher_codes mevc ON (mevc.voucherCodeID = evc.id) 
					WHERE evc.voucherID = ? AND mevc.voucherCodeID IS NULL
					LIMIT 1',
                    array($this->pluginConfig['voucherset']));

                if (!empty($getVoucher)) {
                    $context['sVoucher'] = ($getVoucher["code"]?$getVoucher["code"]:'');

                    Shopware()->Db()->query("INSERT INTO s_plugin_mag_emarketing_voucher_codes (voucherID, voucherCodeID, email) VALUES (?, ?, ?)", array($this->pluginConfig['voucherset'], $getVoucher["id"], $recipient));
                }
            }
        }

        foreach ($request->getPost() as $key => $value) {
            $context['sUser.' . $key] = $value;
            $context['sUser'][$key] = $value;
        }

        $mail = Shopware()->TemplateMail()->createMail($template, $context);
        $mail->addTo($recipient);
        $mail->send();
    }

    /**
     * @param EventArgs $args
     */
    public function onCollectLessFiles(EventArgs $args)
    {
        $less = new LessDefinition(
        //configuration
            array(
            ),
            //less files to compile
            array(
                $this->pluginDirectory . '/Resources/frontend/less/newsletterbox.less'
            ),
            //import directory
            $this->pluginDirectory
        );

        return new ArrayCollection(array($less));
    }

    /**
     * @param EventArgs $args
     */
    public function onCollectJavascriptFiles(EventArgs $args)
    {
        $collection = new ArrayCollection();
        $collection->add($this->pluginDirectory . '/Resources/frontend/js/newsletterbox.js');

        return $collection;
    }
}