<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components\Template;

use Enlight_View_Default;

class PluginVar
{
    /**
     * @var string
     */
    private $varKey = 'mediameetsFacebookPixel';

    /**
     * @var Enlight_View_Default
     */
    private $view;

    /**
     * @param Enlight_View_Default $view
     */
    public function __construct(Enlight_View_Default $view)
    {
        $this->view = $view;
    }

    /**
     * @param array|null $value
     */
    public function set($value)
    {
        $this->view->assign($this->varKey, $value);
    }

    /**
     * @return array|null
     */
    public function get()
    {
        return $this->view->getAssign($this->varKey);
    }

    /**
     * @param array|null $data
     */
    public function setData($data)
    {
        $viewData = $this->get();
        $viewData['data'] = $data;
        $this->set($viewData);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $viewData = $this->get();
        return isset($viewData['data']) ? $viewData['data'] : [];
    }
}
