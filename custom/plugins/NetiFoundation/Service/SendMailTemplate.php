<?php
/**
 * Copyright notice
 *
 * (c) 2009-2017 Net Inventors - Agentur für digitale Medien GmbH
 * All rights reserved
 *
 * This script is part of the Spirit-Project.
 * The Spirit-Project is property of the Net Inventors GmbH and
 * may not be used in projects not related to the Net Inventors
 * without explicit permission by the authors.
 *
 * PHP version 5
 *
 * @package    NetiFoundation
 * @subpackage NetiFoundation/SendMailTemplate.php
 * @author     bmueller
 * @copyright  2017 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */

namespace NetiFoundation\Service;

use NetiFoundation\Struct\MailTemplateData;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Customer\Address;
use Shopware\Models\Customer\Customer;
use Shopware\Models\Mail\Mail as MailModel;
use Shopware\Models\Shop\Shop as ShopModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SendMailTemplate
 *
 * @package NetiFoundation\Service
 */
class SendMailTemplate implements SendMailTemplateInterface
{
    /**
     * @var ModelManager
     */
    protected $modelManager;

    /**
     * @var \Shopware_Components_TemplateMail
     */
    protected $templateMail;

    /**
     * @var \Enlight_Components_Mail
     */
    protected $mail;

    /**
     * @var \Shopware_Components_Config
     */
    protected $swConfig;

    /**
     * @var ShopInterface
     */
    protected $shop;

    /**
     * SendMailTemplate constructor.
     *
     * @param ModelManager                      $modelManager
     * @param \Shopware_Components_TemplateMail $templateMail
     * @param \Enlight_Components_Mail          $mail
     * @param \Shopware_Components_Config       $swConfig
     * @param Shop                              $shop
     */
    public function __construct(
        ModelManager $modelManager,
        \Shopware_Components_TemplateMail $templateMail,
        \Enlight_Components_Mail $mail,
        \Shopware_Components_Config $swConfig,
        Shop $shop
    ) {
        $this->modelManager = $modelManager;
        $this->templateMail = $templateMail;
        $this->mail         = $mail;
        $this->swConfig     = $swConfig;
        $this->shop         = $shop;
    }

