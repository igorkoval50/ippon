<?php

namespace Shopware\Themes\Ippon;

use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Components\Form as Form;
use Shopware\Components\Theme\ConfigSet;

class Theme extends \Shopware\Components\Theme
{
    protected $extend = 'Responsive';

    protected $name = 'Ippon Theme - by Dupp GmbH';

    protected $description = 'Ippon Theme';

    protected $author = 'David Strauch - Dupp GmbH';

    protected $license = 'Proprietär';

	/** @var array Defines the files which should be compiled by the javascript compressor */
    protected $css = [
        'src/css/jquery.fancybox.min.css'
    ];

	protected $javascript = array(
		'src/js/vendors/bootstrap/bootstrap.affix.js',
        'src/js/lazyload.min.js',
		'src/js/jquery.search.js',
		'src/js/jquery.menu-scroller.js',
		'src/js/jquery.language-switcher.js',
		'src/js/jquery.language-country.js',
		'src/js/jquery.themescripts.js',
        'src/js/jquery.mag-newsletter-box.js',
        'src/js/jquery.lazyload.js',
        'src/js/jquery.last-seen-products.js',
        'src/js/jquery.listing-actions.js',
        'src/js/jquery.listing-items-count.js',
        'src/js/truncate.js',
        'src/js/jquery.quantity-select.js',
        'src/js/jquery.image-slider.js',
        'src/js/slick.min.js',
        'src/js/slick-lightbox.min.js',
        'src/js/tls-instagram-slider.js',
        'src/js/jquery.add-kssarticle.js',
        'src/js/jquery.shopware-ippon-responsive.js',
        'src/js/jquery.collapse-cart.js',
        'src/js/jquery.ajax-variant.js',
        'src/js/jquery.fancybox.min.js',
        'src/js/jquery.zoom.min.js',
        'src/js/custom.js',

	);

