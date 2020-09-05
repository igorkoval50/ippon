<?php
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

namespace SwagNewsletter\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Components_Mail;
use Enlight_Loader;
use Shopware\Components\DependencyInjection\Bridge\MailTransport as MailTransportFactory;
use Shopware_Components_Config;
use SwagNewsletter\Components\DependencyProviderInterface;

/**
 * Switches the transport of the the default mailer
 */
class MailTransport implements SubscriberInterface
{
    /**
     * @var \Zend_Mail_Transport_Abstract
     */
    protected $defaultTransport;

    /**
     * @var MailTransportFactory
     */
    private $mailTransport;

    /**
     * @var DependencyProviderInterface
     */
    private $dependencyProvider;

    /**
     * @var Enlight_Loader
     */
    private $enlightLoader;

    /**
     * @var Shopware_Components_Config
     */
    private $config;

    /**
     * @var \Enlight_Components_Mail
     */
    private $mail;

    /**
     * @param Enlight_Components_Mail     $mail
     * @param DependencyProviderInterface $dependencyProvider
     * @param MailTransportFactory        $mailTransport
     * @param Enlight_Loader              $enlightLoader
     * @param Shopware_Components_Config  $config
     */
    public function __construct(
        Enlight_Components_Mail $mail,
        DependencyProviderInterface $dependencyProvider,
        MailTransportFactory $mailTransport,
        Enlight_Loader $enlightLoader,
        Shopware_Components_Config $config
    ) {
        $this->mail = $mail;
        $this->dependencyProvider = $dependencyProvider;
        $this->mailTransport = $mailTransport;
        $this->enlightLoader = $enlightLoader;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Controllers_Backend_Newsletter::mailAction::before' => 'beforeMailAction',
            'Shopware_Controllers_Backend_Newsletter::mailAction::after' => 'afterMailAction',
        ];
    }

    /**
     * Change the default transport of the shopware mailer
     */
    public function beforeMailAction()
    {
        // save the old transport
        $this->defaultTransport = $this->mail->getDefaultTransport();

        $options = $this->dependencyProvider->getParameter('shopware.mail');

        if ($this->dependencyProvider->hasParameter('shopware.newsletterMail')) {
            $options = array_merge($options, $this->dependencyProvider->getParameter('shopware.newsletterMail'));
        }

        // create a new transport based on this options
        $transportFactory = $this->mailTransport;
        $newTransport = $transportFactory->factory(
            $this->enlightLoader,
            $this->config,
            $options
        );

        // set the new transport as the default one
        $this->mail->setDefaultTransport($newTransport);
    }

    /**
     * Set back the shopware transport to the original one
     */
    public function afterMailAction()
    {
        $this->mail->setDefaultTransport($this->defaultTransport);
    }
}