    /**
     * @param Customer         $customer
     * @param MailTemplateData $mailTemplateData
     *
     * @return \Enlight_Components_Mail
     * @throws \Exception
     */
    public function sendMailToCustomer(Customer $customer, MailTemplateData $mailTemplateData)
    {
        /** @var ShopModel $languageShopID */
        $languageShopID   = $customer->getLanguageSubShop();
        $customerLanguage = $languageShopID->getId() ?: $customer->getShop()->getId();
        $email            = $customer->getEmail();

        $this->validateEmailAddress($email);

        // register shop and save previously registered shop
        /** @var ShopModel $oldShop */
        $oldShop = Shopware()->Container()->get('Shop', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $shop    = $this->registerSubshop($customerLanguage);

        $result = $this->prepareAndSend($mailTemplateData, $shop, $email, $customer);

        if ($oldShop instanceof ShopModel) {
            // re-register previous shop
            $oldShop->registerResources();
        }

        return $result;
    }

    /**
     * @param string           $email
     * @param MailTemplateData $mailTemplateData
     *
     * @return \Enlight_Components_Mail
     * @throws \Exception
     */
    public function sendMail($email, MailTemplateData $mailTemplateData)
    {
        $this->validateEmailAddress($email);

        // register shop and save previously registered shop
        /** @var ShopModel $oldShop */
        $oldShop = Shopware()->Container()->get('Shop', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $shop    = $this->registerSubshop();

        $result = $this->prepareAndSend($mailTemplateData, $shop, $email);

        if ($oldShop instanceof ShopModel) {
            // re-register previous shop
            $oldShop->registerResources();
        }

        return $result;
    }

    /**
     * @param MailTemplateData $mailTemplateData
     *
     * @return MailModel
     */
    protected function applyMailTemplateDataToModel(MailTemplateData $mailTemplateData)
    {
        /**
         * @var $mailModel MailModel
         */

        $criteria = array();
        if ($mailTemplateData->getTemplateName()) {
            $criteria['name'] = $mailTemplateData->getTemplateName();
        } elseif ($mailTemplateData->getMailTemplateId()) {
            $criteria['id'] = $mailTemplateData->getMailTemplateId();
        }

        if (! empty($criteria)) {
            $mailModel = $this->modelManager->getRepository('Shopware\Models\Mail\Mail')->findOneBy($criteria);

            if ($mailModel instanceof MailModel) {
                if ($mailTemplateData->getFromMail()) {
                    $mailModel->setFromMail($mailTemplateData->getFromMail());
                }
                if ($mailTemplateData->getFromName()) {
                    $mailModel->setFromName($mailTemplateData->getFromName());
                }
                if ($mailTemplateData->getSubject()) {
                    $mailModel->setSubject($mailTemplateData->getSubject());
                }
                if ($mailTemplateData->isIsHtml()) {
                    $mailModel->setIsHtml($mailTemplateData->isIsHtml());
                }
                if ($mailTemplateData->getContent()) {
                    $mailModel->setContent($mailTemplateData->getContent());
                }
                if ($mailTemplateData->getContentHtml()) {
                    $mailModel->setContentHtml($mailTemplateData->getContentHtml());
                }
            }

            return $mailModel;
        }

        return null;
    }

    /**
     * @param array     $context
     * @param ShopModel $shop
     * @param Customer  $customer
     */
    protected function buildContext(array &$context, ShopModel $shop, Customer $customer = null)
    {
        $baseUrl = sprintf(
            '%s://%s/%s',
            $shop->getSecure() ? 'https' : 'http',
            $shop->getHost(),
            $shop->getBasePath()
        );

        $context['sShopURL'] = rtrim($baseUrl, '/');
        if ($customer instanceof Customer) {
            $context['sCustomerEmail']      = $customer->getEmail();
            $context['sCustomerFirstname']  = $customer->getFirstname();
            $context['sCustomerLastname']   = $customer->getLastname();
            $context['sCustomerSalutation'] = $customer->getSalutation();
            $context['sCustomerTitle']      = $customer->getTitle();
            $context['sCustomerBirthday']   = $customer->getBirthday();
            $context['sCustomerNumber']     = $customer->getNumber();
            $billing                        = $customer->getDefaultBillingAddress();
            if ($billing instanceof Address) {
                $context['sCustomerBillingSalutation']             = $billing->getSalutation();
                $context['sCustomerBillingFirstName']              = $billing->getFirstName();
                $context['sCustomerBillingLastName']               = $billing->getLastName();
                $context['sCustomerBillingStreet']                 = $billing->getStreet();
                $context['sCustomerBillingZipCode']                = $billing->getZipCode();
                $context['sCustomerBillingCity']                   = $billing->getCity();
                $context['sCustomerBillingCompany']                = $billing->getCompany();
                $context['sCustomerBillingAdditionalAddressLine1'] = $billing->getAdditionalAddressLine1();
                $context['sCustomerBillingAdditionalAddressLine2'] = $billing->getAdditionalAddressLine2();
            }
        }
    }

    /**
     * @param MailModel $mailModel
     * @param array     $context
     * @param ShopModel $shop
     * @param array     $overrideConfig
     *
     * @return \Enlight_Components_Mail
     * @throws \Enlight_Exception
     */
    protected function createMail(MailModel $mailModel, $context = array(), $shop = null, $overrideConfig = array())
    {
        $this->templateMail->setShop($shop);

        if ($this->templateMail->getShop() !== null) {
            $defaultContext    = array(
                'sConfig'  => $this->swConfig,
                'sShop'    => $this->swConfig->get('shopName'),
                'sShopURL' => 'http://' . $this->swConfig->get('basePath'),
            );
            $isoCode           = $this->templateMail->getShop()->get('isocode');
            $translationReader = $this->templateMail->getTranslationReader();
            $translation       = $translationReader->read($isoCode, 'config_mails', $mailModel->getId());
            $mailModel->setTranslation($translation);
        } else {
            $defaultContext = array(
                'sConfig' => $this->swConfig,
            );
        }

        // save current context to mail model
        $mailContext = json_encode($context);
        $mailContext = json_decode($mailContext, true);
        $mailModel->setContext($mailContext);
        /**
         * Remove flush model from original code Shopware()->TemplateMail()->createMail()
         */

        $this->templateMail->getStringCompiler()->setContext(array_merge($defaultContext, $context));

        $mail = clone $this->mail;

        return $this->templateMail->loadValues($mail, $mailModel, $overrideConfig);
    }

    /**
     * @param $email
     *
     * @throws \Exception
     */
    protected function validateEmailAddress($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($email) {
                throw new \Exception(sprintf('Invalid email address "%s"', $email));
            }
            throw new \Exception('Empty email address');
        }
    }

    /**
     * This will register the detected shop
     * Please be aware, that this affects all following processes.
     * So we recommend to store the previously registered shop and register it again afterwards.
     *
     * @param $customerLanguage
     *
     * @return \Shopware\Models\Shop\DetachedShop
     * @throws \Exception
     */
    protected function registerSubshop($customerLanguage = null)
    {
        if (is_numeric($customerLanguage)) {
            $shop = $this->shop->getActiveShopById($customerLanguage);
            if (! $shop instanceof ShopModel) {
                throw new \Exception(sprintf('Subshop not found (ID %s)', $customerLanguage));
            }
        } else {
            $shop = $this->shop->getActiveShop();
            if (null === $shop) { // if not called from the frontend
                $shop = $this->shop->getActiveDefaultShop();
            }

            if (! $shop instanceof ShopModel) {
                throw new \Exception('Active subshop could not be determined');
            }
        }

        $shop->registerResources();

        return $shop;
    }

    /**
     * @param MailTemplateData $mailTemplateData
     * @param                  $shop
     * @param                  $email
     * @param Customer         $customer
     *
     * @return \Enlight_Components_Mail
     * @throws \Exception
     */
    protected function prepareAndSend(MailTemplateData $mailTemplateData, $shop, $email, Customer $customer = null)
    {
        $context = $mailTemplateData->getContextData();
        $this->buildContext($context, $shop, $customer);
        $mailModel = $this->applyMailTemplateDataToModel($mailTemplateData);

        if (!$mailModel instanceof MailModel) {
            throw new \Exception(sprintf(
                'Mail model not found (by name "%s" or ID "%s")',
                $mailTemplateData->getTemplateName(),
                $mailTemplateData->getMailTemplateId()
            ));
        }

        $mail = $this->createMail($mailModel, $context, $shop);
        $mail->addTo($email);

        if (!empty($bcc = $mailTemplateData->getBcc())) {
            $mail->addBcc($bcc);
        }

        if (!empty($attachments = $mailTemplateData->getAttachments())) {
            foreach ($attachments as $attachment) {
                if ($attachment instanceof \Zend_Mime_Part) {
                    $mail->addAttachment($attachment);
                }
            }
        }

        // store previous transport
        $defaultTransport = \Enlight_Components_Mail::getDefaultTransport();

        // generate new transport, to use the mailer data for the registered subshop
        $mailTransport = Shopware()->Container()->get('mailtransport_factory')->factory(
            Shopware()->Container()->get('Loader'),
            Shopware()->Container()->get('config'),
            Shopware()->Container()->getParameter('shopware.mail')
        );

        // send mail using the new transport
        $mail->send($mailTransport);

        // restore previously registered transport
        \Enlight_Components_Mail::setDefaultTransport($defaultTransport);

        $this->modelManager->refresh($mailModel);

        return $mail;
    }

}
