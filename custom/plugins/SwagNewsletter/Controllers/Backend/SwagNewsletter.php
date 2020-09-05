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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Shopware\Models\Newsletter\Newsletter;
use Shopware\Models\Newsletter\Repository;
use Shopware\Models\Plugin\Plugin;
use Shopware\Models\Shop\Locale;
use SwagNewsletter\Components\LiveShopping\LiveShoppingCompatibilityException;
use SwagNewsletter\Models\Component;
use SwagNewsletter\Models\Newsletter as CustomNewsletter;
use SwagNewsletter\Models\Repository as ComponentRepository;

class Shopware_Controllers_Backend_SwagNewsletter extends Shopware_Controllers_Backend_NewsletterManager
{
    /**
     * Used to map between the new emotion-style containers and the old newsletter containers
     *
     * @var array
     */
    protected $componentMapping = [];

    /**
     * @return Repository
     */
    public function getCampaignsRepository()
    {
        return $this->get('models')->getRepository(Newsletter::class);
    }

    /**
     * Get a list of existing newsletters
     */
    public function listNewslettersAction()
    {
        $filter = $this->Request()->getParam('filter');
        $sort = $this->Request()->getParam('sort', [['property' => 'mailing.date', 'direction' => 'DESC']]);
        $limit = $this->Request()->getParam('limit', 10);
        $offset = $this->Request()->getParam('start', 0);

        $modelManager = $this->get('models');
        $dbalConnection = $this->get('dbal_connection');

        // Get the revenue for the newsletters
        $sql = "SELECT
                partnerID, COUNT(partnerID) AS orders,
                ROUND(SUM((o.invoice_amount_net-o.invoice_shipping_net)/currencyFactor),2) AS `revenue`
            FROM
                `s_order` AS o
            WHERE
                o.status != 4
            AND
                o.status != -1
            AND
                o.partnerID <> ''
            GROUP BY o.partnerID";
        $revenues = $dbalConnection->query($sql)->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_GROUP);

        // Delete old previews
        $results = $this->getComponentRepository()->getPreviewNewslettersQuery()->getResult();
        foreach ($results as $model) {
            $modelManager->remove($model);
        }
        $modelManager->flush();

        //get newsletters
        $query = $this->getCampaignsRepository()->getListNewslettersQuery($filter, $sort, $limit, $offset);

        $query->setHydrationMode(AbstractQuery::HYDRATE_ARRAY);
        $paginator = $modelManager->createPaginator($query);
        $result = $paginator->getIterator()->getArrayCopy();

        $newsletterIds = [];
        foreach ($result as $newsletter) {
            $newsletterIds[] = $newsletter['id'];
        }

        $querBuilder = $dbalConnection->createQueryBuilder();
        $querBuilder->select('lastmailing, COUNT(lastmailing) as addressCount')
            ->from('s_campaigns_mailaddresses')
            ->where('lastmailing IN (:newsletterIds)')
            ->groupBy('lastmailing')
            ->setParameter('newsletterIds', $newsletterIds, Connection::PARAM_INT_ARRAY);

        $addressesCount = $querBuilder->execute()->fetchAll(\PDO::FETCH_KEY_PAIR);

        foreach ($result as &$item) {
            $item['groups'] = $this->unserializeGroup($item['groups']);

            $item['addresses'] = 0;
            if (isset($addressesCount[$item['id']])) {
                $item['addresses'] = (int) $addressesCount[$item['id']];
            }

            $sCampaignId = 'sCampaign' . $item['id'];
            if (isset($revenues[$sCampaignId])) {
                $revenue = $revenues[$sCampaignId][0]['revenue'];
                $orders = $revenues[$sCampaignId][0]['orders'];
                if ($revenue !== null) {
                    $item['revenue'] = $revenue;
                    $item['orders'] = $orders;
                }
            }
        }
        unset($item);

