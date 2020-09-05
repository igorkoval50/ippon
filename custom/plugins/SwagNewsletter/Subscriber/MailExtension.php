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

namespace SwagNewsletter\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\Model\ModelManager;
use SwagNewsletter\Components\LiveShopping\LiveShoppingRepositoryInterface;
use SwagNewsletter\Components\SuggestServiceInterface;
use SwagNewsletter\Models\Element;

class MailExtension implements SubscriberInterface
{
    /**
     * @var LiveShoppingRepositoryInterface
     */
    private $liveShoppingRepository;

    /**
     * @var SuggestServiceInterface
     */
    private $suggestService;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var \Enlight_Controller_Front
     */
    private $front;

    /**
     * @param LiveShoppingRepositoryInterface $liveShoppingRepository
     * @param SuggestServiceInterface         $suggestService
     * @param ModelManager                    $modelManager
     * @param \Enlight_Controller_Front       $frontendController
     */
    public function __construct(
        LiveShoppingRepositoryInterface $liveShoppingRepository,
        SuggestServiceInterface $suggestService,
        ModelManager $modelManager,
        \Enlight_Controller_Front $frontendController
    ) {
        $this->liveShoppingRepository = $liveShoppingRepository;
        $this->suggestService = $suggestService;
        $this->modelManager = $modelManager;
        $this->front = $frontendController;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Controllers_Backend_Newsletter::getMailingSuggest::after' => 'getMailingSuggest',
            'Shopware_Controllers_Backend_Newsletter::getMailingDetails::after' => 'getMailingDetails',
            'Shopware_Modules_Marketing_MailCampaignsGetDetail_FilterSQL' => 'onFilterMailCampaignsDetailSQL',
        ];
    }

    /**
     * Additionally select position in sMarketing::sMailCampaignsDetail
     *
     * @param \Enlight_Event_EventArgs $arguments
     *
     * @return string
     */
    public function onFilterMailCampaignsDetailSQL(\Enlight_Event_EventArgs $arguments)
    {
        $sql = $arguments->getReturn();

        $sql = str_replace(' FROM', ', position FROM', $sql);

        return $sql;
    }

    /**
     * Called after the getMailingDetails method of the Newsletter Controller
     * Used to insert third party components
     *
     * @param $args \Enlight_Hook_HookArgs
     */
    public function getMailingDetails(\Enlight_Hook_HookArgs $args)
    {
        $params = $args->getArgs();
        $newsletterId = $params[0];

        /** @var $repository \SwagNewsletter\Models\Repository */
        $repository = $this->modelManager->getRepository(Element::class);
        $query = $repository->getElementsByNewsletterIdQuery($newsletterId);
        $additionalElements = $query->getArrayResult();

        // if not third party components were found, we don't need to touch the original data
        if (!empty($additionalElements)) {
            $return = $args->getReturn();
            $containers = $return['containers'];

            // Set proper template name and field value
            foreach ($additionalElements as &$element) {
                $element['templateName'] = $element['component']['template'];
                $data = [];
                foreach ($element['data'] as $datum) {
                    $data[$datum['field']['name']] = $datum['value'];
                    if ($datum['field']['valueType'] === 'json') {
                        $data[$datum['field']['name']] = \Zend_Json::decode($datum['value']);
                    }
                }

                //We got liveshopping component
                if ($element['component']['xType'] === 'newsletter-components-live-shopping') {
                    if ($this->liveShoppingRepository->getLiveShoppingPlugin()->getActive()) {
                        $allLiveShoppingProducts = $this->liveShoppingRepository->getLiveProducts();

                        $liveShoppingProducts = $this->getLiveShoppingProducts($data, $allLiveShoppingProducts);
                        $element['values'] = $liveShoppingProducts;
                        $element['installed'] = true;
                    }
                }
                $element['data'] = $data;
            }
            unset($element);

            // merge the default containers and the third party components - sort them
            $elements = array_merge($additionalElements, $containers);
            // sort the elements
            uasort($elements, [$this, 'sortElementsByPosition']);

            $return['containers'] = $elements;

            $args->setReturn($return);
        }
    }

    /**
     * Called after the getMailingSuggest method of the newsletter controller
     * Gets suggestions from the newsletter core class
     *
     * @param \Enlight_Hook_HookArgs $hookArgs
     *
     * @return mixed
     */
    public function getMailingSuggest(\Enlight_Hook_HookArgs $hookArgs)
    {
        $args = $hookArgs->getArgs();
        $id = $args[0];
        $userId = $args[1];

        return $this->suggestService->getProductSuggestions($id, $userId);
    }

    /**
     * Helper function to sort an elements-array by position
     *
     * @param array $element1
     * @param array $element2
     *
     * @return int
     */
    private function sortElementsByPosition(array $element1 = [], array $element2 = [])
    {
        // First make sure, that all elements have a position field
        // This is 'startRow' for the old style containers and
        // 'position' for the new style elements
        $element1['position'] = isset($element1['startRow']) ? $element1['startRow'] : $element1['position'];
        $element2['position'] = isset($element2['startRow']) ? $element2['startRow'] : $element2['position'];

        if ($element1['position'] === $element2['position']) {
            return 0;
        }

        return ($element1['position'] < $element2['position']) ? -1 : 1;
    }

    /**
     * Match liveshopping widget products to liveshopping products and remove all not matched products
     * or show random products in no products are selected.
     *
     * @param array $elementData
     * @param array $liveShoppingProducts
     *
     * @return array
     */
    private function getLiveShoppingProducts(array $elementData, array $liveShoppingProducts)
    {
        $productIds = [];
        foreach ($elementData['article_data'] as $product) {
            if ($product['articleId']) {
                $productIds[] = $product['articleId'];
            }
        }

        if (count($productIds) > 0) {
            foreach ($liveShoppingProducts as $key => $liveProducts) {
                if (!in_array($liveProducts['articleID'], $productIds)) {
                    unset($liveShoppingProducts[$key]);
                }
            }
        } else {
            shuffle($liveShoppingProducts);
            $liveShoppingProducts = array_splice($liveShoppingProducts, 0, $elementData['number']);
        }

        return $liveShoppingProducts;
    }
}
