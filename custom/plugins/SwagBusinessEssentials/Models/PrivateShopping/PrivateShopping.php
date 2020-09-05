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

namespace SwagBusinessEssentials\Models\PrivateShopping;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_core_plugins_b2b_private", options={"collate":"utf8_unicode_ci"})
 */
class PrivateShopping extends ModelEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="customergroup", type="string", length=10, nullable=false)
     */
    protected $customerGroup;

    /**
     * @var int
     *
     * @ORM\Column(name="activatelogin", type="smallint", nullable=false)
     */
    protected $activateLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="redirectlogin", type="string", nullable=false)
     */
    protected $redirectLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="redirectregistration", type="string", nullable=false)
     */
    protected $redirectRegistration;

    /**
     * @var string whiteListedControllers
     *
     * @ORM\Column(name="whitelistedcontrollers", type="string", nullable=true)
     */
    protected $whiteListedControllers;

    /**
     * @var int
     *
     * @ORM\Column(name="registerlink", type="smallint", nullable=false)
     */
    protected $registerLink;

    /**
     * @var string
     *
     * @ORM\Column(name="registergroup", type="string", length=50, nullable=false)
     */
    protected $registerGroup;

    /**
     * @var int
     *
     * @ORM\Column(name="unlockafterregister", type="smallint", nullable=false)
     */
    protected $unlockAfterRegister;

    /**
     * @var string
     *
     * @ORM\Column(name="templatelogin", type="string", length=50, nullable=false)
     */
    protected $templateLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="templateafterlogin", type="string", length=50, nullable=false)
     */
    protected $templateAfterLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="redirectURL", type="string", length=1024, nullable=true)
     */
    protected $redirectURL;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $customerGroup
     *
     * @return $this
     */
    public function setCustomerGroup($customerGroup)
    {
        $this->customerGroup = $customerGroup;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerGroup()
    {
        return $this->customerGroup;
    }

    /**
     * @param int $activateLogin
     *
     * @return $this
     */
    public function setActivateLogin($activateLogin)
    {
        $this->activateLogin = $activateLogin;

        return $this;
    }

    /**
     * @return int
     */
    public function getActivateLogin()
    {
        return $this->activateLogin;
    }

    /**
     * @param string $redirectLogin
     *
     * @return $this
     */
    public function setRedirectLogin($redirectLogin)
    {
        $this->redirectLogin = $redirectLogin;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectLogin()
    {
        return $this->redirectLogin;
    }

    /**
     * @param string $redirectRegistration
     *
     * @return $this
     */
    public function setRedirectRegistration($redirectRegistration)
    {
        $this->redirectRegistration = $redirectRegistration;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectRegistration()
    {
        return $this->redirectRegistration;
    }

    /**
     * @return string
     */
    public function getWhiteListedControllers()
    {
        return $this->whiteListedControllers;
    }

    /**
     * @param string $whiteListedControllers
     *
     * @return $this
     */
    public function setWhiteListedControllers($whiteListedControllers)
    {
        $this->whiteListedControllers = $whiteListedControllers;

        return $this;
    }

    /**
     * @param int $registerLink
     *
     * @return $this
     */
    public function setRegisterLink($registerLink)
    {
        $this->registerLink = $registerLink;

        return $this;
    }

    /**
     * @return int
     */
    public function getRegisterLink()
    {
        return $this->registerLink;
    }

    /**
     * @param string $registerGroup
     *
     * @return $this
     */
    public function setRegisterGroup($registerGroup)
    {
        $this->registerGroup = $registerGroup;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegisterGroup()
    {
        return $this->registerGroup;
    }

    /**
     * @param int $unlockAfterRegister
     *
     * @return $this
     */
    public function setUnlockAfterRegister($unlockAfterRegister)
    {
        $this->unlockAfterRegister = $unlockAfterRegister;

        return $this;
    }

    /**
     * @return int
     */
    public function getUnlockAfterRegister()
    {
        return $this->unlockAfterRegister;
    }

    /**
     * @param string $templateLogin
     *
     * @return $this
     */
    public function setTemplateLogin($templateLogin)
    {
        $this->templateLogin = $templateLogin;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateLogin()
    {
        return $this->templateLogin;
    }

    /**
     * @param string $templateAfterLogin
     *
     * @return $this
     */
    public function setTemplateAfterLogin($templateAfterLogin)
    {
        $this->templateAfterLogin = $templateAfterLogin;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateAfterLogin()
    {
        return $this->templateAfterLogin;
    }

    /**
     * @return string
     */
    public function getRedirectURL()
    {
        return $this->redirectURL;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setRedirectURL($url)
    {
        $this->redirectURL = $url;

        return $this;
    }
}
