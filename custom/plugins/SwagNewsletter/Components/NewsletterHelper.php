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

namespace SwagNewsletter\Components;

use Doctrine\ORM\AbstractQuery;
use Shopware\Bundle\MediaBundle\MediaServiceInterface;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Article\Detail;
use Shopware\Models\Newsletter\Container;
use Shopware\Models\Newsletter\ContainerType\Article;
use Shopware\Models\Newsletter\ContainerType\Banner;
use Shopware\Models\Newsletter\ContainerType\Link;
use Shopware\Models\Newsletter\ContainerType\Text;
use Shopware\Models\Newsletter\Newsletter;
use SwagNewsletter\Components\ContainerConverter\ContainerConverterFactory;
use SwagNewsletter\Models\Component;
use SwagNewsletter\Models\Data;
use SwagNewsletter\Models\Element;
use SwagNewsletter\Models\Field;

class NewsletterHelper implements NewsletterHelperInterface
{
    /**
     * @var \SwagNewsletter\Models\Repository
     */
    protected $componentRepository;

    /**
     * @var array
     */
    protected $componentMapping = [];

    /**
     * @var array
     */
    protected $defaultComponents = [
        'newsletter-html-text-element',
        'newsletter-banner-element',
        'newsletter-article-element',
        'newsletter-link-element',
        'newsletter-voucher-element',
        'newsletter-suggest-element',
    ];

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var MediaServiceInterface
     */
    private $mediaService;

