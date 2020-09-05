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

namespace SwagNewsletter\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use SwagNewsletter\Models\Component;
use SwagNewsletter\Models\Field;

class ComponentTest extends TestCase
{
    public function test_gettersAndSetters()
    {
        $model = new Component();

        $fields = [
            $this->getTestField(),
        ];

        $model->setFields($fields);

        $model->setCls('TEST_CLASS');
        $model->setPluginId(999);
        $model->setXType('TEST_X_TYPE');
        $model->setConvertFunction('TEST_CONVERTER');

        $this->assertEquals('TEST', $model->getFields()[0]->getFieldLabel());
        $this->assertEquals('TEST_CLASS', $model->getCls());
        $this->assertEquals(999, $model->getPluginId());
        $this->assertEquals('TEST_X_TYPE', $model->getXType());
        $this->assertEquals('TEST_CONVERTER', $model->getConvertFunction());
    }

    public function test_createField()
    {
        $model = new Component();

        $field = $model->createField(['fieldLabel' => 'TEST']);
        $this->assertEquals('TEST', $field->getFieldLabel());
    }

    public function test_createCheckboxField()
    {
        $model = new Component();

        $field = $model->createCheckboxField([]);
        $this->assertEquals('checkboxfield', $field->getXType());
    }

    public function test_createComboBoxField()
    {
        $model = new Component();

        $field = $model->createComboBoxField([]);
        $this->assertEquals('combobox', $field->getXType());
    }

    public function test_createDateField()
    {
        $model = new Component();

        $field = $model->createDateField([]);
        $this->assertEquals('datefield', $field->getXType());
    }

    public function test_createDisplayField()
    {
        $model = new Component();

        $field = $model->createDisplayField([]);
        $this->assertEquals('displayfield', $field->getXType());
    }

    public function test_createHiddenField()
    {
        $model = new Component();

        $field = $model->createHiddenField([]);
        $this->assertEquals('hiddenfield', $field->getXType());
    }

    public function test_createHtmlEditorField()
    {
        $model = new Component();

        $field = $model->createHtmlEditorField([]);
        $this->assertEquals('htmleditor', $field->getXType());
    }

    public function test_createNumberField()
    {
        $model = new Component();

        $field = $model->createNumberField([]);
        $this->assertEquals('numberfield', $field->getXType());
    }

    public function test_createRadioField()
    {
        $model = new Component();

        $field = $model->createRadioField([]);
        $this->assertEquals('radiofield', $field->getXType());
    }

    public function test_createTextField()
    {
        $model = new Component();

        $field = $model->createTextField([]);
        $this->assertEquals('textfield', $field->getXType());
    }

    public function test_createTextAreaField()
    {
        $model = new Component();

        $field = $model->createTextAreaField([]);
        $this->assertEquals('textareafield', $field->getXType());
    }

    public function test_createTimeField()
    {
        $model = new Component();

        $field = $model->createTimeField([]);
        $this->assertEquals('timefield', $field->getXType());
    }

    public function test_createCodeMirrorField()
    {
        $model = new Component();

        $field = $model->createCodeMirrorField([]);
        $this->assertEquals('codemirrorfield', $field->getXType());
    }

    public function test_createTinyMceField()
    {
        $model = new Component();

        $field = $model->createTinyMceField([]);
        $this->assertEquals('tinymce', $field->getXType());
    }

    public function test_createMediaSelectionField()
    {
        $model = new Component();

        $field = $model->createMediaSelectionField([]);
        $this->assertEquals('mediaselectionfield', $field->getXType());
    }

    private function getTestField()
    {
        $field = new Field();

        $field->fromArray([
            'fieldLabel' => 'TEST',
            'valueType' => '',
            'store' => '',
            'supportText' => '',
            'helpTitle' => '',
            'helpText' => '',
            'defaultValue' => '',
            'displayField' => '',
            'valueField' => '',
            'allowBlank' => false,
        ]);

        return $field;
    }
}
