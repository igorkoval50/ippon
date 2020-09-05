<?php
use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_MagNewsletterBox extends Enlight_Controller_Action implements CSRFWhitelistAware {

    public function getWhitelistedCSRFActions() {
        return [
            'subscribeNewsletter',
            'validateMailAddress'
        ];
    }

    /**
     * this function is called initially and extends the standard template directory
     * @return void
     */
    public function init() {
        $this->View()->addTemplateDir(dirname(__FILE__));
        Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
    }

    /**
     * Returns whether or not the current request contains
     * a valid newsletter confirmation
     *
     * @return bool
     */
    protected function isConfirmed()
    {
        if (empty($this->Request()->sConfirmation)) {
            return false;
        }

        $getVote = Shopware()->Db()->fetchRow(
            'SELECT * FROM s_core_optin WHERE hash = ?',
            [$this->Request()->get('sConfirmation')]
        );

        if (empty($getVote['data'])) {
            return false;
        }

        // Needed for 'added' date
        $this->front->setParam('optinDate', $getVote['datum']);

        Shopware()->System()->_POST = unserialize($getVote['data'], ['allowed_classes' => false]);

        Shopware()->Db()->query(
            'DELETE FROM s_core_optin WHERE hash = ?',
            [$this->Request()->get('sConfirmation')]
        );

        return true;
    }

    /**
     * Send mail method
     *
     * @param string      $recipient
     * @param string      $template
     * @param bool|string $optin
     */
    protected function sendMail($recipient, $template, $optin = false)
    {
        $context = [];

        if (!empty($optin)) {
            $context['sConfirmLink'] = $optin;
        }

        foreach ($this->Request()->getPost() as $key => $value) {
            $context['sUser.' . $key] = $value;
            $context['sUser'][$key] = $value;
        }

        $context = Shopware()->Events()->filter('Shopware_Controllers_Frontend_Newsletter_sendMail_FilterVariables', $context, [
            'template' => $template,
            'recipient' => $recipient,
            'optin' => $optin,
        ]);

        $mail = Shopware()->TemplateMail()->createMail($template, $context);
        $mail->addTo($recipient);
        $mail->send();
    }

    /**
     * Test e-mail address if already exists
     * @return void
     */
    public function validateMailAddressAction() {
        $this->View()->_POST = Shopware()->System()->_POST->toArray();
        $mailQuery = Shopware()->Db()->fetchOne("SELECT COUNT(id) AS total FROM s_campaigns_mailaddresses WHERE email = ?", array(Shopware()->System()->_POST['email']));

        if ($mailQuery[total] >= 1) {
            echo 'true';
        }
    }

    /**
     * insert email address to newsletter archiv action is called if user submits the form
     * @return void
     */
    public function subscribeNewsletterAction()
    {
        $this->View()->assign('sUserLoggedIn', Shopware()->Modules()->Admin()->sCheckUser());
        $this->front->setParam('optinNow', (new \DateTime())->format('Y-m-d H:i:s'));
        $this->View()->assign('sUnsubscribe', false);
        $this->View()->assign('_POST', Shopware()->System()->_POST->toArray());

        if (!isset(Shopware()->System()->_POST['newsletter'])) {
            return;
        }

        $config = Shopware()->Container()->get('config');
        $noCaptchaAfterLogin = $config->get('noCaptchaAfterLogin');

        // redirect user if captcha is active and request is sent from the footer
        if ($config->get('newsletterCaptcha') !== 'noCaptcha'
            && $this->Request()->getPost('redirect') !== null
            && !($noCaptchaAfterLogin && Shopware()->Modules()->Admin()->sCheckUser())) {
            return;
        }

        if (empty($config->get('sOPTINNEWSLETTER'))) { // Routine ohne Double-Optin
            $this->View()->assign('sStatus', Shopware()->Modules()->Admin()->sNewsletterSubscription(Shopware()->System()->_POST['newsletter']));
            if ($this->View()->getAssign('sStatus')['code'] == 3 && $this->View()->getAssign('sStatus')['isNewRegistration']) {
                // Send mail to subscriber
                $this->sendMail(Shopware()->System()->_POST['newsletter'], 'sNEWSLETTERCONFIRMATION');
                echo Shopware()->Snippets()->getNamespace('frontend/account/internalMessages')->get('NewsletterSuccess');
            }
        } else { // Routine für Double-Optin
            $this->View()->sStatus = Shopware()->Modules()->Admin()->sNewsletterSubscription(Shopware()->System()->_POST['newsletter'], false);

            if ($this->View()->getAssign('sStatus')['code'] == 3) {
                if ($this->View()->getAssign('sStatus')['isNewRegistration']) {
                    Shopware()->Modules()->Admin()->sNewsletterSubscription(Shopware()->System()->_POST['newsletter'], true);
                    $hash = \Shopware\Components\Random::getAlphanumericString(32);
                    $data = serialize(Shopware()->System()->_POST->toArray());

                    $link = $this->Front()->Router()->assemble(['sViewport' => 'newsletter', 'action' => 'index', 'sConfirmation' => $hash]);

                    $this->sendMail(Shopware()->System()->_POST['newsletter'], 'sOPTINNEWSLETTER', $link);

                    Shopware()->Db()->query('
                    INSERT INTO s_core_optin (datum,hash,data,type)
                    VALUES (
                    now(),?,?,"swNewsletter"
                    )
                    ', [$hash, $data]);
                }

                $this->View()->assign('sStatus', ['code' => 3, 'message' => Shopware()->Snippets()->getNamespace('frontend')->get('sMailConfirmation')]);

                echo Shopware()->Snippets()->getNamespace('frontend')->get('sMailConfirmation');
            }
        }
    }
}