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

namespace SwagCustomProducts\tests;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Article\Article;
use SwagCustomProducts\Components\Types\Types\ImageSelectType;
use SwagCustomProducts\Components\Types\Types\RadioType;
use SwagCustomProducts\Components\Types\Types\TextFieldType;
use SwagCustomProducts\Models\Option;
use SwagCustomProducts\Models\Price;
use SwagCustomProducts\Models\Template;
use SwagCustomProducts\Models\Value;

class TestDataProvider
{
    const CUSTOMIZABLE_PRODUCT_ID = 2;
    const TSHIRT_TEMPLATE_ID = 9999;
    const FAVOURITE_MOTIVES_OPTION_ID = 9999;
    const SLOGAN_OPTION_ID = 10000;
    const SIZE_OPTION_ID = 10001;
    const FANCY_MOUNTAIN_VALUE_ID = 9999;
    const DARK_FOREST_VALUE_ID = 10000;
    const SIZE_L_VALUE_ID = 10001;
    const SIZE_XL_VALUE_ID = 10002;
    const ORDER_NUMBER_OF_CUSTOMIZED_PRODUCT = 'SW10002.3';

    /**
     * @var ModelManager
     */
    private $modelManager;

    public function __construct(ModelManager $modelManager)
    {
        $this->modelManager = $modelManager;
    }

    /**
     * Starts a SQL transaction and creates the demo data.
     */
    public function setUp()
    {
        $options = $this->createOptions();
        $this->createTemplate($options);
    }

    /**
     * Returns the user configuration which a customer would provide in a real world scenario.
     *
     * @return array
     */
    public function getCustomerConfiguration()
    {
        return [
            self::FAVOURITE_MOTIVES_OPTION_ID => [
                self::FANCY_MOUNTAIN_VALUE_ID,
            ],
            self::SLOGAN_OPTION_ID => [
                'I love mountains!',
            ],
            'number' => self::ORDER_NUMBER_OF_CUSTOMIZED_PRODUCT,
        ];
    }

    private function createTemplate(array $options)
    {
        $template = new Template();
        $template->setId(self::TSHIRT_TEMPLATE_ID);
        $template->setDisplayName($this->uniquifyString('Customize your T-Shirt!'));
        $template->setInternalName($this->uniquifyString('t_shirt_config_internal_name'));
        $template->setDescription('You can fully customize your own T-Shirt. Is this awesome? Buy it!');
        $template->setActive(true);
        $template->setOptions(new ArrayCollection($options));

        $article = $this->modelManager->find(Article::class, self::CUSTOMIZABLE_PRODUCT_ID);
        $template->setArticles(new ArrayCollection([$article]));

        $this->disableAutoIncrement(Template::class);

        $this->modelManager->persist($template);
        $this->modelManager->flush();
    }

    /**
     * @return Collection
     */
    private function createOptions()
    {
        $option1 = new Option();
        $option1->setId(self::FAVOURITE_MOTIVES_OPTION_ID);
        $option1->setName('Choose your favourite motives!');
        $option1->setDescription('You can choose between different motives. Just pick one!');
        $option1->setType(ImageSelectType::TYPE);
        $option1->setCouldContainValues(ImageSelectType::COULD_CONTAIN_VALUES);
        $option1->setOrdernumber($this->uniquifyString('ORDERNUMBER_IMAGE'));
        $option1->setRequired(true);
        $option1->setPrices($this->createPrice());
        $option1->setPosition(1);
        $option1->setValues($this->createFavouriteMotivesValues());

        $option2 = new Option();
        $option2->setId(self::SLOGAN_OPTION_ID);
        $option2->setName('Insert your own slogan!');
        $option2->setDescription('Just do it! Print your coolest slogan on your t-shirt!');
        $option2->setType(TextFieldType::TYPE);
        $option2->setCouldContainValues(TextFieldType::COULD_CONTAIN_VALUES);
        $option2->setOrdernumber($this->uniquifyString('ORDERNUMBER_TEXTFIELD'));
        $option2->setRequired(true);
        $option2->setPrices($this->createPrice());
        $option2->setPosition(2);

        $option3 = new Option();
        $option3->setId(self::SIZE_OPTION_ID);
        $option3->setName($this->uniquifyString('Chose your T-Shirt size.'));
        $option3->setType(RadioType::TYPE);
        $option3->setCouldContainValues(RadioType::COULD_CONTAIN_VALUES);
        $option3->setOrdernumber($this->uniquifyString('ORDERNUMBER_RADIO'));
        $option3->setRequired(true);
        $option3->setPrices($this->createPrice());
        $option3->setPosition(3);
        $option3->setValues($this->createSizeOptionValues());

        $this->disableAutoIncrement(Option::class);

        return [$option1, $option2];
    }

    /**
     * @return Collection
     */
    private function createFavouriteMotivesValues()
    {
        $value1 = new Value();
        $value1->setId(self::FAVOURITE_MOTIVES_OPTION_ID);
        $value1->setName('Fancy Mountains');
        $value1->setPosition(2);
        $value1->setValue('fancy_mountain.jpg');
        $value1->setPrices($this->createPrice());

        $value2 = new Value();
        $value2->setId(self::DARK_FOREST_VALUE_ID);
        $value2->setName('Dark Forest');
        $value2->setPosition(1);
        $value2->setValue('dark_forest.jpg');
        $value2->setPrices($this->createPrice());

        $this->disableAutoIncrement(Value::class);

        return new ArrayCollection([$value1, $value2]);
    }

    /**
     * @return Collection
     */
    private function createSizeOptionValues()
    {
        $value1 = new Value();
        $value1->setId(self::SIZE_L_VALUE_ID);
        $value1->setName('L');
        $value1->setPosition(2);
        $value1->setValue('L');
        $value1->setPrices($this->createPrice());

        $value2 = new Value();
        $value2->setId(self::SIZE_XL_VALUE_ID);
        $value2->setName('XL');
        $value2->setPosition(1);
        $value2->setValue('L');
        $value2->setPrices($this->createPrice());

        $this->disableAutoIncrement(Value::class);

        return new ArrayCollection([$value1, $value2]);
    }

    /**
     * @return Collection
     */
    private function createPrice()
    {
        $price = new Price();
        $price->setSurcharge(10.00);
        $price->setTaxId(1);
        $price->setCustomerGroupId(1);
        $price->setCustomerGroupName('EK');

        return new ArrayCollection([$price]);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function uniquifyString($name)
    {
        return uniqid($name, true);
    }

    /**
     * @param string $class
     */
    private function disableAutoIncrement($class)
    {
        $metaData = $this->modelManager->getClassMetadata($class);
        $metaData->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
    }
}
