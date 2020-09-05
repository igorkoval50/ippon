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

use Doctrine\DBAL\Query\QueryBuilder;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\Sorting\PriceSorting;
use Shopware\Bundle\StoreFrontBundle\Service;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Shopware\Components\ProductStream\RepositoryInterface;
use Shopware\Models\Shop\Shop;
use SwagProductAdvisor\Bundle\AdvisorBundle\Struct\Advisor;
use SwagProductAdvisor\Bundle\AdvisorBundle\Struct\AdvisorAttribute;
use SwagProductAdvisor\Bundle\SearchBundle\AdvisorSorting;
use SwagProductAdvisor\Components\Helper\BackendLocale;
use SwagProductAdvisor\Components\Helper\BackendStreamHelperInterface;
use SwagProductAdvisor\Components\Helper\RewriteUrlGeneratorInterface;
use SwagProductAdvisor\Components\Helper\TranslationServiceInterface;
use SwagProductAdvisor\Components\Helper\UrlGeneratorInterface;

/**
 * Class Shopware_Controllers_Backend_Advisor
 */
class Shopware_Controllers_Backend_Advisor extends Shopware_Controllers_Backend_Application
{
    protected $model = \SwagProductAdvisor\Models\Advisor::class;
    protected $alias = 'advisor';

    /**
     * Overrides the original getDetail to generate a link to the advisor.
     *
     * @param int $id
     *
     * @return array
     */
    public function getDetail($id)
    {
        $detailArray = parent::getDetail($id);

        $detailArray['data'] = $this->prepareAdvisorData($detailArray['data']);

        return $detailArray;
    }

    /**
     * This method is to delete all the sessions that are created by this advisor.
     *
     * {@inheritdoc}
     */
    public function delete($id)
    {
        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this->get('models');
        /** @var \SwagProductAdvisor\Models\Session[] $sessionArray */
        $sessionArray = $entityManager->getRepository(
            \SwagProductAdvisor\Models\Session::class
        )->findBy(['advisor' => $id]);

        foreach ($sessionArray as $session) {
            $entityManager->remove($session);
        }

        $entityManager->flush();

        return parent::delete($id);
    }

    /**
     * This method save the data from inline editing.
     * Currently only the name- and the active-flag are saved.
     */
    public function saveDataInlineAction()
    {
        $id = $this->request->get('id');
        $name = $this->request->get('name');
        $active = ($this->request->get('active') === 'true');

        if (!$id) {
            $this->view->assign([
                'success' => false,
                'message' => 'No ProductAdvisor ID found.',
            ]);

            return;
        }

        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this->get('models');
        /** @var \SwagProductAdvisor\Models\Advisor $advisor */
        $advisor = $entityManager->getRepository(\SwagProductAdvisor\Models\Advisor::class)->find($id);

        if (!$advisor) {
            $this->view->assign([
                'success' => false,
                'message' => 'No ProductAdvisor found.',
            ]);

            return;
        }

        try {
            $advisor->setName($name);
            $advisor->setActive($active);

            $entityManager->persist($advisor);
            $entityManager->flush();

            $this->view->assign(['success' => true]);
        } catch (\Exception $ex) {
            $this->view->assign([
                'success' => false,
                'message' => $ex->getMessage(),
            ]);
        }
    }

    /**
     * Extend the SaveMethod
     *
     * {@inheritdoc}
     */
    public function save(array $data)
    {
        // at first check for a the Banner to prevent association errors.
        if (!$data['teaserBannerId']) {
            $data['teaserBanner'] = null;
        }

        $parentData = parent::save($data);

        if (!$parentData['success']) {
            return $parentData;
        }

        /** @var TranslationServiceInterface $translationService */
        $translationService = $this->container->get('swag_product_advisor.translation_service');
        $translationService->checkForTranslationClone($data['questions'], $parentData['data']['questions']);

        $data = $parentData['data'];

        return parent::save($data);
    }

