<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components;

use Shopware\Models\Config\Element;
use Shopware_Plugins_Frontend_mediameetsFacebookPixel_Bootstrap;

class InstallManager
{
    /**
     * @var Shopware_Plugins_Frontend_mediameetsFacebookPixel_Bootstrap
     */
    protected $plugin;

    /**
     * InstallManager constructor
     *
     * @param Shopware_Plugins_Frontend_mediameetsFacebookPixel_Bootstrap $plugin
     */
    public function __construct(Shopware_Plugins_Frontend_mediameetsFacebookPixel_Bootstrap $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Parses array of success booleans.
     *
     * @param array $success
     * @return bool
     */
    protected function isSuccess($success)
    {
        foreach ($success as $value) {
            if (! $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Creates config form elements and translations.
     *
     * @return bool
     */
    protected function createConfigForm()
    {
        $form = $this->plugin->Form();

        $form->setElement(
            'checkbox',
            'status',
            [
                'label' => 'Plugin aktivieren/einbinden',
                'scope' => Element::SCOPE_SHOP,
                'required' => true,
                'value' => true,
                'position' => 5,
            ]
        );

        $form->setElement(
            'text',
            'facebookPixelID',
            [
                'label' => 'Facebook&#174; Pixel ID',
                'scope' => Element::SCOPE_SHOP,
                'required' => true,
                'description' => 'Hier die Pixel ID aus dem Business Manager oder dem Code von Facebook&#174; einfügen. Zeile: fbq(\'init\', \'XXXXXXXXXXXXXX\');',
                'position' => 10,
            ]
        );

        $form->setElement(
            'textarea',
            'additionalFacebookPixelIDs',
            [
                'label' => 'zusätzl. Facebook&#174; Pixel ID\'s',
                'scope' => Element::SCOPE_SHOP,
                'required' => false,
                'description' => 'Hier weitere Pixel ID\'s einfügen. Je Zeile eine Pixel ID.',
                'position' => 15,
            ]
        );

        $form->setElement(
            'select',
            'privacyMode',
            [
                'label' => 'Datenschutzmodus',
                'scope' => Element::SCOPE_SHOP,
                'required' => true,
                'value' => 'integrate',
                'position' => 20,
                'store' => [
                    ['integrate',
                        [
                            'de_DE' => 'Shopware Cookie Modus nutzen',
                            'en_GB' => 'Use Shopware Cookie mode',
                            'en_US' => 'Use Shopware Cookie mode',
                        ]
                    ],
                    ['active',
                        [
                            'de_DE' => 'Pixel aktiv, kein Hinweis für den Nutzer',
                            'en_GB' => 'Pixel active, no hint for the user',
                            'en_US' => 'Pixel active, no hint for the user',
                        ]
                    ],
                    ['optin',
                        [
                            'de_DE' => 'Pixel nicht aktiv, Nutzer wird gefragt ob Pixel aktiviert werden darf (Opt-In)',
                            'en_GB' => 'Pixel not active, user is asked if pixel can be activated (opt-in)',
                            'en_US' => 'Pixel not active, user is asked if pixel can be activated (opt-in)',
                        ]
                    ],
                    ['optout',
                        [
                            'de_DE' => 'Pixel aktiv, Nutzer wird gefragt ob Pixel deaktiviert werden soll (Opt-Out)',
                            'en_GB' => 'Pixel active, user is asked if pixel should be deactivated (opt-out)',
                            'en_US' => 'Pixel active, user is asked if pixel should be deactivated (opt-out)',
                        ]
                    ]
                ]
            ]
        );

        $form->setElement(
            'checkbox',
            'advancedMatching',
            [
                'label' => 'Erweiterter Datenabgleich',
                'scope' => Element::SCOPE_SHOP,
                'description' => 'Der erweiterte Datenabgleich übermittelt Kundendaten wie E-Mailadresse, Vorname, Name, Geschlecht, Geb.-Datum (sofern vorhanden), PLZ und Ort der Rechnungsadresse verschlüsselt an Facebook. Dazu muss der Kunde in seinem Konto im Shop eingeloggt sein. Mit den Daten kann Facebook Nutzer erkennen, die zum Zeitpunkt des Besuchs nicht auf Facebook eingeloggt waren.',
                'position' => 30,
            ]
        );

        $form->setElement(
            'checkbox',
            'autoConfig',
            [
                'label' => 'Automatische Konfiguration',
                'scope' => Element::SCOPE_SHOP,
                'description' => 'Mit der automatischen Konfiguration sendet der Facebook Pixel Button-Klick-Daten und Seiten-Metadaten (wie z. B. entsprechend der Opengraph- oder Schema.org-Formate strukturierte Daten) von der Webseite, um die Anzeigenauslieferung und -messung zu verbessern und das Pixel-Setup zu automatisieren. Die automatische Konfiguration ist seitens Facebook standardmäßig aktiviert. (Empfohlen)',
                'position' => 40,
                'value' => true,
            ]
        );

        $form->setElement(
            'checkbox',
            'customerStreams',
            [
                'label' => 'Customer Streams übermitteln',
                'scope' => Element::SCOPE_SHOP,
                'description' => 'Ist diese Option aktiviert, werden die Customer Streams, denen der eingeloggte Kunde zugewiesen ist, nach Facebook übermittelt.',
                'position' => 50,
                'value' => false,
            ]
        );

        $form->setElement(
            'select',
            'productIdentifier',
            [
                'label' => 'Produktidentifikation über',
                'scope' => Element::SCOPE_SHOP,
                'required' => true,
                'value' => 'ordernumber',
                'description' => 'Zur Identifikation der Produkte im Facebook Business Manager und dem Abgleich mit einem Facebook Produktkatalog.',
                'position' => 55,
                'store' => [
                    [
                        'ordernumber',
                        [
                            'de_DE' => 'Artikelnummer',
                            'en_GB' => 'Item number',
                            'en_US' => 'Item number',
                        ]
                    ],
                    [
                        'id',
                        [
                            'de_DE' => 'Interne Artikeldetail-ID',
                            'en_GB' => 'Internal item detail ID',
                            'en_US' => 'Internal item detail ID',
                        ]
                    ],
                ]
            ]
        );

        $form->setElement(
            'select',
            'priceMode',
            [
                'label' => 'Übermittelte Preise',
                'scope' => Element::SCOPE_SHOP,
                'required' => true,
                'value' => 'gross',
                'position' => 60,
                'store' => [
                    ['gross',
                        [
                            'de_DE' => 'Bruttopreise',
                            'en_GB' => 'Gross prices',
                            'en_US' => 'Gross prices',
                        ]
                    ],
                    ['net',
                        [
                            'de_DE' => 'Nettopreise',
                            'en_GB' => 'Net prices',
                            'en_US' => 'Net prices',
                        ]
                    ]
                ]
            ]
        );

        $form->setElement(
            'checkbox',
            'includeShipping',
            [
                'label' => 'Versandkosten einrechnen',
                'scope' => Element::SCOPE_SHOP,
                'description' => 'Ist diese Option aktiviert, werden die Versandkosten bei Warenkorbwerten eingerechnet.',
                'position' => 70,
                'value' => true,
            ]
        );

        $en_EN = [
            'facebookPixelID' => [
                'label' => 'Facebook&#174; Pixel ID',
                'description' => 'Insert the Pixel ID from the Business Manager or Facebook&#174; code here. Line: fbq(\'init\', \'XXXXXXXXXXXXXX\');',
            ],
            'additionalFacebookPixelIDs' => [
                'label' => 'add. Facebook&#174; Pixel ID\'s',
                'description' => 'Insert additional Pixel ID\'s. One Pixel ID per line.',
            ],
            'privacyMode' => [
                'label' => 'Privacy mode',
            ],
            'advancedMatching' => [
                'label' => 'Advanced matching',
                'description' => 'Advanced data matching transmits customer data such as e-mail address, first name, name, gender, birth date (if available), zip code and billing address location encrypted to Facebook&#174;. To do this, the customer must be logged in to his account in the shop. With the data, Facebook&#174; can recognize users who were not logged in to Facebook&#174; at the time of the visit.',
            ],
            'autoConfig' => [
                'label' => 'Automatic configuration',
                'description' => 'With the automatic configuration, the Facebook pixel sends button click data and page metadata (such as structured data according to the Opengraph or Schema.org formats) from the web page to improve ad delivery and metering, and to further automate the pixel setup. The automatic configuration is enabled by Facebook by default. (Recommended)',
            ],
            'customerStreams' => [
                'label' => 'Transmit Customer Streams',
                'description' => 'If this option is activated, the customer streams to which the logged-in customer is assigned will be transmitted to Facebook.',
            ],
            'productIdentifier' => [
                'label' => 'Product identification via',
            ],
            'priceMode' => [
                'label' => 'Transmitted prices',
            ],
            'includeShipping' => [
                'label' => 'Include shipping costs',
                'description' => 'If this option is activated, the shipping costs are included in shopping cart values.',
            ],
        ];

        $this->plugin->addFormTranslations([
            'en_GB' => $en_EN,
            'en_US' => $en_EN,
        ]);

        return true;
    }
}
