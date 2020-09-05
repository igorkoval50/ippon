<?php

namespace BogxInstagramFeed\Bootstrap;

use Shopware\Components\Emotion\ComponentInstaller;

class EmotionElementInstaller
{
    /**
     * @var ComponentInstaller
     */
    private $emotionComponentInstaller;

    /**
     * @var string
     */
    private $pluginName;

    /**
     * @param string $pluginName
     * @param ComponentInstaller $emotionComponentInstaller
     */
    public function __construct($pluginName, ComponentInstaller $emotionComponentInstaller)
    {
        $this->emotionComponentInstaller = $emotionComponentInstaller;
        $this->pluginName = $pluginName;
    }

    /**
     * @throws \Exception
     */
    public function install()
    {
        $bogxFeedElement = $this->emotionComponentInstaller->createOrUpdate(
            $this->pluginName,
            'BogxInstagramFeed',
            [
				'name' => 'Bogx Instagram Widget',
				'xtype' => 'emotion-components-bogxfeed',
				'template' => 'bogx_instagram_feed',
				'cls' => 'emotion-components-bogx-feed',
				'description' => 'Bogx Instagram Widget: Instagram Feeds in Einkaufswelten verwenden'
            ]
        );

        $bogxFeedElement->createTextField([
            'name'       => 'username', // eindeutiger Name: so wird die Template-Variable für diesen Wert später heißen
            'fieldLabel' => 'Instagram Benutzername oder Such-Hashtag', // wird im Backend im Formular neben dem Textfeld angezeigt
			'supportText' => 'Wenn Sie Such-Hashtag angeben, dann bitte mit # (Raute) beginnen z.B. #meinsuchtag', // wird unter dem Textfeld angezeigt
			'helpText'	=> 'Das Feld ist ein Plichtfeld. Sie können hier entweder Ihren Benutzernamen oder ein Such-Hashtag angeben',
            'allowBlank' => false // wenn true, ist dieses Feld optional. Wenn false, muss es ausgefüllt werden
        ]);

        /*
        $bogxFeedElement->createTextField([
            'name'       => 'proxy_ip', // eindeutige Server- oder Proxy-IP
            'fieldLabel' => 'Proxy/Server-IP',
            'defaultValue' 	=> $_SERVER['SERVER_ADDR'],
            'supportText' => 'Wenn Instagram Ihre Server-IP nicht akzeptiert, bitte eine andere Server- oder eine Proxy-IP eintragen ',
            'helpText'	=> 'Dieses Feld wird nur für Benutzername verwendet. Eine kostenlose (anonymisierte) Proxy-IP finden Sie z.B. hier: https://www.proxyrotator.com. Besser sind kostenpflichtige Proxies - eventuell von Ihrem Hoster',
            'allowBlank' => true // wenn true, ist dieses Feld optional. Wenn false, muss es ausgefüllt werden
        ]);

        $bogxFeedElement->createTextField([
            'name'       => 'proxy_port', // eindeutige Server- oder Proxy-IP
            'fieldLabel' => 'Proxy/Server-Port',
            'defaultValue' 	=> $_SERVER['SERVER_PORT'],
            'supportText' => 'Zu der oberen Server/Proxy-IP passenden Server-Port eintragen ',
            'helpText'	=> 'Dieses Feld wird nur für Benutzername verwendet. Eine kostenlose (anonymisierte) Proxy-IP und Port finden Sie z.B. hier: https://www.proxyrotator.com. Besser sind kostenpflichtige Proxies - eventuell von Ihrem Hoster',
            'allowBlank' => true // wenn true, ist dieses Feld optional. Wenn false, muss es ausgefüllt werden
        ]);

        $bogxFeedElement->createTextField([
            'name'       => 'hashtags', // durch Leerzeichen getrennte Hastags
            'fieldLabel' => 'Hashtags zum Filtern', // wird im Backend im Formular neben dem Textfeld angezeigt
			'supportText' => 'Mehrere Hashtags/Keywords bitte mit LEERZEICHEN trennen z.B. #tag1 #tag2 keyword1 keyword2', // wird unter dem Textfeld angezeigt
			'helpText'	=> 'Das Feld ist optional, kann auch leer bleiben. Wenn das Feld leer bleibt, werden die Postings ungefiltert angezeigt',
            'allowBlank' => true // wenn true, ist dieses Feld optional. Wenn false, muss es ausgefüllt werden
        ]);

        $bogxFeedElement->createTextField([
            'name'       => 'blacklist1', // durch Leerzeichen getrennte Posting-ID's
            'fieldLabel' => 'Posting-ID Blacklist', // wird im Backend im Formular neben dem Textfeld angezeigt
			'supportText' => 'Mehrere Posting-IDs bitte mit LEERZEICHEN trennen z.B. 123456 8973 762997', // wird unter dem Textfeld angezeigt
			'helpText'	=> 'Das Feld ist optional. Die ID ist in jedem Posting-Text zu finden',
            'allowBlank' => true // wenn true, ist dieses Feld optional. Wenn false, muss es ausgefüllt werden
        ]);

        $bogxFeedElement->createTextField([
            'name'       => 'blacklist2', // durch Leerzeichen getrennte Owner-ID's
            'fieldLabel' => 'Owner-ID Blacklist', // wird im Backend im Formular neben dem Textfeld angezeigt
			'supportText' => 'Mehrere Owner-IDs bitte mit LEERZEICHEN trennen z.B. 123456 8973 762997', // wird unter dem Textfeld angezeigt
			'helpText'	=> 'Das Feld ist optional. Die ID ist in jedem Hashtag-Posting zu finden',
            'allowBlank' => true // wenn true, ist dieses Feld optional. Wenn false, muss es ausgefüllt werden
        ]);

        $bogxFeedElement->createNumberField([
            'name'       	=> 'cache_time',
            'fieldLabel' => 'Feed aktualisieren nach [Minuten]',
            'defaultValue' 	=> 360,
            'supportText' => 'Nach welcher Zeit [in Minuten] soll das Feed (Cache) wieder neu geladen/aktualisiert werden, 360 = 6 Stunden', // wird unter dem Textfeld angezeigt
            'allowBlank' => false
        ]);
        */

        $bogxFeedElement->createTextField([
            'name'       => 'cache_suffix',
            'fieldLabel' => 'Cache Suffix-Name', // wird im Backend im Formular neben dem Textfeld angezeigt
            'supportText' => 'Durch diesen (optionalen) Namen kann dasselbe Feed für Desktop und Tablett/Smartphone unterschieden werden', // wird unter dem Textfeld angezeigt
            'helpText'	=> 'Für ein Feed, das auch auf Tablett/Smartphones gezeigt werden soll, aber weniger Postings beinhaltet z.B. den Namen "mobile" angeben',
            'allowBlank' => true // wenn true, ist dieses Feld optional. Wenn false, muss es ausgefüllt werden
        ]);

        $bogxFeedElement->createNumberField([
            'fieldLabel' => 'Bilder pro Zeile',
            'name'       	=> 'items-in-row',
			'defaultValue' 	=> 3,
			'supportText' => 'Wieviele Bilder pro Zeile anzeigen', // wird unter dem Textfeld angezeigt	
            'allowBlank' => false
        ]);

        $bogxFeedElement->createNumberField([
            'fieldLabel' => 'Bilder insgesamt',
            'name'       => 'limit',
            'defaultValue' 	=> 15,
            'supportText' => 'Wieviele Bilder insgesamt anzeigen', // wird unter dem Textfeld angezeigt
            'allowBlank' => false
        ]);

        $bogxFeedElement->createNumberField([
            'fieldLabel' => 'Horizontaler Abstand [Pixel]',
            'name'       => 'horizontal_padding',
            'defaultValue' 	=> 1,
            'supportText' => 'Horizontaler Abstand zwischen den Bildern/Postings', // wird unter dem Textfeld angezeigt
            'allowBlank' => false
        ]);

        $bogxFeedElement->createNumberField([
            'fieldLabel' => 'Vertikaler Abstand [Pixel]',
            'name'       => 'vertical_padding',
            'defaultValue' 	=> 1,
            'supportText' => 'Vertikaler Abstand zwischen den Bildern/Postings', // wird unter dem Textfeld angezeigt
            'allowBlank' => false
        ]);

        $bogxFeedElement->createComboBoxField([
            'fieldLabel' => 'Layout',
            'name'       => 'layout',
			'supportText' => 'In welchem Layout sollen die Feed-Bilder angezeigt werden', // wird unter dem Textfeld angezeigt
            'allowBlank' => false,
            'store' => 'Shopware.apps.bogxFeed.store.FeedLayoutStore',
            'queryMode' => 'local',
            'displayField' => 'name',
            'valueField' => 'value',
            'defaultValue' => 'grid'
        ]);

        $bogxFeedElement->createDisplayField([
			'name' 			=> 'info',
			'defaultValue' 	=> 'Welche Inhalte pro Posting/Bild sollen angezeigt werden - gilt nur für das Grid mit HOVER-Effekt:',
			'fieldlabel'	=> 'Infos:'
		]);

        $bogxFeedElement->createCheckboxField([
            'name' => 'profile',
            'defaultValue' => true,
            'fieldLabel' => 'Profil?',
            'supportText' => 'Profil = Verlinktes Profilbild + Username oder Hashtag anzeigen?'
        ]);

        $bogxFeedElement->createCheckboxField([
			'name' => 'captions',
			'defaultValue' => true,
			'fieldLabel' => 'Captions?',
			'supportText' => 'Captions = Posting-Texte + Hashtags anzeigen?'
		]);

        $bogxFeedElement->createCheckboxField([
			'name' => 'counts',
			'defaultValue' => true,
			'fieldLabel' => 'Counts?',
			'supportText' => 'Counts = Likes-Zähler + Comments-Zähler anzeigen?'
		]);

        /*
        $bogxFeedElement->createCheckboxField([
			'name' => 'insta_id',
			'defaultValue' => true,
			'fieldLabel' => 'Posting-ID?',
			'supportText' => 'Posting-ID dient zur Identifikation und einem optionalen Setzen des Postings auf Blacklist'
		]);

        $bogxFeedElement->createCheckboxField([
			'name' => 'owner_id',
			'defaultValue' => true,
			'fieldLabel' => 'Owner-ID?',
			'supportText' => 'Owner-ID dient zur Identifikation des Posting-Authors und einem optionalen Setzen seiner Postings auf Blacklist'
		]);			
		*/
			
		
    }
}