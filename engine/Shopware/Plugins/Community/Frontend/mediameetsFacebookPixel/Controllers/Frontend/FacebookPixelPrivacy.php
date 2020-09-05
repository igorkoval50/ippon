<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

class Shopware_Controllers_Frontend_FacebookPixelPrivacy extends Enlight_Controller_Action
{
    /**
     * Shows page with activation or deactivation message,
     * depending on configured privacy mode.
     */
    public function closeAction()
    {
        $this->View()->loadTemplate('frontend/facebook_pixel_privacy/close.tpl');
    }

    /**
     * Shows page with activation message.
     */
    public function activateAction()
    {
        $this->View()->loadTemplate('frontend/facebook_pixel_privacy/activate.tpl');
    }

    /**
     * Shows page with deactivation message.
     */
    public function deactivateAction()
    {
        $this->View()->loadTemplate('frontend/facebook_pixel_privacy/deactivate.tpl');
    }
}
