<?php

/**
 * configuration can contain models, attributes, form data, menu item(s)
 */

// Get all countries
$countries = [];
$query          = Shopware()->Db()->query("show tables like 's_neti_ip2location%'");
if ($query->rowCount() != 0) {
    $contrydb = Shopware()->Db()->fetchAll("SELECT DISTINCT(country_code), country_name FROM s_neti_ip2location ORDER BY country_name");
    $countries = [['0', 'default (für alle nicht zugewiesenen Länder)']];

    foreach ($contrydb as $k => $v) {
        if ('-' !== $v['country_code']) {
            $countries[] = [$v['country_code'], $v['country_name']];
        }
    }
}

return [
    'redmine'    => [
        'projectID' => '000000-012-340',
        'contact'   => 'sb@netinventors.de'

    ],
    'form' => [
        [
            'type' => 'select',
            'name' => 'countrycode',
            'label' => [
                'de_DE' => 'Zugewiesene Länder',
                'en_GB' => 'Assigned Countries'
            ],
            'description' => [
                'de_DE' => 'Dieser Subshop wird Besuchern aus den zugewiesenen Ländern angeboten. ' .
                    'Der "Default"-Eintrag gilt für Besucher aus nicht zugewiesenen Ländern.',
                'en_GB' => 'The current subshop will be offered to users of the assigned countries. ' .
                    'The "default" value counts for unassigned countries.'
            ],
            'value' => [],
            'scope' => Shopware\Models\Config\Element::SCOPE_SHOP,
            'required' => true,
            'store' => $countries,
            'options' => [
                'multiSelect' => true,
                'editable'    => false,
                'forceSelection' => true
            ],
        ],
        [
            'boolean',
            'silent_redirect',
            [
                'de_DE' => 'Ohne Rückfrage umleiten',
                'en_GB' => 'Redirect without notification'
            ],
            [
                'de_DE' => 'Leitet den Besucher ohne Rückfrage um.',
                'en_GB' => 'Redirect the visitor without notification.'
            ],
            false
        ],
        [
            'select',
            'detectionMethod',
            [
                'de_DE' => 'Erkennungsmethode',
                'en_GB' => 'Detection method'
            ],
            [
                'de_DE' => 'Zur Erkennung des Besucher-Landes soll folgende Methode genutzt werden',
                'en_GB' => 'The selected method should be used for the country detection of the customer.'
            ],
            'both',
            false,
            true,
            [
                ['browser', 'Browser-Sprache / language setting in browser'],
                ['ip', 'IP-Adresse / IP address'],
                ['both', 'Zunächst Browser-Sprache, dann IP-Adresse / Browser setting first, then IP address'],
            ],
            false
        ],
        [
            'boolean',
            'updatecurrency',
            [
                'de_DE' => 'Währung automatisch ändern',
                'en_GB' => 'Change currency automatically'
            ],
            [
                'de_DE' => 'Anhand des erkannten Aufenthaltslandes (durch IP ermittelt) wird automatisch die ' .
                    'passende Währung ausgewählt, sofern sie im Subshop verfügbar ist.',
                'en_GB' => 'Based on the recognized country of residence (as determined by IP) the right currency ' .
                    'is selected automatically, if it is available in the current Subshop.'
            ],
            true,
            Shopware\Models\Config\Element::SCOPE_SHOP
        ],
        [
            'boolean',
            'subShopdeactivate',
            [
                'de_DE' => 'Subshop deaktivieren',
                'en_GB' => 'Deactivate Subshop'
            ],
            [
                'de_DE' => '',
                'en_GB' => ''
            ],
            false,
            Shopware\Models\Config\Element::SCOPE_SHOP
        ],
        [
            'text',
            'unsetCookieIp',
            [
                'de_DE' => 'Cookie nicht setzen für folgende IP-Adressen',
                'en_GB' => 'Do not set cookie for these IP addresses'
            ],
            [
                'de_DE' => 'Kommaseparierte Liste. Bei diesen IP-Adressen wird ein eventuelles Modal bei jedem Seitenaufruf angezeigt.',
                'en_GB' => 'Comma separated list. For these IP addresses, the modal will appear on every refresh of the page.'
            ],
            '',
            Shopware\Models\Config\Element::SCOPE_SHOP
        ],
        [
            'boolean',
            'subShopRedirect',
            [
                'de_DE' => 'Subshop-übergreifende Umleitung',
                'en_GB' => 'Route to other Subshops'
            ],
            [
                'de_DE' => 'Wenn innerhalb des aktuellen Subshops kein passender Sprachshop gefunden wird, wird in den anderen Subshops weiter gesucht.',
                'en_GB' => 'If no fitting language shop will be found in the current subshop, the pugin will continue searching in other subshops.'
            ],
            false,
            Shopware\Models\Config\Element::SCOPE_SHOP
        ],
        [
            'boolean',
            'searchSubshopsBeforeDefault',
            [
                'de_DE' => 'Subshop-übergreifende Suche vor Fallback auf "default"',
                'en_GB' => 'Search inside other subshops before falling back to default'
            ],
            [
                'de_DE' => 'Ist diese und die überliegende Option aktiviert und im aktuellen Subshop-Kontext kein passender Sprachshop gefunden, wird ein passender Sprachshop innerhalb anderer Subshops gesucht. Erst wenn diese Suche erfolglos bleibt, wird auf den nächstbesten "default"-Shop zurückgegriffen.<br /><br />Wird empfohlen, wenn Sprachshops als Subshops angelegt sind.',
                'en_GB' => 'If this option and the option above is activated and no suitable language shop is found inside the current subshop context, a suitable language shop will be searched for within other subshops. Only if this search is unsuccessful, the next best "default" shop is used.<br /><br />Recommended if langugage shops are configured as subshops.'
            ],
            false,
            Shopware\Models\Config\Element::SCOPE_SHOP
        ],
        [
            'boolean',
            'debugMode',
            [
                'de_DE' => 'Debug-Modus aktivieren',
                'en_GB' => 'Enable debug mode',
            ],
            [
                'de_DE' => 'Session wird nach jedem Request zurückgesetzt, Plugin schreibt zusätzliche Debug Ausgaben (FirePHP).',
                'en_GB' => 'Session will be reset after each request, plugin writes additional debug output (FirePHP)'
            ],
            false,
            Shopware\Models\Config\Element::SCOPE_SHOP,
        ],
    ]
];