        $this->View()->assign(
            [
                'success' => true,
                'data' => $result,
                'total' => $paginator->count(),
            ]
        );
    }

    /**
     * Loads necessary additional data for a newsletter.
     */
    public function detailAction()
    {
        $newsletterId = $this->Request()->getParam('id');

        /** @var ComponentRepository $newsletterRepository */
        $newsletterRepository = $this->getModelManager()->getRepository(Component::class);
        $newsletter = $newsletterRepository->getNewsletterDetailQuery($newsletterId)->getArrayResult()[0];

        $newsletter = $this->get('swag_newsletter.components.newsletter_helper')->getNewsletterElements($newsletter);

        $newsletter['groups'] = $this->unserializeGroup($newsletter['groups']);

        foreach ($newsletter['elements'] as &$element) {
            $element['component'] = $this->translateComponents($element['component']);
        }
        unset($element);

        $this->View()->assign(['data' => $newsletter, 'success' => true]);
    }

    /**
     * Update an existing newsletter model from passed data
     */
    public function updateNewsletterAction()
    {
        $id = $this->Request()->getParam('id');
        $duplicate = $this->Request()->getParam('duplicate', false);
        $newsletterHelper = $this->get('swag_newsletter.components.newsletter_helper');
        $modelManager = $this->get('models');

        // If the record needs to be duplicated, modify some fields and redirect to the createNewsletterAction
        if ($duplicate) {
            $this->Request()->setParam('locked', 0);
            $this->Request()->setParam('read', 0);
            $this->Request()->setParam('status', 0);
            $this->Request()->setParam('clicked', 0);
            $this->Request()->setParam(
                'subject',
                $this->getNameForDuplicatedNewsletter($this->Request()->getParam('subject'))
            );

            /** @var ComponentRepository $newsletterRepository */
            $newsletterRepository = $modelManager->getRepository(CustomNewsletter::class);
            $newsletterArray = $newsletterRepository->getNewsletterDetailQuery($id)->getArrayResult()[0];
            $newsletterArray = $newsletterHelper->getNewsletterElements($newsletterArray);

            $this->Request()->setParam('elements', $newsletterArray['elements']);

            $this->forward('createNewsletter');

            return;
        }

        if ($id === null) {
            $this->View()->assign(['success' => false, 'message' => 'no id passed']);

            return;
        }

        $data = $this->Request()->getParams();
        if ($data === null) {
            $this->View()->assign(['success' => false, 'message' => 'no data passed']);

            return;
        }

        if (!isset($data['timedDelivery'])) {
            $data['timedDelivery'] = null;
        }

        $modelManager->getConnection()->beginTransaction(); // suspend auto-commit

        try {
            // first of all get rid of the old containers and text fields
            $this->clearNewsletterData($id);

            //don't touch the date
            unset($data['date'], $data['locked']);
            $elements = $data['elements'];
            unset($data['elements']);
            $data['groups'] = $this->serializeGroup($data['groups']);

            /* @var Newsletter $model */
            $model = $modelManager->find(Newsletter::class, $id);

            if (!$model instanceof Newsletter) {
                $this->View()->assign(['success' => false, 'message' => 'newsletter not found']);

                return;
            }

            $model->fromArray($data);

            $modelManager->persist($model);

            $newsletterHelper->saveNewsletterElements($model, $elements);
            $newsletterHelper->saveThirdPartyElements($model, $elements);

            $modelManager->flush();

            $modelManager->getConnection()->commit();
            $modelManager->clear();
        } catch (\Exception $e) {
            $modelManager->getConnection()->rollBack();
            $modelManager->close();
            $this->View()->assign(['success' => false, 'data' => $e->getMessage()]);

            return;
        }

        $this->View()->assign(['success' => true, 'data' => null]);
    }

    /**
     * Get available vouchers
     * */
    public function getVoucherAction()
    {
        $sql = "SELECT s_emarketing_vouchers.id, s_emarketing_vouchers.description, s_emarketing_vouchers.value, s_emarketing_vouchers.numberofunits, IF(s_emarketing_vouchers.percental = 1, '%', '€') AS type_sign
            FROM s_emarketing_vouchers
            WHERE  s_emarketing_vouchers.modus = 1 AND (s_emarketing_vouchers.valid_to >= now() OR s_emarketing_vouchers.valid_to IS NULL)
            AND (s_emarketing_vouchers.valid_from <= now() OR s_emarketing_vouchers.valid_from IS NULL)
            AND (
                SELECT s_emarketing_voucher_codes.id
                FROM s_emarketing_voucher_codes
                WHERE s_emarketing_voucher_codes.voucherID = s_emarketing_vouchers.id
                AND s_emarketing_voucher_codes.userID IS NULL
                AND s_emarketing_voucher_codes.cashed = 0
                LIMIT 1
            )";

        $data = $this->get('dbal_connection')->fetchAll($sql);

        $this->View()->assign(
            [
                'success' => true,
                'data' => $data,
                'total' => count($data),
            ]
        );
    }

    /**
     * Create a new newsletter model from passed data
     */
    public function createNewsletterAction()
    {
        $data = $this->Request()->getParams();

        if ($data === null) {
            $this->View()->assign(['success' => false, 'message' => 'no data passed']);

            return;
        }

        $modelManager = $this->get('models');
        $newsletterHelper = $this->get('swag_newsletter.components.newsletter_helper');
        $modelManager->getConnection()->beginTransaction(); // suspend auto-commit

        try {
            $elements = $data['elements'];
            unset($data['elements']);

            $data['groups'] = $this->serializeGroup($data['groups']);
            $data['date'] = new \DateTime();

            $model = new Newsletter();
            $model->fromArray($data);
            $modelManager->persist($model);
            $modelManager->flush();

            $newsletterHelper->saveNewsletterElements($model, $elements);
            $newsletterHelper->saveThirdPartyElements($model, $elements);

            $modelManager->flush();

            $modelManager->getConnection()->commit();
            $modelManager->clear();
        } catch (\Exception $e) {
            $modelManager->getConnection()->rollBack();
            $modelManager->close();
            $this->View()->assign(['success' => false, 'data' => $e->getMessage()]);

            return;
        }

        $data = [
            'id' => $model->getId(),
        ];

        $this->View()->assign(['success' => true, 'data' => $data]);
    }

    /**
     * Lists orders which are related to a newsletter
     */
    public function orderAction()
    {
        $filter = $this->Request()->getParam('filter');
        $sort = $this->Request()->getParam('sort');
        $limit = (int) $this->Request()->getParam('limit', 100);
        $offset = (int) $this->Request()->getParam('start', 0);

        $params = [];

        // Escape and prepare params for the sql query
        if (is_array($filter) && isset($filter[0]['value'])) {
            $params['filter'] = '%' . $filter[0]['value'] . '%';
            $params['value'] = 'sCampaign' . $filter[0]['value'];
            $filter = 'AND (m.subject LIKE :filter OR o.partnerID = :value OR ub.firstname LIKE :filter OR ub.lastname LIKE :filter)';
        } else {
            $filter = '';
        }

        if ($sort !== null && isset($sort[1]['property'])) {
            $direction = 'ASC';
            if (isset($sort['1']['direction']) && $sort['1']['direction'] === 'DESC') {
                $direction = 'DESC';
            }

            switch ($sort[1]['property']) {
                case 'orderTime':
                    $sort = 'o.ordertime';
                    break;
                case 'newsletterDate':
                    $sort = 'm.datum';
                    break;
                case 'subject':
                    $sort = 'm.subject';
                    break;
                case 'customer':
                    $sort = 'customer';
                    break;
                case 'invoiceAmountEuro':
                    $sort = 'invoiceAmount';
                    break;
                default:
                    $sort = 'm.datum';
                    $direction = 'DESC';
                    break;
            }

            $sort = "ORDER BY $sort $direction";
        } else {
            $sort = 'ORDER BY m.datum DESC';
        }

        // Get orders
        $sql = "
        SELECT o.id as id, m.id as partnerId, m.subject, m.id as newsletterId, m.datum as newsletterDate, CONCAT(ub.lastname, ', ', ub.firstname) as customer, ub.userId as customerId, o.id as orderId, o.invoice_amount as invoiceAmount, o.currencyFactor, subshopID as shopId, o.status, o.cleared, o.ordertime as orderTime

        FROM s_campaigns_mailings m

        LEFT JOIN s_order o ON o.partnerID = CONCAT('sCampaign', m.id)
        LEFT JOIN s_user_billingaddress ub ON ub.userID = o.userID
        WHERE o.status > -1 $filter
        $sort
        LIMIT $offset,$limit
        ";

        $results = $this->get('dbal_connection')->fetchAll($sql, $params);

        $this->View()->assign(
            [
                'success' => true,
                'data' => $results,
            ]
        );
    }

    /**
     * Event listener function of the library store.
     */
    public function libraryAction()
    {
        $components = $this->getComponentRepository()->getComponentsQuery()->getArrayResult();

        foreach ($components as $key => &$component) {
            $component['componentFields'] = $component['fields'];
            unset($component['fields']);

            // Ignore disabled and uninstalled plugin
            if ($component['pluginId'] !== null) {
                /** @var $plugin Plugin */
                $plugin = $this->get('models')->find(Plugin::class, $component['pluginId']);
                if (!$plugin || !$plugin->getActive()) {
                    unset($components[$key]);
                }
            }
        }
        unset($component);

        $components = $this->translateComponents($components);

        $this->View()->assign(
            [
                'success' => true,
                'data' => array_values($components),
            ]
        );
    }

    /**
     * Search live shopping product which is active only when live shopping plugin is active
     *
     * @throws Exception
     */
    public function getLiveProductsAction()
    {
        $filter = $this->Request()->getParam('filter');
        if (!empty($filter) && $filter[0]['property'] === 'free') {
            $filter = [
                ['property' => 'product.name', 'value' => $filter[0]['value'], 'operator' => 'LIKE'],
            ];
        }

        $liveShoppingCompatibilityRepository = $this->get('swag_newsletter.components.live_shopping_repository');

        try {
            $result = $liveShoppingCompatibilityRepository->getProducts($filter);
            $this->View()->assign(['success' => true, 'data' => $result]);
        } catch (LiveShoppingCompatibilityException $exception) {
            $this->View()->assign(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * Changes the "publish" and the "status" of the newsletter with the given id to 1|true.
     */
    public function updatePublishAction()
    {
        $newsletterId = $this->Request()->getParam('id');

        if (!$newsletterId) {
            return;
        }

        $modelManager = $this->get('models');

        /** @var Newsletter $model */
        $model = $modelManager->find(Newsletter::class, $newsletterId);

        $model->setPublish(true);
        $model->setStatus(1);

        $modelManager->persist($model);
        $modelManager->flush($model);
    }

    /**
     * Method to define acl dependencies in backend controllers
     * <code>
     * $this->addAclPermission("name_of_action_with_action_prefix",
     * "name_of_assigned_privilege","optionally error message");
     * // $this->addAclPermission("indexAction","read","Ops. You have no permission to view that...");
     * </code>
     */
    protected function initAcl()
    {
        // read
        $this->addAclPermission('getPreviewNewsletters', 'read', 'Insufficient Permissions');
        $this->addAclPermission('listNewsletters', 'read', 'Insufficient Permissions');

        //write
        $this->addAclPermission('createNewsletter', 'write', 'Insufficient Permissions');
    }

    /**
     * @return ComponentRepository
     */
    private function getComponentRepository()
    {
        return $this->get('models')->getRepository(Component::class);
    }

    /**
     * Internal helper function which deletes all data for the passed newsletter.
     *
     * @param int $newsletterId
     */
    private function clearNewsletterData($newsletterId)
    {
        $modelManager = $this->get('models');

        /* @var Query $query */
        $query = $modelManager->createQuery(
            'DELETE SwagNewsletter\Models\Data u WHERE u.newsletterId = ?1'
        );
        $query->setParameter(1, $newsletterId);
        $query->execute();

        $query = $modelManager->createQuery(
            'DELETE SwagNewsletter\Models\Element u WHERE u.newsletterId = ?1'
        );
        $query->setParameter(1, $newsletterId);
        $query->execute();

        $query = $modelManager->createQuery(
            'DELETE Shopware\Models\Newsletter\Container u WHERE u.newsletterId = ?1'
        );
        $query->setParameter(1, $newsletterId);
        $query->execute();

        $modelManager->flush();
    }

    /**
     * Returns a array with translation names
     *
     * @return array
     */
    private function getTranslationKeys()
    {
        return [
            'newsletter/component_article',
            'newsletter/component_article/headline/fieldLabel',
            'newsletter/component_article/headline/supportText',

            'newsletter/component_html',
            'newsletter/component_html/headline/fieldLabel',
            'newsletter/component_html/text/fieldLabel',
            'newsletter/component_html/text/supportText',
            'newsletter/component_html/text/helpTitle',
            'newsletter/component_html/text/helpText',
            'newsletter/component_html/image/fieldLabel',
            'newsletter/component_html/url/fieldLabel',

            'newsletter/component_banner',
            'newsletter/component_banner/description/fieldLabel',
            'newsletter/component_banner/file/fieldLabel',
            'newsletter/component_banner/link/fieldLabel',
            'newsletter/component_banner/target_selection/fieldLabel',
            'newsletter/component_banner/target_selection/supportText',

            'newsletter/component_link',
            'newsletter/component_link/description/fieldLabel',

            'newsletter/component_voucher',
            'newsletter/component_voucher/headline/fieldLabel',
            'newsletter/component_voucher/voucher_selection/fieldLabel',
            'newsletter/component_voucher/text/fieldLabel',
            'newsletter/component_voucher/text/supportText',
            'newsletter/component_voucher/text/defaultValue',
            'newsletter/component_voucher/text/helpTitle',
            'newsletter/component_voucher/text/helpText',
            'newsletter/component_voucher/image/fieldLabel',
            'newsletter/component_voucher/url/fieldLabel',

            'newsletter/component_suggest',
            'newsletter/component_suggest/headline/fieldLabel',
            'newsletter/component_suggest/number/fieldLabel',
        ];
    }

    /**
     * Collects all translation names
     *
     * @return array
     */
    private function getTranslationNames()
    {
        /** @var Enlight_Event_EventManager $eventManager */
        $eventManager = $this->get('events');
        $collection = new ArrayCollection([]);
        $eventManager->collect('swag_newsletter_collect_translation_keys', $collection);

        $translationKeys = $this->getTranslationKeys();

        return array_merge($collection->toArray(), $translationKeys);
    }

    /**
     * Collects all array keys which could contain translatable snippets
     *
     * @return array
     */
    private function getTranslatableKeys()
    {
        /** @var Enlight_Event_EventManager $eventManager */
        $eventManager = $this->get('events');
        $collection = new ArrayCollection([]);
        $eventManager->collect('swag_newsletter_collect_translatable_keys', $collection);

        $translatableKeys = [
            'fieldLabel',
            'supportText',
            'defaultValue',
            'helpTitle',
            'helpText',
        ];

        return array_merge($collection->toArray(), $translatableKeys);
    }

    /**
     * translate components
     *
     * @param array $components
     *
     * @return array
     */
    private function translateComponents(array $components)
    {
        $locale = $this->getBackendLocale();

        if ($locale->getLocale() === 'de_DE') {
            return $components;
        }

        $localeId = $locale->getId();
        $names = $this->getTranslationNames();
        $prefix = 'newsletter/';

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->get('dbal_connection')->createQueryBuilder();
        $translations = $queryBuilder->select('snippets.name, snippets.value')
            ->from('s_core_snippets', 'snippets')
            ->where('snippets.namespace = "backend/swag_newsletter/main"')
            ->andWhere('snippets.localeID = :localeId')
            ->andWhere('snippets.name IN (:names)')
            ->setParameter('localeId', $localeId)
            ->setParameter(':names', $names, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_KEY_PAIR);

        foreach ($components as &$component) {
            if (!empty($translations[$prefix . $component['template']])) {
                $component['name'] = $translations[$prefix . $component['template']];
            }

            foreach ($component['componentFields'] as &$componentField) {
                foreach ($this->getTranslatableKeys() as $translatableKey) {
                    if (!empty($componentField[$translatableKey])) {
                        $key = implode('/', [
                            $prefix . $component['template'],
                            $componentField['name'],
                            $translatableKey,
                        ]);
                        if (!empty($translations[$key])) {
                            $componentField[$translatableKey] = $translations[$key];
                        }
                    }
                }
            }
        }

        return $components;
    }

    /**
     * @return Locale
     */
    private function getBackendLocale()
    {
        return $this->get('auth')->getIdentity()->locale;
    }

    /**
     * Little helper function, that puts the array in the form found in the database originally and serializes it
     *
     * @param array $groups
     *
     * @return string
     */
    private function serializeGroup(array $groups)
    {
        $newGroup = [[], []];

        foreach ($groups as $key => $values) {
            if ($values['isCustomerGroup'] === true) {
                $newGroup[0][$values['groupkey']][] = $values['number'];
            } elseif ($values['streamId'] !== null) {
                $newGroup[2][$values['streamId']][] = $values['number'];
            } else {
                $newGroup[1][$values['internalId']][] = $values['number'];
            }
        }

        return serialize($newGroup);
    }

    /**
     * Helper function which takes a serializes group string from the database and puts it in a flattened form
     *
     * @param string $group
     *
     * @return array
     */
    private function unserializeGroup($group)
    {
        $groups = unserialize($group);

        $flattenedGroup = [];
        foreach ($groups as $groupKey => $item) {
            foreach ($item as $id => $number) {
                switch ($groupKey) {
                    case 0:
                        $flattenedGroup[] = [
                            'internalId' => null,
                            'number' => $number,
                            'name' => '',
                            'streamId' => null,
                            'groupkey' => $id,
                            'isCustomerGroup' => true,
                        ];
                        break;
                    case 1:
                        $flattenedGroup[] = [
                            'internalId' => $id,
                            'number' => $number,
                            'name' => '',
                            'streamId' => null,
                            'groupkey' => false,
                            'isCustomerGroup' => false,
                        ];
                        break;
                    case 2:
                        $flattenedGroup[] = [
                            'internalId' => null,
                            'number' => $number,
                            'name' => '',
                            'streamId' => $id,
                            'groupkey' => false,
                            'isCustomerGroup' => false,
                        ];
                        break;
                }
            }
        }

        return $flattenedGroup;
    }

    /**
     * Find a name for a duplicated newsletter - will check for a free name like "oldName (Copy #n)"
     *
     * @param string $oldName
     *
     * @return string
     */
    private function getNameForDuplicatedNewsletter($oldName)
    {
        $copySnippet = $this->container->get('snippets')->getNamespace('backend/swag_newsletter/main')->get(
            'newsletter/duplicated'
        );
        $copyTemplate = '(%s #%s)';
        $copyTemplateRegEx = '/ \(.*? #(?P<number>\d)*\)/';

        // Name without any copy counter
        $pureOldName = preg_replace($copyTemplateRegEx, '', $oldName);
        // Existing mails with the "original" name
        $existingNames = $this->get('dbal_connection')->fetchColumn(
            'SELECT subject FROM s_campaigns_mailings WHERE subject LIKE ?',
            [$pureOldName . '%']
        );

        // Iterate the existing mails with the original name in it and find a free slot
        $copyNumber = 1;
        do {
            $newName = sprintf('%s %s', $pureOldName, sprintf($copyTemplate, $copySnippet, $copyNumber));
            ++$copyNumber;
        } while (in_array($newName, $existingNames));

        return $newName;
    }
}
