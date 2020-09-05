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

namespace SwagCustomProducts\tests\Functional\Subscriber;

use SwagCustomProducts\Subscriber\Basket;
use SwagCustomProducts\Subscriber\InquiryBasket;
use SwagCustomProducts\tests\KernelTestCaseTrait;
use SwagCustomProducts\tests\ServicesHelper;

class InquiryBasketTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    const INQUIRY_FIELD_ID = 69;

    public function test_onGetContent_should_add_custom_products_content()
    {
        $serviceHelper = new ServicesHelper(Shopware()->Container());
        $serviceHelper->registerServices();

        Shopware()->Container()->set('front', new ControllerMock());
        Shopware()->Container()->get('session')->offsetSet('sessionId', 'session_id');

        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/basket_inquiry.sql'));

        $formController = new \Shopware_Controllers_Frontend_Forms();
        $formController->_postData = [
            72 => [],
            74 => [],
            75 => [],
            71 => [],
            73 => [],
            self::INQUIRY_FIELD_ID => [],
        ];

        $request = new \Enlight_Controller_Request_RequestTestCase();
        $request->setParam('sInquiry', 'basket');

        $formController->setRequest($request);

        $args = new \Enlight_Hook_HookArgs($formController, '');
        $args->setReturn([
            'sElements' => $this->getsElements(),
        ]);

        $modules = Shopware()->Container()->get('modules');

        Shopware()->Container()->set(
            'modules',
            new ModulesMock()
        );

        $service = new InquiryBasket(Shopware()->Container());
        $result = $service->onGetContent($args);

        Shopware()->Container()->set(
            'modules',
            $modules
        );

        $resultMessage = $result['sElements'][self::INQUIRY_FIELD_ID]['value'];

        static::assertStringContainsString('1 x Strandtuch "Ibiza"  (SW10178) - 16,77 &euro;', $resultMessage);

        // Single value option, i.e. textbox, textarea etc
        static::assertStringContainsString('text1', $resultMessage, 'Could not add single value option');
        static::assertStringContainsString('TestText1', $resultMessage, 'Could not render single value option values');
        static::assertStringContainsString('10,00 &euro;', $resultMessage, 'Could not render single value option price');

        // Multi value options, i.e. checkboxes, radio etc
        static::assertStringContainsString('checkbox', $resultMessage, 'Could not add multi value option');
        static::assertStringContainsString('value1', $resultMessage, 'Could not render multi value option values');
        static::assertStringContainsString('value2', $resultMessage, 'Could not render multi value option values');
        static::assertStringContainsString('20,00 &euro;', $resultMessage, 'Could not render multi value option price');
    }

    private function getsElements()
    {
        return [
            72 => [
                'id' => '72',
                'name' => 'anrede',
                'note' => '',
                'typ' => 'select',
                'required' => '1',
                'label' => 'Anrede',
                'class' => 'normal',
                'value' => 'Frau;Herr',
                'error_msg' => '',
            ],
            74 => [
                'id' => '74',
                'name' => 'email',
                'note' => '',
                'typ' => 'text',
                'required' => '1',
                'label' => 'eMail-Adresse',
                'class' => 'normal',
                'value' => '',
                'error_msg' => '',
            ],
            75 => [
                'id' => '75',
                'name' => 'vorname',
                'note' => '',
                'typ' => 'text',
                'required' => '1',
                'label' => 'Vorname',
                'class' => 'normal',
                'value' => '',
                'error_msg' => '',
            ],
            71 => [
                'id' => '71',
                'name' => 'nachname',
                'note' => '',
                'typ' => 'text',
                'required' => '1',
                'label' => 'Nachname',
                'class' => 'normal',
                'value' => '',
                'error_msg' => '',
            ],
            73 => [
                'id' => '73',
                'name' => 'telefon',
                'note' => '',
                'typ' => 'text',
                'required' => '0',
                'label' => 'Telefon',
                'class' => 'normal',
                'value' => '',
                'error_msg' => '',
            ],
            self::INQUIRY_FIELD_ID => [
                'id' => '69',
                'name' => 'inquiry',
                'note' => '',
                'typ' => 'textarea',
                'required' => '1',
                'label' => 'Anfrage',
                'class' => 'normal',
                'value' => 'Bitte unterbreiten Sie mir ein Angebot über die nachfolgenden Positionen
1 x Kobra Vodka 37,5%  (SW10012) - 9,99 EUR',
                'error_msg' => '',
            ],
        ];
    }
}

class Enlight_TestCase_Args extends \Enlight_Hook_HookArgs
{
    /**
     * @var mixed
     */
    public $return;

    /**
     * @return ControllerMock
     */
    public function getSubject()
    {
        return new ControllerMock();
    }

    public function setReturn($data)
    {
        $this->return = $data;
    }

    public function getReturn()
    {
        return $this->return;
    }
}

class ControllerMock extends \Shopware_Controllers_Frontend_Forms
{
    public function __construct()
    {
    }

    public function Request()
    {
        return new RequestMock();
    }
}

class RequestMock extends \Enlight_Controller_Request_RequestTestCase
{
    public function getParam($key, $default = null)
    {
        return 'basket';
    }

    public function getQuery($key = null, $default = null)
    {
        return '';
    }
}

class ModulesMock
{
    /**
     * @return InquiryBasketTestBasketMock
     */
    public function Basket()
    {
        return new InquiryBasketTestBasketMock();
    }
}

class InquiryBasketTestBasketMock
{
    /**
     * @return array
     */
    public function sGetBasket()
    {
        $basketData = require __DIR__ . '/_fixtures/basketArray.php';

        $subscriber = new Basket(Shopware()->Container());

        $args = new Enlight_TestCase_Args($this, '');
        $args->setReturn($basketData);

        return $subscriber->getBasket($args);
    }
}
