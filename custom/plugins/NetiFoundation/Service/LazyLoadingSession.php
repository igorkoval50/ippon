<?php
/**
 * @copyright  Copyright (c) 2018, Net Inventors GmbH
 * @category   Shopware
 * @author     hrombach
 */

namespace NetiFoundation\Service;

use Shopware\Models\Shop\Shop as ShopModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/** @noinspection LongInheritanceChainInspection */
class LazyLoadingSession extends \Enlight_Components_Session_Namespace
{
    /**
     * @var \Enlight_Controller_Front
     */
    private $frontController;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Enlight_Components_Session_Namespace|false
     */
    private $session = false;

    /**
     * @var \Enlight_Components_Session_Namespace|false
     */
    private $backendSession = false;

    /** @noinspection MagicMethodsValidityInspection */
    /** @noinspection PhpMissingParentConstructorInspection */
    /**
     * UnifiedSessionFactory constructor.
     *
     * @param \Enlight_Controller_Front $frontController
     * @param ContainerInterface        $container
     */
    public function __construct(
        \Enlight_Controller_Front $frontController,
        ContainerInterface $container
    ) {
        $this->frontController = $frontController;
        $this->container       = $container;
    }

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->getSession()->offsetExists($key);
    }

    /**
     * @return \Enlight_Components_Session_Namespace
     */
    private function getSession()
    {
        if (
            'cli' === \PHP_SAPI ||
            !$this->container->has('shop') ||
            !$this->container->get('shop', ContainerInterface::NULL_ON_INVALID_REFERENCE) instanceof ShopModel
            || !\in_array($this->frontController->Request()->getModuleName(), ['frontend', 'widgets'], true)
        ) {
            return $this->backendSession ?: $this->backendSession = $this->container->get('backendsession');
        }

        return $this->session ?: $this->session = $this->container->get('session');
    }

    /**
     * @param string $key
     */
    public function offsetUnset($key)
    {
        $this->getSession()->offsetUnset($key);
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->getSession()->offsetGet($key);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->getSession()->offsetSet($key, $value);
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->getSession()->count();
    }

    /**
     * @param      $name
     * @param null $default
     *
     * @return null
     */
    public function get($name, $default = null)
    {
        return $this->getSession()->get($name, $default);
    }

    /**
     * @return \ArrayObject
     */
    public function getIterator()
    {
        return $this->getSession()->getIterator();
    }

    /**
     *
     */
    public function lock()
    {
        $this->getSession()->lock();
    }

    /**
     *
     */
    public function unlock()
    {
        $this->getSession()->unlock();
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->getSession()->isLocked();
    }

    /**
     * @return true
     */
    public function unsetAll()
    {
        return $this->getSession()->unsetAll();
    }

    /**
     * @param string $name
     *
     * @return mixed
     * @throws \Zend_Session_Exception
     */
    public function & __get($name)
    {
        return $this->getSession()->__get($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return true|void
     * @throws \Zend_Session_Exception
     */
    public function __set($name, $value)
    {
        $this->getSession()->__set($name, $value);
    }

    /**
     * @param array|string $callback
     *
     * @return mixed
     */
    public function apply($callback)
    {
        return $this->getSession()->apply($callback);
    }

    /**
     * @param array|string $callback
     *
     * @return mixed
     * @throws \Zend_Session_Exception
     */
    public function applySet($callback)
    {
        return $this->getSession()->applySet($callback);
    }

    /**
     * @param string $name
     *
     * @return bool
     * @throws \Zend_Session_Exception
     */
    public function __isset($name)
    {
        return $this->getSession()->__isset($name);
    }

    /**
     * @param string $name
     *
     * @return true
     * @throws \Zend_Session_Exception
     */
    public function __unset($name)
    {
        return $this->getSession()->__unset($name);
    }

    /**
     * @param int  $seconds
     * @param null $variables
     *
     * @throws \Zend_Session_Exception
     */
    public function setExpirationSeconds($seconds, $variables = null)
    {
        $this->getSession()->setExpirationSeconds($seconds, $variables);
    }

    /**
     * @param int  $hops
     * @param null $variables
     * @param bool $hopCountOnUsageOnly
     *
     * @throws \Zend_Session_Exception
     */
    public function setExpirationHops($hops, $variables = null, $hopCountOnUsageOnly = false)
    {
        $this->getSession()->setExpirationHops($hops, $variables, $hopCountOnUsageOnly);
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->getSession()->getNamespace();
    }
}