    /**
     * create a copy of a Advisor by id
     * id come from request
     */
    public function cloneAdvisorAjaxAction()
    {
        $id = $this->request->get('id');

        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this->get('models');
        $advisor = $entityManager->getRepository(\SwagProductAdvisor\Models\Advisor::class)->find($id);

        if (empty($advisor)) {
            return;
        }

        $newAdvisor = clone $advisor;

        $prefix = $this->getCopyPrefix();

        $newAdvisor->setName($prefix . $advisor->getName());

        $entityManager->persist($newAdvisor);
        $entityManager->flush();

        /** @var TranslationServiceInterface $translationService */
        $translationService = $this->get('swag_product_advisor.translation_service');
        $translationService->cloneTranslations($newAdvisor, $advisor);
    }

    /**
     * get all ProductStreams
     */
    public function getProductStreamsAjaxAction()
    {
        /** @var QueryBuilder $builder */
        $builder = $this->get('dbal_connection')->createQueryBuilder();

        $result = $builder->select(['id', 'name'])
            ->from('s_product_streams', 'stream')
            ->execute()
            ->fetchAll(PDO::FETCH_ASSOC);

        $this->view->assign([
            'data' => $result,
            'total' => count($result),
        ]);
    }

    /**
     * needed parameter in request
     *
     *  streamId,
     *  limit,
     *  start,
     *  shopId,
     *  currencyId,
     *  customerGroupKey,
     */
    public function getProductsByStreamIdAjaxAction()
    {
        $streamId = (int) $this->request->getParam('streamId');

        /** @var ContextServiceInterface $contextService */
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');
        $context = $contextService->createProductContext(
            (int) $this->request->getParam('shopId'),
            (int) $this->request->getParam('currencyId'),
            $this->request->getParam('customerGroupKey')
        );

        $limit = (int) $this->request->getParam('limit');
        $offset = (int) $this->request->getParam('start');

        $criteria = new Criteria();
        $criteria->offset($offset);
        $criteria->limit($limit);

        /** @var RepositoryInterface $streamRepository */
        $streamRepository = Shopware()->Container()->get('shopware_product_stream.repository');
        $streamRepository->prepareCriteria($criteria, $streamId);

        $productSearch = Shopware()->Container()->get('shopware_search.product_search');
        $result = $productSearch->search($criteria, $context);

        $this->View()->assign([
            'success' => true,
            'data' => array_values($result->getProducts()),
            'total' => $result->getTotalCount(),
        ]);
    }

    /**
     * read all AttributeColumns
     *
     * @throws Exception
     */
    public function getAttributesAjaxAction()
    {
        $table = 's_articles_attributes';

        $connection = $this->container->get('dbal_connection');

        $schemaManager = $connection->getSchemaManager();
        $tableColumns = $schemaManager->listTableColumns($table);

        $blackList = [
            'id',
            'articleid',
            'articledetailsid',
        ];

        $columns = [];
        foreach ($tableColumns as $key => $value) {
            if (!in_array($key, $blackList)) {
                $columns[] = ['id' => $key, 'name' => $key];
            }
        }

        $this->view->assign([
            'data' => $columns,
            'total' => count($columns),
        ]);
    }

    /**
     * real all attributeValues from stream
     *
     * need the streamId and attributeColumnName in request
     *
     * @throws Exception
     */
    public function getAttributeValuesAjaxAction()
    {
        $streamId = (int) $this->request->get('streamId');
        $attributeColumn = $this->request->get('attributeColumn');

        /** @var BackendStreamHelperInterface $backendPreview */
        $backendPreview = $this->get('swag_product_advisor.backend_stream_helper');
        $attributes = $backendPreview->getAttributeValuesByStreamIdAndAttributeColumnName($streamId, $attributeColumn);

        $searchValue = $this->request->get('query');
        if ($searchValue && strlen($searchValue) > 0) {
            $attributes = $this->searchForValue($attributes, strtolower($searchValue));
        }

        $total = count($attributes);
        $result = $this->prepareValues($attributes, $total);

        $this->view->assign([
            'data' => $result,
            'total' => $total,
        ]);
    }

