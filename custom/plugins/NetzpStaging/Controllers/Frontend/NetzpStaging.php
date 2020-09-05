<?php
use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_NetzpStaging extends \Enlight_Controller_Action
                                                 implements CSRFWhitelistAware
{
    const SECRET = 'kTZGnbLjCDDjMR34';

    public function getWhitelistedCSRFActions()
    {
        return [
            'clearcache'
        ];
    }

    public function clearcacheAction()
    {
        $secret = $this->Request()->getParam('secret');
        if($secret != self::SECRET) {
            die('ts...');
        }

        $cache = Shopware()->Container()->get('shopware.cache_manager');
        $cache->clearHttpCache();
        $cache->clearTemplateCache();
        $cache->clearConfigCache();
        $cache->clearOpCache();

        $this->setResponseData(json_encode(['result' => 1]));
    }

    function setResponseData($body) {

        Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $this->Response()->setHeader('Cache-Control', 'post-check=0, pre-check=0', true);
        $this->Response()->setHeader('Pragma', 'no-cache', true);
        $this->Response()->setBody($body);
    }

}