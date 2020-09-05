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

namespace SwagCustomProducts\tests\Functional\Components\Services;

use Shopware_Components_Document as Document;
use SwagCustomProducts\Components\Services\DateTimeService;
use SwagCustomProducts\Components\Services\DocumentValueExtender;
use SwagCustomProducts\Components\Services\HashManager;
use SwagCustomProducts\tests\KernelTestCaseTrait;

class DocumentValueExtenderTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_groupOptionsForDocument_should_merge_unnecessary_options_into_values()
    {
        $documentValueExtender = $this->getDocumentValueExtender();

        /** @var Document $document */
        $document = \Enlight_Class::Instance('Shopware_Components_Document');
        $document->_view = new DocumentViewMock();
        $document->_config = ['_previewForcePagebreak' => false];
        $document->_document = ['pagebreak' => 10];

        $hookArgs = new HookArgsMock($document);

        $document->_view->assign('Pages', require __DIR__ . '/_fixtures/basic_document_setup.php');
        $document->_view->assign('customProductOptionValues', require __DIR__ . '/_fixtures/custom_product_option_values.php');

        $documentValueExtender->groupOptionsForDocument($hookArgs);

        $result = $document->_view->getTemplateVars('Pages');

        // We set the pagebreak to 10, we have 18 positions - therefore we expect 2 pages
        static::assertCount(2, $result);
        static::assertEquals('Drop-Down Feld: Droppie B', $result[1][13]['name']);
        static::assertEquals('Farbauswahl: Rot', $result[0][1]['name']);
    }

    public function test_groupOptionsForMail_should_merge_unnecessary_options_into_values()
    {
        $documentValueExtender = $this->getDocumentValueExtender();

        $positions = require __DIR__ . '/_fixtures/basic_mail_setup.php';
        $result = $documentValueExtender->groupOptionsForMail($positions);

        static::assertCount(18, $result);
        static::assertEquals('Farbauswahl: Rot', $result[1]['articlename']);
    }

    /**
     * @return DocumentValueExtender
     */
    private function getDocumentValueExtender()
    {
        return new DocumentValueExtender(
            self::getContainer()->get('dbal_connection'),
            new HashManager(
                self::getContainer()->get('dbal_connection'),
                new DateTimeService(),
                self::getContainer()->get('shopware_storefront.context_service')
            )
        );
    }
}

class HookArgsMock extends \Enlight_Hook_HookArgs
{
    private $subject;

    public function __construct(Document $subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }
}

class DocumentViewMock
{
    public $storage = [];

    public function assign($key, $value)
    {
        $this->storage[$key] = $value;
    }

    public function getTemplateVars($key)
    {
        return $this->storage[$key];
    }
}