    /**
     * read all manufacturer from stream
     *
     * need the streamId in request
     *
     * @throws Exception
     */
    public function getManufacturerAjaxAction()
    {
        $streamId = $this->request->get('streamId');

        /** @var BackendStreamHelperInterface $backendPreview */
        $backendPreview = $this->get('swag_product_advisor.backend_stream_helper');
        $manufacturers = $backendPreview->getManufacturerByStreamIds($streamId);

        $searchValue = $this->request->get('query');
        if ($searchValue && strlen($searchValue) > 0) {
            $manufacturers = $this->searchForValue($manufacturers, $searchValue);
        }

        $total = count($manufacturers);
        $result = $this->prepareValues($manufacturers, $total);

        $this->view->assign([
            'data' => $result,
            'total' => $total,
        ]);
    }

    /**
     * call all properties from stream
     *
     * need the streamId in request
     */
    public function getPropertiesAjaxAction()
    {
        $streamId = $this->request->get('streamId');
        $showAllProperties = $this->request->get('showAllProperties');

        if ($showAllProperties === 'true') {
            $queryBuilder = $this->container->get('dbal_connection')->createQueryBuilder();
            $properties = $queryBuilder->select(['id', 'name'])
                ->from('s_filter_options')
                ->execute()
                ->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            /** @var BackendStreamHelperInterface $backendPreview */
            $backendPreview = $this->get('swag_product_advisor.backend_stream_helper');
            $properties = $backendPreview->getPropertiesByStreamId($streamId);
        }

        $this->view->assign([
            'data' => $properties,
            'total' => count($properties),
        ]);
    }

    /**
     * read all possible propertyValues from stream
     *
     * need the streamId in request
     * need propertyId in request
     *
     * @throws Exception
     */
    public function getPropertyValuesAjaxAction()
    {
        $streamId = $this->request->get('streamId');
        $propertyId = $this->request->get('propertyId');
        $showAllProperties = $this->request->get('showAllProperties');

        if ($showAllProperties === 'true') {
            $propertyValues = $this->getAllPropertyValues($propertyId);
        } else {
            $propertyValues = $this->getStreamPropertyValues($streamId, $propertyId);
        }

        $this->view->assign([
            'data' => $propertyValues,
            'total' => count($propertyValues),
        ]);
    }

    /**
     * This helper is for the PriceFilter
     * to display the MaxArticlePrice in stream to the Shop owner.
     *
     * need the streamId in request
     */
    public function getMaxPriceAjaxAction()
    {
        $streamId = $this->request->get('streamId');

        /** @var BackendStreamHelperInterface $backendPreview */
        $backendPreview = $this->get('swag_product_advisor.backend_stream_helper');
        $maxPrice = $backendPreview->getMaxPriceByStreamIds($streamId);

        $this->view->assign([
            'data' => (float) $maxPrice,
        ]);
    }

    /**
     * This action finds all products by the advisorId and answers
     * and assigns the result to the View
     */
    public function findProductAction()
    {
        $advisorId = $this->request->get('advisorId');

        /** @var SwagProductAdvisor\Components\Helper\AnswerBuilder $answerBuilder */
        $answerBuilder = $this->get('swag_product_advisor.answer_builder');

        $answers = $answerBuilder->buildAnswers(json_decode($this->request->getParam('answers'), true));

        /** @var ShopContextInterface $contextService */
        $contextService = $this->get('shopware_storefront.context_service');

        /** @var ShopContextInterface $context */
        $context = $contextService->createProductContext(
            $this->request->getParam('shop'),
            $this->request->getParam('currency'),
            $this->request->getParam('customer')
        );

        /** @var Shop $shop */
        $shop = $this->get('models')
            ->getRepository(Shop::class)
            ->find($this->request->getParam('shop'));

        $this->get('shopware.components.shop_registration_service')->registerResources($shop);

        /** @var Advisor $advisor */
        $advisor = $this->get('swag_product_advisor.advisor_service')->get($advisorId, $context, $answers);

        if (!empty($answers)) {
            $result = $this->getAdvisorResult($advisor, $context);
            $this->View()->assign('result', $this->prepareSearchResultForBackendPreview($result));
        }
    }

