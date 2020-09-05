<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\PluginManager;

use NetiFoundation\Service\Logging\LoggingServiceInterface;
use NetiFoundation\Struct\PluginConfigFile\Acl as AclConfig;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Models\Plugin\Plugin as PluginModel;
use Shopware\Models\User\Privilege;
use Shopware\Models\User\Resource;
use Shopware\Models\User\Rule;

/**
 * Class Acl
 *
 * @package NetiFoundation\Service\PluginManager
 */
class Acl implements AclInterface
{
    /** @var ModelManager */
    protected $em;

    /** @var \Shopware_Components_Acl */
    protected $acl;

    /** @var LoggingServiceInterface */
    protected $loggingService;

    /**
     * @param ModelManager             $em
     * @param \Shopware_Components_Acl $acl
     * @param LoggingServiceInterface  $loggingService
     */
    public function __construct(
        ModelManager $em,
        \Shopware_Components_Acl $acl,
        LoggingServiceInterface $loggingService
    ) {
        $this->em             = $em;
        $this->acl            = $acl;
        $this->loggingService = $loggingService;
    }

    /**
     * @param Plugin      $plugin
     * @param AclConfig[] $acl
     *
     * @throws \Exception
     */
    public function installAcl(Plugin $plugin, array $acl)
    {
        $this->updateAcl($plugin, $acl);
    }

    /**
     * @param Plugin      $plugin
     * @param AclConfig[] $acl
     *
     * @throws \Exception|null
     */
    public function updateAcl(Plugin $plugin, array $acl)
    {
        $logs      = ['success' => true, 'resources' => []];
        $exception = null;

        try {
            $swAcl = $this->acl;
            if ($swAcl instanceof \Shopware_Components_Acl) {
                foreach ($acl as $entry) {
                    $resourceName = $entry->getResourceName();
                    $privileges   = $entry->getPrivileges();

                    $log = array(
                        'resource' => $resourceName,
                        'privileges' => $privileges,
                        'success' => false
                    );

                    if (! is_array($privileges)) {
                        $privileges = array($privileges);
                    }

                    $pluginModel = $this->em->getRepository(PluginModel::class)
                        ->findOneBy(['name' => $plugin->getName()]);

                    if (!$pluginModel instanceof PluginModel) {
                        throw new \RuntimeException(\sprintf(
                            'No plugin model found for name "%s"',
                            $plugin->getName()
                        ));
                    }

                    // create resources and privileges
                    try {
                        $swAcl->createResource($resourceName, $privileges, null, $pluginModel->getId());
                    } catch (\Enlight_Exception $e) {
                    }

                    /**
                     * @var Resource $resource
                     */
                    $resource = $this->em->getRepository('Shopware\Models\User\Resource')
                        ->findOneBy(array('name' => $resourceName));

                    if ($resource instanceof Resource) {

                        $log['success'] = true;

                        // authorize admin for the resource
                        $resourceID = $resource->getId();

                        // get existing rules for the current resource
                        /** @var Rule[] $existingRules */
                        $existingRules = $this->em->getRepository('Shopware\Models\User\Rule')
                            ->findBy(['roleId' => 1, 'resourceId' => $resourceID]);

                        $existingRulePrivileges = [];
                        foreach ($existingRules as $existingRule) {
                            $existingRulePrivileges[] = $existingRule->getPrivilegeId();
                        }

                        // only add new rule, if it does not exist yet
                        if (! in_array(null, $existingRulePrivileges)) {
                            $rule = new Rule();
                            $rule->setRoleId(1);
                            $rule->setResourceId($resourceID);
                            $rule->setPrivilegeId(null);

                            $this->em->persist($rule);
                        }

                        /** @var Privilege[] $privileges */
                        $privileges = $this->em->getRepository('Shopware\Models\User\Privilege')
                            ->findBy(array('resourceId' => $resourceID));

                        // authorize admin for each privilege
                        foreach ($privileges as $privilege) {
                            /**
                             * @var Privilege $privilege
                             */
                            if ($privilege instanceof Privilege) {
                                // only add new rule, if it does not exist yet
                                if (! in_array($privilege->getId(), $existingRulePrivileges)) {
                                    $rule = new Rule();
                                    $rule->setRoleId(1);
                                    $rule->setResourceId($resourceID);
                                    $rule->setPrivilegeId($privilege->getId());

                                    $this->em->persist($rule);
                                }
                            }
                        }
                        $this->em->flush();
                    }
                    $logs['resources'][] = $log;
                }
                $logs['success'] = true;
            }
        } catch (\Exception $e) {
            $exception = $e;
            $logs['success'] = false;
            $logs['message'] = 'Error ' .  $e->getCode() . ': ' . $e->getMessage();
        }

        $this->loggingService->write(
            $plugin->getName(),
            __FUNCTION__,
            $logs['success'] ? 'Successful' : 'Error',
            ['acl' => $logs]
        );

        if ($exception) {
            throw $exception;
        }
    }

    /**
     * @param Plugin $plugin
     */
    public function removeAcl(Plugin $plugin)
    {
        $logs = ['success' => false, 'resources' => []];

        $pluginModel = $this->em->getRepository(PluginModel::class)
                                ->findOneBy(['name' => $plugin->getName()]);

        if (!$pluginModel instanceof PluginModel) {
            return;
        }

        try {
            $swAcl = $this->acl;
            if ($swAcl instanceof \Shopware_Components_Acl) {
                $resources = $this->em->getRepository(Resource::class)
                    ->findBy(['pluginId' => $pluginModel->getId()]);

                $resourceNames = [];
                $resourceIds   = [];
                $privilegeIds  = [];

                foreach ($resources as $resource) {
                    if ($resource instanceof Resource) {
                        $resourceNames[] = $resource->getName();
                        $resourceIds[]   = $resource->getId();

                        foreach ($resource->getPrivileges() as $privilege) {
                            if ($privilege instanceof Privilege) {
                                $privilegeIds[] = $privilege->getId();
                            }
                        }
                    }
                }

                $log = array(
                    'resources' => $resourceNames,
                    'success' => false
                );

                $connection = $this->em->getConnection();
                if ([] !== $privilegeIds) {
                    $implodedIds = \implode(',', $privilegeIds);
                    $connection->exec(
                        'DELETE FROM s_core_acl_privilege_requirements WHERE privilege_id IN ('
                        . $implodedIds
                        . ') OR required_privilege_id IN ('
                        . $implodedIds
                        . ')'
                    );

                    $connection->exec(
                        'DELETE FROM s_core_acl_privileges WHERE id IN ('. $implodedIds .')'
                    );
                }

                if ([] !== $resourceIds) {
                    $implodedIds = \implode(',', $resourceIds);

                    $connection->exec(
                        'DELETE FROM s_core_acl_roles WHERE resourceID IN ('. $implodedIds .')'
                    );

                    $connection->exec(
                        'DELETE FROM s_core_acl_resources WHERE id IN ('. $implodedIds .')'
                    );
                }

                $log['success'] = true;
            }
            $logs['success'] = true;
        } catch (\Exception $e) {
            $logs['message'] = 'Error ' .  $e->getCode() . ': ' . $e->getMessage();
        }

        $this->loggingService->write(
            $plugin->getName(),
            __FUNCTION__,
            $logs['success'] ? 'Successful' : 'Error',
            ['acl' => $logs]
        );
    }
}