    public function createConfig(Form\Container\TabContainer $container)
    {
	    $fieldSettingsTab = array(
		    'attributes' => array(
			    'layout' => 'anchor',
			    'autoScroll' => true,
			    'padding' => '0'
		    )
	    );

	    $fieldSettings = array(
		    'attributes' => array(
			    'layout' => 'anchor',
			    'padding' => '10',
			    'margin'=> '5',
			    'defaults' => array(
				    'anchor' => '100%',
				    'labelWidth' => 155
			    )
		    )
	    );

	    /* tab */
	    $tab = $this->createTab(
		    'extended_theme_config',
		    'Erweiterte Konfiguration',
		    $fieldSettingsTab
	    );
	    $container->addTab($tab);

		/* $fieldsetPrint */
		$fieldsetPrint = $this->createFieldset(
		    'print_settings_fields',
		    'Print Logo',
		    $fieldSettings
		);
		$tab->addElement($fieldsetPrint);


		$printLogo = $this->createMediaField(
		    'printLogo',
		    'Print Logo',
		    'frontend/_public/src/img/logos/logo--tablet.png',
		    ['attributes' => ['lessCompatible' => false]]
		);
		$fieldsetPrint->addElement($printLogo);

		/* fieldsetBlog */
		$fieldsetBlog = $this->createFieldset(
		    'blog_settings_fields',
		    'Blog',
		    $fieldSettings
		);
		$tab->addElement($fieldsetBlog);

		/* fields */
		$blogValidationTime = $this->createNumberField(
		    'blogValidationTime',
		    'Badge gültig für [n] Tage',
		    '20',
		    ['attributes' => ['lessCompatible' => false]]
		);
		$fieldsetBlog->addElement($blogValidationTime);

	    /* fieldsetLieferzeit */
	    $fieldsetLieferzeit = $this->createFieldset(
		    'lieferzeit_settings_fields',
		    'Detailseite',
		    $fieldSettings
	    );
	    $tab->addElement($fieldsetLieferzeit);

	    /* fields */
	    $showStock = $this->createCheckboxField(
		    'showStock',
		    'Bestand anzeigen',
		    false,
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLieferzeit->addElement($showStock);

	    $showVariantListing = $this->createCheckboxField(
		    'showvariantlisting',
		    'Schnellkauf anzeigen',
		    false,
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLieferzeit->addElement($showVariantListing);

		/* fieldsets */
		$fieldsetBtns = $this->createFieldset(
		    'custom_settings_fields',
		    'Fixierte Buttons',
		    $fieldSettings
		);
		$tab->addElement($fieldsetBtns);

		$linkText1 = $this->createTextField(
			'linkText1',
			'1 - Link Text',
			'',
			['attributes' => ['lessCompatible' => false]]
		);
	    $fieldsetBtns->addElement($linkText1);

	    $linkIcon1 = $this->createTextField(
		    'linkIcon1',
		    '1 - Link Icon',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetBtns->addElement($linkIcon1);

	    $linkId1 = $this->createNumberField(
		    'linkId1',
		    '1 - Link ID',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetBtns->addElement($linkId1);

	    $linkText2 = $this->createTextField(
		    'linkText2',
		    '2 - Link Text',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetBtns->addElement($linkText2);

	    $linkIcon2 = $this->createTextField(
		    'linkIcon2',
		    '2 - Link Icon',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetBtns->addElement($linkIcon2);

	    $linkId2 = $this->createNumberField(
		    'linkId2',
		    '2 - Link ID',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetBtns->addElement($linkId2);

	    $linkText3 = $this->createTextField(
		    'linkText3',
		    '3 - Link Text',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetBtns->addElement($linkText3);

	    $linkIcon3 = $this->createTextField(
		    'linkIcon3',
		    '3 - Link Icon',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetBtns->addElement($linkIcon3);

	    $linkId3 = $this->createNumberField(
		    'linkId3',
		    '3 - Link ID',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetBtns->addElement($linkId3);

	    /* tab */
	    $tabLanguageSwitcher = $this->createTab(
		    'extended_theme_language_switcher',
		    'Language Switcher',
		    $fieldSettingsTab
	    );
	    $container->addTab($tabLanguageSwitcher);

	    /* $fieldsetPrint */
	    $fieldsetLanguageSwitcher = $this->createFieldset(
		    'language_swichter_fieldset',
		    'Sprachen',
		    $fieldSettings
	    );
	    $tabLanguageSwitcher->addElement($fieldsetLanguageSwitcher);

	    $language1Title = $this->createCheckboxField(
		    'languageSwitcher',
		    'Aktiv',
		    true,
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language1Title);

	    $language1Title = $this->createTextField(
		    'language1Title',
		    'Sprache 1 - Titel',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language1Title);

	    $language1Url = $this->createTextField(
		    'language1Url',
		    'Sprache 1 - Url',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language1Url);

	    $language1Code = $this->createTextField(
		    'language1Code',
		    'Sprache 1 - Code',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language1Code);

	    $language1Media = $this->createMediaField(
		    'language1Media',
		    'Sprache 1 - Logo',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language1Media);

	    $language2Title = $this->createTextField(
		    'language2Title',
		    'Sprache 2 - Titel',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language2Title);

	    $language2Url = $this->createTextField(
		    'language2Url',
		    'Sprache 2 - Url',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language2Url);

	    $language2Code = $this->createTextField(
		    'language2Code',
		    'Sprache 2 - Code',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language2Code);

	    $language2Media = $this->createMediaField(
		    'language2Media',
		    'Sprache 2 - Logo',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language2Media);

	    $language3Title = $this->createTextField(
		    'language3Title',
		    'Sprache 3 - Titel',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language3Title);

	    $language3Url = $this->createTextField(
		    'language3Url',
		    'Sprache 3 - Url',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language3Url);

	    $language3Code = $this->createTextField(
		    'language3Code',
		    'Sprache 3 - Code',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language3Code);

	    $language3Media = $this->createMediaField(
		    'language3Media',
		    'Sprache 3 - Logo',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language3Media);

	    $language4Title = $this->createTextField(
		    'language4Title',
		    'Sprache 4 - Titel',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language4Title);

	    $language4Url = $this->createTextField(
		    'language4Url',
		    'Sprache 4 - Url',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language4Url);

	    $language4Code = $this->createTextField(
		    'language4Code',
		    'Sprache 4 - Code',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language4Code);

	    $language4Media = $this->createMediaField(
		    'language4Media',
		    'Sprache 4 - Logo',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language4Media);

	    $language5Title = $this->createTextField(
		    'language5Title',
		    'Sprache 5 - Titel',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language5Title);

	    $language5Url = $this->createTextField(
		    'language5Url',
		    'Sprache 5 - Url',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language5Url);

	    $language5Code = $this->createTextField(
		    'language5Code',
		    'Sprache 5 - Code',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language5Code);

	    $language5Media = $this->createMediaField(
		    'language5Media',
		    'Sprache 5 - Logo',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language5Media);

	    $language6Title = $this->createTextField(
		    'language6Title',
		    'Sprache 6 - Titel',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language6Title);

	    $language6Url = $this->createTextField(
		    'language6Url',
		    'Sprache 6 - Url',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language6Url);

	    $language6Code = $this->createTextField(
		    'language6Code',
		    'Sprache 6 - Code',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language6Code);

	    $language6Media = $this->createMediaField(
		    'language6Media',
		    'Sprache 6 - Logo',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language6Media);

	    $language7Title = $this->createTextField(
		    'language7Title',
		    'Sprache 7 - Titel',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language7Title);

	    $language7Url = $this->createTextField(
		    'language7Url',
		    'Sprache 7 - Url',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language7Url);

	    $language7Code = $this->createTextField(
		    'language7Code',
		    'Sprache 7 - Code',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language7Code);

	    $language7Media = $this->createMediaField(
		    'language7Media',
		    'Sprache 7 - Logo',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language7Media);

	    $language8Title = $this->createTextField(
		    'language8Title',
		    'Sprache 8 - Titel',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language8Title);

	    $language8Url = $this->createTextField(
		    'language8Url',
		    'Sprache 8 - Url',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language8Url);

	    $language8Code = $this->createTextField(
		    'language8Code',
		    'Sprache 8 - Code',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language8Code);

	    $language8Media = $this->createMediaField(
		    'language8Media',
		    'Sprache 8 - Logo',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language8Media);

	    $language9Title = $this->createTextField(
		    'language9Title',
		    'Sprache 9 - Titel',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language9Title);

	    $language9Url = $this->createTextField(
		    'language9Url',
		    'Sprache 9 - Url',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language9Url);

	    $language9Code = $this->createTextField(
		    'language9Code',
		    'Sprache 9 - Code',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language9Code);

	    $language9Media = $this->createMediaField(
		    'language9Media',
		    'Sprache 9 - Logo',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language9Media);

	    $language10Title = $this->createTextField(
		    'language10Title',
		    'Sprache 10 - Titel',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language10Title);

	    $language10Url = $this->createTextField(
		    'language10Url',
		    'Sprache 10 - Url',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language10Url);

	    $language10Code = $this->createTextField(
		    'language10Code',
		    'Sprache 10 - Code',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language10Code);

	    $language10Media = $this->createMediaField(
		    'language10Media',
		    'Sprache 10 - Logo',
		    '',
		    ['attributes' => ['lessCompatible' => false]]
	    );
	    $fieldsetLanguageSwitcher->addElement($language10Media);
    }



	/**
     * Helper function to merge default theme colors with color schemes
     * @param ArrayCollection $collection
     */
    public function createConfigSets(ArrayCollection $collection)
    {
        $set = new ConfigSet();
        $set->setName('Ippon Color-Sheme');
	    $set->setDescription('Farbschema speziell für Ippon');
	    $set->setValues(array(
            "brand-primary" => "#e20612",
		    "brand-primary-light" => "lighten(@brand-primary, 10%)",
            "brand-secondary" => "#1b1b1b",
		    "brand-secondary-dark" => "darken(@brand-secondary, 7%)",
            "text-color" => "#333333",
            "text-color-dark" => "#111111",
            "gray" => "#ede8e5",
            "border-color" => "@gray-dark",
		    "body-bg" => "#ffffff",
            "input-bg" => "#ffffff",
		    "input-border" => "@border-color",
		    "input-focus-bg" => "@body-bg",
		    "input-focus-border" => "@brand-primary",
		    "input-focus-color" => "@text-color",
		    "font-base-stack" => "\"Montserrat\", \"Helvetica Neue\", Helvetica, Arial, \"Lucida Grande\", sans-serif;",
		    "font-headline-stack" => "'Bebas Neue', \"Helvetica Neue\", Helvetica, Arial, \"Lucida Grande\", sans-serif;",
		    "font-size-base" => "14",
		    "input-font-size" => "@font-size-base",
		    "font-size-h1" => "36",
		    "font-size-h2" => "30",
		    "font-size-h3" => "24",
		    "font-size-h4" => "20",
		    "font-size-h5" => "@font-size-base",
		    "font-size-h6" => "12",
		    "font-light-weight" => "300",
		    "font-base-weight" => "400",
		    "font-bold-weight" => "600",
		    "badge-discount-bg" => "@brand-primary"
        ));
        $collection->add($set);
    }
}
