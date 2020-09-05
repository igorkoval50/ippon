<?php
use MagNewsletterBox\Models\ReservedCodes;
use Shopware\Models\Voucher\Code;

class Shopware_Controllers_Backend_MagNewsletterBox extends \Shopware_Controllers_Backend_Application
{
    protected $model = 'MagNewsletterBox\Models\ReservedCodes';
    protected $alias = 'evc';

    /**
     * Helper function which creates the listing query builder.
     * If the class property model isn't configured, the init function throws an exception.
     * The listing alias for the from table can be configured over the class property alias.
     *
     * @return QueryBuilder
     */
    protected function getListQuery()
    {
        $builder = parent::getListQuery();
        $builder = $this->getManager()->createQueryBuilder();

        $search = $this->request->getParam('filter');
        $search = $search[0]['value'];

        if ($search) {
            $builder->addSelect([
                'codes.code',
                'evc.id',
                'evc.email',
            ])
                ->from('MagNewsletterBox\Models\ReservedCodes', 'evc')
                ->innerJoin('Shopware\Models\Voucher\Code', 'codes', 'WITH', 'evc.voucherCodeID = codes.id')
                ->where('codes.code LIKE :search OR evc.email LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        } else {
            $builder->addSelect([
                'codes.code',
                'evc.id',
                'evc.email',
            ])
                ->from('MagNewsletterBox\Models\ReservedCodes', 'evc')
                ->innerJoin('Shopware\Models\Voucher\Code', 'codes','WITH', 'evc.voucherCodeID = codes.id');
        }

        return $builder;
    }

    /**
     * Internal function which deletes the configured model with the passed identifier.
     * This function is used from the {@link #deleteAction} function which can be called over an ajax request.
     * The function can returns three different states:
     *  1. array('success' => false, 'error' => 'The id parameter contains no value.')
     *   => The passed $id parameter is empty
     *  2. array('success' => false, 'error' => 'The passed id parameter exists no more.')
     *   => The passed $id parameter contains no valid id for the configured model and the entity manager find function returns no valid entity.
     *  3. array('success' => true)
     *   => Delete was successfully.
     *
     * @param int $id
     *
     * @return array
     */
    public function delete($id)
    {
        if (empty($id)) {
            return ['success' => false, 'error' => 'The id parameter contains no value.'];
        }

        /** @var \Doctrine\DBAL\Query\QueryBuilder $query */
        $query = $this->container->get('dbal_connection')->createQueryBuilder();

        $query->delete('s_plugin_mag_emarketing_voucher_codes')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();

        return ['success' => true];
    }
}