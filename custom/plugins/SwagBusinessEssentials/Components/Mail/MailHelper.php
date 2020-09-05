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

namespace SwagBusinessEssentials\Components\Mail;

use Enlight_Components_Mail as Mail;
use Enlight_Components_Snippet_Manager as SnippetManager;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\ShopRegistrationServiceInterface;
use Shopware\Models\Customer\Customer;
use Shopware_Components_Config as Config;
use Shopware_Components_TemplateMail as TemplateMail;
use SwagBusinessEssentials\Models\CgSettings\CgSettings;

class MailHelper implements MailHelperInterface
{
    const MAIL_TEMPLATE_ACCEPTED = 'sCUSTOMERGROUPHACCEPTED';
    const MAIL_TEMPLATE_REJECTED = 'sCUSTOMERGROUPHREJECTED';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var TemplateMail
     */
    private $templateMail;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var SnippetManager
     */
    private $snippetManager;

    /**
     * @var ShopRegistrationServiceInterface
     */
    private $registrationService;

    public function __construct(
        Config $config,
        TemplateMail $templateMail,
        ModelManager $modelManager,
        SnippetManager $snippetManager,
        ShopRegistrationServiceInterface $registrationService
    ) {
        $this->config = $config;
        $this->templateMail = $templateMail;
        $this->modelManager = $modelManager;
        $this->snippetManager = $snippetManager;
        $this->registrationService = $registrationService;
    }

    /**
     * Sends a mail to the customer when his request was accepted.
     */
    public function sendAcceptedMail(Customer $customer)
    {
        $mailTemplate = $this->getMailTemplateForCustomer($customer);
        $this->sendMail($customer, $mailTemplate);
    }

    /**
     * Sends a mail to the customer when his request was declined.
     */
    public function sendDeclinedMail(Customer $customer)
    {
        $mailTemplate = $this->getMailTemplateForCustomer($customer, false);
        $this->sendMail($customer, $mailTemplate);
    }

    /**
     * Returns the name of the mail-template being saved for the given customer-group.
     *
     * @param bool $accept
     *
     * @return string
     */
    private function getMailTemplateForCustomer(Customer $customer, $accept = true)
    {
        /** @var CgSettings $cgSettings */
        $cgSettings = $this->modelManager->getRepository(CgSettings::class)->findOneBy([
            'customerGroup' => $customer->getValidation(),
        ]);

        $namespace = $this->snippetManager->getNamespace('backend/swag_business_essentials/view');
        $mailTemplateAllow = $namespace->get('PrivateRegisterTemplateAllowEmpty');
        $mailTemplateDeny = $namespace->get('PrivateRegisterTemplateDenyEmpty');

        if ($cgSettings) {
            $mailTemplateDeny = $cgSettings->getEmailTemplateDeny();
            $mailTemplateAllow = $cgSettings->getEmailTemplateAllow();
        }

        if ($accept) {
            if (!$mailTemplateAllow) {
                return self::MAIL_TEMPLATE_ACCEPTED;
            }

            return $mailTemplateAllow;
        }

        if (!$mailTemplateDeny) {
            return self::MAIL_TEMPLATE_REJECTED;
        }

        return $mailTemplateDeny;
    }

    /**
     * Sends the mail to the customer with the specified mail-template.
     *
     * @param string $mailTemplate
     */
    private function sendMail(Customer $customer, $mailTemplate)
    {
        $this->registrationService->registerResources($customer->getShop());

        /** @var Mail $mail */
        $mail = $this->templateMail->createMail(
            $mailTemplate,
            $this->getAdditionalMailContext(),
            $customer->getLanguageSubShop()
        );

        $mail->addTo($customer->getEmail());
        $mail->send();
    }

    /**
     * Returns the additional-mail context to be merged with the context from the mail-template itself.
     *
     * @return array
     */
    private function getAdditionalMailContext()
    {
        return [
            'sConfig' => $this->config,
        ];
    }
}