    /**
     * get the answers who was in the Question.
     * this Action was only called by the PriceFilter
     */
    public function getSavedPricesAction()
    {
        $questionId = $this->request->get('questionId');
        $priceAnswers = [];

        if ($questionId) {
            $priceAnswers = $this->container->get('dbal_connection')->createQueryBuilder()
                ->select('*')
                ->from('s_plugin_product_advisor_answer', 'answer')
                ->where('question_id = :questionId')
                ->setParameter(':questionId', $questionId)
                ->execute()
                ->fetchAll(\PDO::FETCH_ASSOC);
        }

        $this->view->assign([
            'data' => $priceAnswers,
        ]);
    }

    /**
     * Creates the advisor seo-URLs.
     * It also supports batch-processing.
     */
    public function seoAdvisorAction()
    {
        @set_time_limit(0);
        $offset = $this->Request()->getParam('offset');
        $limit = $this->Request()->getParam('limit', 50);
        $shopId = (int) $this->Request()->getParam('shopId', 1);
        /** @var Shopware_Components_SeoIndex $seoIndex */
        $seoIndex = $this->container->get('SeoIndex');
        /** @var sRewriteTable $rewriteTable */
        $rewriteTable = $this->container->get('modules')->RewriteTable();
        /** @var RewriteUrlGeneratorInterface $rewriteUrlGenerator */
        $rewriteUrlGenerator = $this->container->get('swag_product_advisor.rewrite_url_generator');

        // Create shop
        $seoIndex->registerShop($shopId);

        $rewriteTable->baseSetup();
        $rewriteUrlGenerator->createRewriteTableAdvisor($offset, $limit, $shopId);

        $this->View()->assign([
            'success' => true,
        ]);
    }

    /**
     * We need to override this to add all the important additional tables by joining them.
     * Additionally we need the order-by.
     *
     * {@inheritdoc}
     */
    protected function getDetailQuery($id)
    {
        $builder = parent::getDetailQuery($id);

        $builder
            ->leftJoin('advisor.teaserBanner', 'teaserBanner')
            ->leftJoin('advisor.stream', 'stream')
            ->leftJoin('advisor.questions', 'questions')
            ->leftJoin('questions.answers', 'answers')
            ->addOrderBy('questions.order', 'ASC')
            ->addOrderBy('answers.order', 'ASC')
            ->addSelect(['teaserBanner', 'stream', 'questions', 'answers']);

        return $builder;
    }

    /**
     * @param string $searchValue
     *
     * @return array
     */
    protected function searchForValue(array $data, $searchValue)
    {
        $returnArray = [];

        foreach ($data as $row) {
            $key = strtolower($row['key']);
            $value = strtolower($row['value']);

            if (stripos($key, $searchValue) !== false || strpos($value, $searchValue) !== false) {
                $returnArray[] = $row;
            }
        }

        return $returnArray;
    }

    /**
     * @param int $total
     *
     * @return array
     */
    private function prepareValues(array $dataArray, $total)
    {
        $result = [];

        $limit = (int) $this->request->get('limit');
        $offset = (int) $this->request->get('start');

        if (!empty($offset)) {
            $limit = $offset + $limit;
        }

        if ($total < $limit) {
            $limit = $total;
        }

        for ($i = $offset; $i < $limit; ++$i) {
            $result[] = $dataArray[$i];
        }

        return $result;
    }

    /**
     * @return array
     */
    private function prepareSearchResultForBackendPreview(array $result)
    {
        $productArray = [];

        foreach ($result as $product) {
            /** @var AdvisorAttribute $advisorAdds */
            $advisorAdds = $product['attributes']['advisor'];

            $search = $product['attributes']['search'];

            $productArray[] = [
                'id' => $product['id'],
                'name' => $product['articleName'],
                'matches' => count($advisorAdds->getMatches()),
                'boost' => $search->get('advisorRanking'),
            ];
        }

        return $productArray;
    }

