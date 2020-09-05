<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Subscriber\Frontend;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs;

class Newsletter implements SubscriberInterface
{
    /**
     * Returns the subscribed events and their listeners.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Newsletter' => 'onPostDispatchSecureNewsletter',
        ];
    }

    /**
     * Add the configured opt-in newsletter state to the view.
     *
     * @param Enlight_Controller_ActionEventArgs $args
     */
    public function onPostDispatchSecureNewsletter(Enlight_Controller_ActionEventArgs $args)
    {
        $args->getSubject()
            ->View()
            ->assign(
                'newsletterIsOptIn',
                Shopware()->Config()->get('sOPTINNEWSLETTER')
            );
    }
}