    /**
     * @param ModelManager          $modelManager
     * @param MediaServiceInterface $mediaService
     */
    public function __construct(
        ModelManager $modelManager,
        MediaServiceInterface $mediaService
    ) {
        $this->modelManager = $modelManager;
        $this->mediaService = $mediaService;
        $this->componentRepository = $this->modelManager->getRepository(Component::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewsletterElements(array $newsletter)
    {
        $containerConverterFactory = new ContainerConverterFactory();
        $newsletterRegistry = $containerConverterFactory->getRegistry();

        $elements = [];
        foreach ($newsletter['containers'] as $container) {
            $element = [
                'id' => $container['id'],
                'startRow' => $container['position'],
                'endRow' => $container['position'],
                'startCol' => 1,
                'endCol' => 1,
                'newsletterId' => $newsletter['id'],
                'data' => [],
            ];

            $component = $this->getComponentByContainerType($container['type']);

            $ctConverter = $newsletterRegistry->getConverter($container['type']);
            $element['data'] = $ctConverter->convert(['container' => $container, 'data' => $element['data']]);

            $element['component'] = [$component];
            $element['componentId'] = $component['id'];

            $elements[] = $element;
        }

        $query = $this->componentRepository->getElementsByNewsletterIdQuery($newsletter['id']);
        $additionalElements = $query->getArrayResult();

        foreach ($additionalElements as &$additionalElement) {
            $additionalElement['component'] = [$additionalElement['component']];
            $additionalElement['component'][0]['componentFields'] = $additionalElement['component'][0]['fields'];
            $additionalElement['componentId'] = $additionalElement['component'][0]['id'];

            foreach ($additionalElement['data'] as &$datum) {
                $datum['key'] = $datum['field']['name'];
                if ($datum['field']['valueType'] === 'json') {
                    $datum['value'] = \Zend_Json::decode($datum['value']);
                }
            }
            unset($datum);
        }
        unset($additionalElement);

        if ($additionalElements) {
            // At this point no sorting is required
            $elements = array_merge($additionalElements, $elements);
        }

        unset($newsletter['containers']);
        $newsletter['elements'] = $elements;

        return $newsletter;
    }

    /**
     * {@inheritdoc}
     */
    public function saveThirdPartyElements(Newsletter $newsletter, array $elements)
    {
        // Iterate all elements
        foreach ($elements as $elementData) {
            $component = $elementData['component'][0];

            // Skip default components - they are stored as containers for compatibility reasons
            if (in_array($component['cls'], $this->defaultComponents)) {
                continue;
            }

            $element = new Element();
            /** @var \SwagNewsletter\Models\Component $component */
            $component = $this->modelManager->find(
                Component::class,
                $elementData['componentId']
            );

            // Iterate all element fields
            foreach ($elementData['data'] as $item) {
                $model = new Data();

                $fieldId = isset($item['field']) ? $item['field']['id'] : $item['id'];
                /** @var $field Field */
                $field = $this->modelManager->find(Field::class, $fieldId);

                $model->setComponent($component);
                $model->setComponentId($component->getId());
                $model->setElement($element);
                $model->setFieldId($item['id']);

                $model->setField($field);
                switch (strtolower($field->getValueType())) {
                    case 'json':
                        $value = \Zend_Json::encode($item['value']);
                        break;
                    case 'string':
                    default:
                        $value = $item['value'];
                        break;
                }
                $model->setValue($value);
                $model->setNewsletterId($newsletter->getId());
                $this->modelManager->persist($model);
            }

            $elementData['newsletter'] = $newsletter;
            $elementData['component'] = $component;
            unset($elementData['data']);
            $element->fromArray($elementData);
            $this->modelManager->persist($element);
            $elements[] = $element;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function saveNewsletterElements(Newsletter $model, array $elements)
    {
        /** @var \Shopware\Models\Article\Repository $productDetailRepository */
        $productDetailRepository = $this->modelManager->getRepository(Detail::class);

        foreach ($elements as $elementKey => $element) {
            $component = $element['component'][0];
            $position = $element['startRow'];
            $data = $element['data'];

            // Only create containers for default components
            if (in_array($component['cls'], $this->defaultComponents)) {
                $container = new Container();
                $container->setNewsletter($model);
                $container->setPosition($position);
                $container->setValue('');
                $this->modelManager->persist($container);
            }

            switch ($component['cls']) {
                // voucher and text elements are basically the same. The voucher-code for voucher-elements
                // is stored in the value property of the parent container
                case 'newsletter-voucher-element':
                case 'newsletter-html-text-element':
                    $text = new Text();
                    $text->setAlignment('left');
                    $text->setContainer($container);
                    foreach ($data as $datum) {
                        switch ($datum['key']) {
                            case 'headline':
                                $container->setDescription($datum['value']);
                                $text->setHeadline($datum['value']);
                                break;
                            case 'text':
                                $text->setContent($datum['value']);
                                break;
                            case 'image':
                                $datum['value'] = $this->fixImagePath($datum['value']);
                                $text->setImage($datum['value']);
                                break;
                            case 'url':
                                $text->setLink($datum['value']);
                                break;
                            case 'voucher_selection':
                                $container->setValue($datum['value']);
                                $container->setType('ctVoucher');
                                break;
                        }
                    }
                    $this->modelManager->persist($text);
                    break;
                case 'newsletter-banner-element':
                    $banner = new Banner();
                    $banner->setContainer($container);
                    foreach ($data as $datum) {
                        switch ($datum['key']) {
                            case 'description':
                                $container->setDescription($datum['value']);
                                $banner->setDescription($datum['value']);
                                break;
                            case 'file':
                                $datum['value'] = $this->fixImagePath($datum['value']);
                                $banner->setImage($datum['value']);
                                break;
                            case 'link':
                                $banner->setLink($datum['value']);
                                break;
                            case 'target_selection':
                                $banner->setTarget($datum['value']);
                                break;
                        }
                    }
                    $this->modelManager->persist($banner);
                    break;
                // Products and links differ from other containers: Each product/link container can have
                // multiple children
                case 'newsletter-article-element':
                    if (count($data) === 0) {
                        throw new \Exception('No products set for the product element');
                    }
                    foreach ($data as $dateKey => $datum) {
                        switch ($datum['key']) {
                            case 'article_data':
                                foreach ($datum['value'] as $productKey => $product) {
                                    switch ($product['type']) {
                                        case 'fix':
                                            $productDetail = $productDetailRepository->findOneBy(
                                                ['number' => $product['ordernumber']]
                                            );
                                            if ($productDetail === null) {
                                                throw new \Exception(
                                                    "Product by ordernumber '{$product['ordernumber']}' not found"
                                                );
                                            }
                                            $product['articleDetail'] = $productDetail;
                                            break;
                                        case 'random':
                                            $product['name'] = 'Zufall';
                                            break;
                                        case 'top':
                                            $product['name'] = 'Topseller';
                                            break;
                                        case 'new':
                                            $product['name'] = 'Neuheit';
                                            break;
                                    }
                                    $productModel = new Article();
                                    $productModel->fromArray($product);
                                    $productModel->setContainer($container);
                                    $this->modelManager->persist($productModel);
                                }
                                break;
                            case 'headline':
                                $container->setDescription($datum['value']);
                                break;
                        }
                    }
                    break;
                case 'newsletter-link-element':
                    foreach ($data as $datum) {
                        switch ($datum['key']) {
                            case 'link_data':
                                foreach ($datum['value'] as $link) {
                                    $linkModel = new Link();
                                    $linkModel->fromArray($link);
                                    $linkModel->setContainer($container);
                                    $this->modelManager->persist($linkModel);
                                }
                                break;
                            case 'description':
                                $container->setDescription($datum['value']);
                                break;
                        }
                    }
                    break;
                case 'newsletter-suggest-element':
                    foreach ($data as $dateKey => $datum) {
                        switch ($datum['key']) {
                            case 'number':
                                $container->setValue($datum['value']);
                                break;
                            case 'headline':
                                $container->setDescription($datum['value']);
                                break;
                        }
                    }
                    $container->setType('ctSuggest');
                    break;
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    private function getComponentByContainerType($containerType)
    {
        $containerTypeMapping = [
            'ctText' => 'newsletter-html-text-element',
            'ctBanner' => 'newsletter-banner-element',
            'ctArticles' => 'newsletter-article-element',
            'ctLinks' => 'newsletter-link-element',
            'ctVoucher' => 'newsletter-voucher-element',
            'ctSuggest' => 'newsletter-suggest-element',
        ];

        if (!array_key_exists($containerType, $containerTypeMapping)) {
            throw new \Exception("Container type {$containerType} is not valid");
        }

        $cls = $containerTypeMapping[$containerType];

        if (!array_key_exists($containerType, $this->componentMapping)) {
            $component = $this->componentRepository->getComponentsByClassQuery($cls)->getOneOrNullResult(
                AbstractQuery::HYDRATE_ARRAY
            );
            $component['componentFields'] = $component['fields'];
            unset($component['fields']);
            $this->componentMapping[$containerType] = $component;

            return $component;
        }

        return $this->componentMapping[$containerType];
    }

    /**
     * Helper method to convert the given image to the new media-path
     *
     * @param string $image
     *
     * @return string
     */
    private function fixImagePath($image)
    {
        return $this->mediaService->normalize($image);
    }
}
