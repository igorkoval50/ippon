<?php
/**
 * Copyright (c) TreoLabs GmbH
 *
 * This Software is the property of TreoLabs GmbH and is protected
 * by copyright law - it is NOT Freeware and can be used only in one project
 * under a proprietary license, which is delivered along with this program.
 * If not, see <https://treolabs.com/eula>.
 *
 * This Software is distributed as is, with LIMITED WARRANTY AND LIABILITY.
 * Any unauthorised use of this Software without a valid license is
 * a violation of the License Agreement.
 *
 * According to the terms of the license you shall not resell, sublicense,
 * rent, lease, distribute or otherwise transfer rights or usage of this
 * Software or its derivatives. You may modify the code of this Software
 * for your own needs, if source code is provided.
 */

namespace TlsNewsletterGroup\Components;

use Doctrine\DBAL\Connection;
use Exception;
use PDO;
use Shopware\Components\Model\ModelManager;

class GroupService
{
    /**
     * @var PluginConfig
     */
    private $pluginConfig;
    /**
     * @var ModelManager
     */
    private $em;

    /**
     * GroupService constructor.
     * @param PluginConfig $pluginConfig
     * @param ModelManager $em
     */
    public function __construct(PluginConfig $pluginConfig, ModelManager $em)
    {
        $this->pluginConfig = $pluginConfig;
        $this->em = $em;
    }

    public function getActiveGroupList($email = null)
    {
        $activeIds = $this->pluginConfig->get('groups');
        if (is_string($activeIds)) {
            $activeIds = array_map('trim', explode(',', $activeIds));
        }

        $builder = $this->em->getDBALQueryBuilder();
        $builder->select('cg.id', 'cg.name')
            ->from('s_campaigns_groups', 'cg')
            ->where('cg.id in (:ids) or cg.name in (:names)')
            ->setParameter('ids', $activeIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('names', $activeIds, Connection::PARAM_STR_ARRAY); // for shopware 5.5

        if ($email) {
            $builder->leftJoin(
                'cg',
                's_campaigns_mailaddresses',
                'mailaddresses',
                'cg.id = mailaddresses.groupID and mailaddresses.email = :email'
            )
                ->addSelect('max(mailaddresses.id) as active')
                ->setParameter('email', $email)
                ->groupBy('id, name');
        }

        return $builder->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param $email
     * @param $groups
     * @param $defaultGroupId
     * @param $added
     * @param null $doubleOptInConfirmed
     * @throws Exception
     */
    public function subscribeNewsletter($email, $groups, $defaultGroupId, $added, $doubleOptInConfirmed = null)
    {
        $builder = $this->em->getDBALQueryBuilder();
        $builder->select('*')->from('s_campaigns_mailaddresses')
            ->where('email = :email')
            ->setParameter('email', $email);

        $results = $builder->execute()->fetchAll(PDO::FETCH_ASSOC);

        $update = $remove = [];
        if (!in_array($defaultGroupId, $groups)) {
            foreach ($results as $result) {
                if ($result['groupID'] == $defaultGroupId) {
                    $update[] = $result; // For update first default group
                    break;
                }
            }
        }

        foreach ($results as $result) {
            if (($key = array_search($result['groupID'], $groups)) !== false) {
                unset($groups[$key]); // Already inserted
            } elseif ($result['groupID'] != $defaultGroupId) {
                $remove[] = $result; // To remove
            }
        }

        foreach ($remove as $result) {
            $this->em->getConnection()->delete('s_campaigns_mailaddresses', ['id' => $result['id']]);
        }

        if (empty($groups)) {
            return;
        }

        foreach ($groups as $group) {
            if (!empty($update)) {
                $result = array_shift($update);

                $this->em->getConnection()
                    ->update(
                        's_campaigns_mailaddresses',
                        ['groupID' => $group, 'customer' => 0],
                        ['id' => $result['id']]
                    );
            } else {
                $this->em->getConnection()->insert('s_campaigns_mailaddresses', [
                    'customer' => 0,
                    'groupID' => $group,
                    'email' => $email,
                    'added' => $added,
                    'double_optin_confirmed' => $doubleOptInConfirmed,
                ]);
            }
        }
    }

    /**
     * @param string $email
     * @param int[] $groups
     */
    public function updateOptinData($email, $groups)
    {
        $id = $this->em->getConnection()->lastInsertId();
        $this->em->getConnection()->update('s_core_optin', [
            'data' => serialize([
                'newsletter' => $email,
                'subscribeToNewsletter' => true,
                'tls_newsletter_groups' => $groups,
            ])
        ], ['id' => $id]);
    }
}
