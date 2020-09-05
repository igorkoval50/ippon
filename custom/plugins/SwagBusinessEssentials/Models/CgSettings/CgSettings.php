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

namespace SwagBusinessEssentials\Models\CgSettings;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_core_plugins_b2b_cgsettings", options={"collate":"utf8_unicode_ci"})
 */
class CgSettings extends ModelEntity
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
     * @ORM\Column(name="allowregister", type="boolean", nullable=false)
     */
    protected $allowRegister;

    /**
     * @var int
     *
     * @ORM\Column(name="requireunlock", type="boolean", nullable=false)
     */
    protected $requireUnlock;

    /**
     * @var string
     *
     * @ORM\Column(name="assigngroupbeforeunlock", type="string", length=10, nullable=false)
     */
    protected $assignGroupBeforeUnlock;

    /**
     * @var string
     *
     * @ORM\Column(name="registertemplate", type="string", nullable=false)
     */
    protected $registerTemplate;

    /**
     * @var string
     *
     * @ORM\Column(name="emailtemplatedeny", type="string", nullable=false)
     */
    protected $emailTemplateDeny;

    /**
     * @var string
     *
     * @ORM\Column(name="emailtemplateallow", type="string", nullable=false)
     */
    protected $emailTemplateAllow;

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
     * @return int
     */
    public function getAllowRegister()
    {
        return $this->allowRegister;
    }

    /**
     * @param int $allowRegister
     *
     * @return $this
     */
    public function setAllowRegister($allowRegister)
    {
        $this->allowRegister = $allowRegister;

        return $this;
    }

    /**
     * @return int
     */
    public function getRequireUnlock()
    {
        return $this->requireUnlock;
    }

    /**
     * @param int $requiredUnlock
     *
     * @return $this
     */
    public function setRequireUnlock($requiredUnlock)
    {
        $this->requireUnlock = $requiredUnlock;

        return $this;
    }

    /**
     * @return string
     */
    public function getAssignGroupBeforeUnlock()
    {
        return $this->assignGroupBeforeUnlock;
    }

    /**
     * @param string $assignGroupBeforeUnlock
     *
     * @return $this
     */
    public function setAssignGroupBeforeUnlock($assignGroupBeforeUnlock)
    {
        $this->assignGroupBeforeUnlock = $assignGroupBeforeUnlock;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegisterTemplate()
    {
        return $this->registerTemplate;
    }

    /**
     * @param string $registerTemplate
     *
     * @return $this
     */
    public function setRegisterTemplate($registerTemplate)
    {
        $this->registerTemplate = $registerTemplate;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailTemplateDeny()
    {
        return $this->emailTemplateDeny;
    }

    /**
     * @param string $emailTemplateDeny
     *
     * @return $this
     */
    public function setEmailTemplateDeny($emailTemplateDeny)
    {
        $this->emailTemplateDeny = $emailTemplateDeny;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailTemplateAllow()
    {
        return $this->emailTemplateAllow;
    }

    /**
     * @param string $emailTemplateAllow
     *
     * @return $this
     */
    public function setEmailTemplateAllow($emailTemplateAllow)
    {
        $this->emailTemplateAllow = $emailTemplateAllow;

        return $this;
    }
}
