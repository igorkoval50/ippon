<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     rubyc
 */

namespace NetiLanguageDetector\Subscriber;

use Enlight\Event\SubscriberInterface;
use NetiFoundation\Service\PluginManager\Config;
use NetiLanguageDetector\Struct\PluginConfig;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop;
use Shopware\Bundle\CookieBundle\CookieCollection;
use Shopware\Bundle\CookieBundle\Structs\CookieGroupStruct;
use Shopware\Bundle\CookieBundle\Structs\CookieStruct;

class Frontend  implements SubscriberInterface
{
    /**
     * @var ModelManager
     */
    private $em;

    /**
     * @var PluginConfig
     */
    private $config;

    /**
     * @var \Shopware\Models\Shop\Shop
     */
    private $currentShop;

    /**
     * Frontend constructor.
     *
     * @param ModelManager            $em
     * @param Config                  $configService
     * @param Shop                    $currentShop
     *
     * @throws \Exception
     */
    public function __construct(
        ModelManager $em,
        Config $configService,
        Shop $currentShop
    ) {
        $this->em          = $em;
        $this->config      = $configService->getPluginConfig($this);
        $this->currentShop = $currentShop;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatchFrontend',
            'CookieCollector_Collect_Cookies'                       => 'addCookie',
        ];
    }

    public function addCookie()
    {
        $collection = new CookieCollection();
        $collection->add(new CookieStruct(
            'disable-redirect',
            '/^disable-redirect$/',
            'Language detector',
            CookieGroupStruct::TECHNICAL
        ));

        return $collection;
    }

    /**
     * @throws \Exception
     */
    public function onPostDispatchFrontend(\Enlight_Controller_ActionEventArgs $args): void
    {
        // do nothing if a bot is detected
        if (
            $this->config->isSubShopdeactivate()
        ) {
            return;
        }

        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view       = $controller->View();

        /** @var \Enlight_Controller_Request_RequestHttp $request */
        $request  = $controller->Request();

        $server = $request->getServer();
        if (\Zend_Http_UserAgent_Bot::match($server['HTTP_USER_AGENT'], $server)) {
            return;
        }

        /** @var \Enlight_Controller_Response_ResponseHttp $response */
        $response = $controller->Response();

        // delete cookie value for "shop" to suppress another redirect
        if ('1' === $request->getParam('neti-redirecting')) {
            $targetShop = $this->em->getRepository(Shop::class)->findOneBy([
                'id' => $request->getParam('__shop'),
            ]);

            if (!$targetShop instanceof Shop || !$this->currentShop instanceof Shop) {
                return;
            }

            $condition1 = $targetShop->getMain() instanceof Shop
                          && $targetShop->getMain()->getId() === $this->currentShop->getId();

            $condition2 = $this->currentShop->getMain() instanceof Shop
                          && $targetShop->getId() === $this->currentShop->getMain()->getId();

            if (!($condition1 || $condition2)) {
                $response->setCookie('shop');
            }

            if ($request->getParam('__shop', false)) {
                $redirectUrl = $this->getNewShopUrl($request, $targetShop);
                $controller->redirect($redirectUrl);
            }

            return;
        }

        $view->assign('runNetiLanguageDetector', true);
    }

    /**
     * @param \Enlight_Controller_Request_RequestHttp $request
     * @param \Shopware\Models\Shop\Shop              $newShop
     *
     * @return string
     */
    protected function getNewShopUrl(
        \Enlight_Controller_Request_RequestHttp $request,
        Shop $newShop
    ) {
        // Remove baseUrl from request url
        $url = $request->getRequestUri();

        $repository  = $this->em->getRepository(Shop::class);
        $requestShop = $repository->getActiveShopByRequestAsArray($request);
        if ($requestShop && strpos($url, $requestShop['base_url']) === 0) {
            $url = substr($url, strlen($requestShop['base_url']));
        }

        $baseUrl = $request->getBaseUrl();
        if (strpos($url, $baseUrl . '/') === 0) {
            $url = substr($url, strlen($baseUrl));
        }

        $basePath = $newShop->getBasePath();
        if (strpos($url, $basePath) === 0) {
            $url = substr($url, strlen($basePath));
        }

        $host    = $newShop->getHost();
        $baseUrl = $newShop->getBaseUrl() ?: $request->getBasePath();

        if ($request->isSecure()) {
            if ($newShop->getBaseUrl()) {
                $baseUrl = $newShop->getBaseUrl();
            } else {
                $baseUrl = $request->getBaseUrl();
            }
        }

        $host    = trim($host, '/');
        $baseUrl = trim($baseUrl, '/');
        if (!empty($baseUrl)) {
            $baseUrl = '/' . $baseUrl;
        }

        $url = ltrim($url, '/');
        if (!empty($url)) {
            $url = '/' . $url;
        }

        //build full redirect url to allow host switches
        return sprintf(
            '%s://%s%s%s',
            $request->getScheme(),
            $host,
            $baseUrl,
            $url
        );
    }
}
