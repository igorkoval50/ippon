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

use Shopware\Models\Newsletter\Group;

// @codingStandardsIgnoreLine
class Shopware_Controllers_Backend_TlsNewsletterGroup extends Shopware_Controllers_Backend_ExtJs
{
    public function getGroupListAction()
    {
        $repository = $this->getModelManager()->getRepository(Group::class);

        $builder = $repository->createQueryBuilder('groups');
        $builder->select([
            'groups.id as id',
            'groups.name as name',
        ]);

        $builder->setFirstResult($this->Request()->getParam('start'))
            ->setMaxResults($this->Request()->getParam('limit'));
        $query = $builder->getQuery();

        $total = $this->getModelManager()->getQueryCount($query);
        $data = $query->getArrayResult();

        $this->View()->assign(['success' => true, 'data' => $data, 'total' => $total]);
    }
}
