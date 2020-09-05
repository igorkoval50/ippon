<?php
/*
Dieser Quellcode, zugehörige Dokumentation und alle damit verbundenen Komponenten unterliegen dem deutschen Urheberrecht und Leistungsschutzrecht.

Der Lizenzgeber und seine Zulieferer sind Inhaber der vollständigen Rechte am Quellcode-Produkt, inklusive aller 
diesbezüglichen Urheberrechte, Patente, Geschäftsgeheimnisse, Marken und anderer Rechte zum Schutze geistigen Eigentums. 
Ihnen ist bekannt, dass der Besitz, die Installation oder die Benutzung der Software (des Quellcodes) keinerlei Ansprüche
auf das geistige Eigentum an der Software begründet, und dass Sie keinerlei Ansprüche an der Software ausser den in der Lizenzvereinbarung 
explizit eingeräumten erwerben. Sie stellt sicher, dass alle eventuell angefertigten Kopien der Software und der zugehörigen 
Dokumentation die entsprechenden Hinweise wie im Originalprodukt enthalten. 

Jede Art der Vervielfältigung, Bearbeitung, Verbreitung, Einspeicherung und jede
Art der Verwertung außerhalb der Grenzen des Urheberrechts bedarf der vorherigen
schriftlichen Zustimmung des Rechteinhabers.

Das geistige Eigentum, die Urheberrechte an dieser Software liegen bei dem Lizenzgeber:
Borucinski Grafix, Inhaber Konrad Borucinski, info@bogx.de, http://bogx.de

Das unerlaubte Kopieren/Speichern, Weitergeben oder Verkaufen dieser Software (dieses Quellcodes) und aller damit verbundenen Komponenten
(auch auszugsweise) ist nicht gestattet und strafbar. Ausgenommen davon sind alle Standard-Komponenten der Software-Umgebung, deren Urherberrechte
bei der Firma Shopware AG oder anderen Software-Herstellern liegen.

Der Lizenznehmer darf den Quellcode ausschliesslich für die eigenen Projektzwecke und nur innerhalb 
seines Unternehmens uneingeschränkt jedoch nicht exklusiv nutzen.

Die Gewährleistung ist in unseren AGB unter http://bogx.de/home/agb/ spezifiziert.
Wir weisen ausdrücklich darauf hin, dass die Gewährleistung mit sofortiger Wirkung entfällt, sobald der Lizenznehmer Änderungen oder
Erweiterungen an dem Quellcode selbst oder durch Dritte vornimmt.

Autor: Konrad Borucinski

 */

//use Shopware\Models\Emotion\Library\Component;


namespace BogxInstagramFeed;
 
use RuntimeException;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use BogxInstagramFeed\Bootstrap\EmotionElementInstaller;
 
class BogxInstagramFeed extends Plugin {


    /**
     * @param InstallContext $context
     * This method is called on plugin installation
     * @throws \Exception
     */
	public function install(InstallContext $context)
	{	
    /**
     * @return bool
     */
		if (!($context->assertMinimumVersion('5.2.7'))) {
			throw new RuntimeException('Sorry, das Plugin (Version 2.x) läuft erst ab Shopware 5.2.7');
		}

        $emotionElementInstaller = new EmotionElementInstaller(
            $this->getName(),
            $this->container->get('shopware.emotion_component_installer')
        );

        $emotionElementInstaller->install();
        return true;
	}

    /**
     * @param UninstallContext $uninstallContext
     */
	public function uninstall(UninstallContext $uninstallContext)
	{
		
		// Die vorhandenen Daten beim Plugin-Deinstallieren nicht löschen, wenn der User es nicht möchte
        // If the user wants to keep his data we will not delete it while uninstalling the plugin
        //if ($uninstallContext->keepUserData()) {
        //    return;
        //}

        // clear cache
        $uninstallContext->scheduleClearCache(UninstallContext::CACHE_LIST_ALL);
	}


    /**
     * @param ActivateContext $activateContext
     */
	public function activate(ActivateContext $activateContext)
	{
		// on plugin activation clear the cache
		$activateContext->scheduleClearCache(ActivateContext::CACHE_LIST_ALL);
	}

    /**
     * @param DeactivateContext $deactivateContext
     */
	public function deactivate(DeactivateContext $deactivateContext)
	{
        // on plugin deactivation clear the cache
        $deactivateContext->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);
	}
	
	
	/**
     * @param UpdateContext $context
     * This method is called on update of the plugin
     */
	public function update(UpdateContext $context)
	{
		if (!($context->assertMinimumVersion('5.2.7'))) {
			throw new RuntimeException('Sorry, das Plugin (Version 2.x) läuft erst ab Shopware 5.2.7');
		}
	
		return true;
		//return parent::update($context);
	}	

	
}