    /**
     * @return Shopware\Bundle\StoreFrontBundle\Struct\ListProduct[] | []
     */
    private function getAdvisorResult(Advisor $advisor, ShopContextInterface $context)
    {
        /** @var Criteria $criteria */
        $criteria = $this->container->get('shopware_product_stream.criteria_factory')
            ->createCriteria($this->Request(), $context);

        $stream = $this->container->get('shopware_product_stream.repository');
        $stream->prepareCriteria($criteria, $advisor->getStream());

        $criteria->resetSorting();
        $criteria->resetFacets();

        $sorting = new AdvisorSorting($advisor);
        $criteria->addSorting($sorting);
        $criteria->addSorting(new PriceSorting($advisor->getLastListingSort()));
        $criteria->limit(50);

        /** @var \SwagProductAdvisor\Bundle\SearchBundle\AdvisorSearch $search */
        $search = $this->container->get('swag_product_advisor.search');

        $result = $search->search($criteria, $context);

        return array_map(function ($item) {
            return $this->get('legacy_struct_converter')->convertListProductStruct($item);
        }, $result->getProducts());
    }

    /**
     * Create the prefix for a copy of a advisor
     *
     * @return string
     */
    private function getCopyPrefix()
    {
        /** @var BackendLocale $backendLocale */
        $backendLocale = $this->container->get('swag_product_advisor.backend_locale');
        $language = $backendLocale->getBackendLanguage();

        switch ($language) {
            case 'de_DE':
                $prefix = 'Kopie von ';
                break;
            default:
                $prefix = 'Copy of ';
                break;
        }

        return $prefix;
    }

    /**
     * Prepares the advisor-data for the backend.
     * Generates the advisor-url to open the advisor and triggers the thumbnail-url generator for image-questions.
     *
     * @return array
     */
    private function prepareAdvisorData(array $advisorData)
    {
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->get('swag_product_advisor.url_generator');
        $advisorData['links'] = $urlGenerator->generateStartUrl($advisorData['id'], $advisorData['name']);
        $advisorData['questions'] = $this->prepareImageThumbnails($advisorData['questions']);

        return $advisorData;
    }

    /**
     * Generates the thumbnail-URLs for the image-question answers.
     *
     * @return array
     */
    private function prepareImageThumbnails(array $questions)
    {
        /** @var Service\MediaServiceInterface $mediaService */
        $mediaService = $this->get('shopware_storefront.media_service');
        /** @var Service\ContextServiceInterface $contextService */
        $contextService = $this->get('shopware_storefront.context_service');

        foreach ($questions as &$question) {
            if (!in_array($question['template'], ['checkbox_image', 'radio_image'])) {
                continue;
            }

            foreach ($question['answers'] as &$answer) {
                if (!$answer['mediaId']) {
                    continue;
                }

                $media = $mediaService->get($answer['mediaId'], $contextService->getShopContext());
                if (!$media) {
                    continue;
                }

                $answer['thumbnail'] = $media->getFile();
            }
        }

        return $questions;
    }

    private function getAllPropertyValues(string $propertyId): array
    {
        $result = $this->container->get('dbal_connection')->createQueryBuilder()
            ->select(['id', 'value'])
            ->from('s_filter_values')
            ->where('optionID = :optionId')
            ->setParameter('optionId', $propertyId)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        return $result ?: [];
    }

    private function getStreamPropertyValues(string $streamId, string $propertyId): array
    {
        /** @var BackendStreamHelperInterface $backendPreview */
        $backendPreview = $this->get('swag_product_advisor.backend_stream_helper');
        $propertyValues = $backendPreview->getPropertyValuesByStreamAndPropertyId($streamId, $propertyId);

        $searchValue = trim($this->request->get('query'));
        if ($searchValue && strlen($searchValue) > 0) {
            $propertyValues = $this->searchForValue($propertyValues, $searchValue);
        }

        return $this->prepareValues($propertyValues, count($propertyValues));
    }
}
