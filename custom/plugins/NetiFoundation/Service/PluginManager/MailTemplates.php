<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Service\PluginManager;

use NetiFoundation\Service\Logging\LoggingServiceInterface;
use NetiFoundation\Struct\PluginConfigFile\MailTemplate;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Models\Mail\Mail;
use Shopware\Models\Order\Status;
use Shopware\Models\Shop\Locale;

/**
 * Class MailTemplates
 *
 * @package NetiFoundation\Service\PluginManager
 */
class MailTemplates implements MailTemplatesInterface
{
    /**
     * @var ModelManager
     */
    protected $em;

    /**
     * @var \Shopware_Components_Translation
     */
    protected $translation;

    /**
     * @var LoggingServiceInterface
     */
    protected $loggingService;

    /**
     * @param ModelManager                     $em
     * @param LoggingServiceInterface          $loggingService
     * @param \Shopware_Components_Translation $translation
     */
    public function __construct(
        ModelManager $em,
        LoggingServiceInterface $loggingService,
        \Shopware_Components_Translation $translation
    ) {
        $this->em             = $em;
        $this->loggingService = $loggingService;
        $this->translation    = $translation;
    }

    /**
     * @param Plugin $plugin
     * @param        $path
     */
    public function installMailTemplates(Plugin $plugin, $path)
    {
        $this->updateMailTemplates($plugin, $path);
    }

    /**
     * @param Plugin $plugin
     * @param string $path
     */
    public function updateMailTemplates(Plugin $plugin, $path)
    {
        $logs        = ['success' => true, 'templates' => []];

        $config = include $path . '/config.php';
        foreach ($config as $mailConfig) {
            $mailConfig = new MailTemplate($mailConfig);

            $template = $mailConfig->getTemplate();
            $locales  = $mailConfig->getLocales();
            $status   = $mailConfig->getStatus() ?
                $this->em->getRepository(Status::class)->find($mailConfig->getStatus())
                : null;

            if (! empty($locales)) {
                /**
                 * @var Mail $mailModel
                 */
                $mailModel = null;
                $localeRepository = $this->em->getRepository('Shopware\Models\Shop\Locale');
                foreach ($locales as $localData) {
                    $locale         = $localData->getLocale();
                    $subject        = $localData->getSubject();
                    $pathToPlainTpl = sprintf('%s/%s/%s/plain.tpl', $path, $template, $locale);
                    $pathToHtmlTpl  = sprintf('%s/%s/%s/html.tpl', $path, $template, $locale);
                    $plainContent   = '';
                    $htmlContent    = '';

                    $log = array(
                        'template' => $template,
                        'locale' => $locale,
                        'success' => false
                    );

                    if (is_file($pathToPlainTpl)) {
                        $plainContent = file_get_contents($pathToPlainTpl);
                    }

                    if (is_file($pathToHtmlTpl)) {
                        $htmlContent = file_get_contents($pathToHtmlTpl);
                    }

                    if (false === strpos($htmlContent, 'emailheaderhtml')) {
                        $htmlContent = "{include file=\"string:{config name=emailheaderhtml}\"}\n\n" . $htmlContent;
                    }
                    if (false === strpos($htmlContent, 'emailfooterhtml')) {
                        $htmlContent .= "\n\n{include file=\"string:{config name=emailfooterhtml}\"}";
                    }

                    if (false === strpos($plainContent, 'emailheaderplain')) {
                        $plainContent = "{include file=\"string:{config name=emailheaderplain}\"}\n\n" . $plainContent;
                    }
                    if (false === strpos($plainContent, 'emailfooterplain')) {
                        $plainContent .= "\n\n{include file=\"string:{config name=emailfooterplain}\"}";
                    }

                    if ('de_DE' === $locale) {
                        try {
                            $mailModel = $this->em->getRepository(Mail::class)->findOneBy([
                                'name' => $template
                            ]);

                            if (!$mailModel instanceof Mail) {
                                $mailModel = new Mail();
                            }

                            if (!$mailModel->isDirty()) {

                                $mailModel->setName($template)
                                          ->setMailtype($mailConfig->getMailtype())
                                          ->setSubject($subject)
                                          ->setContent($plainContent)
                                          ->setFromMail('{config name=mail}')
                                          ->setFromName('{config name=shopName}')
                                          ->setIsHtml($mailConfig->getHtml());

                                if (
                                    $status instanceof Status
                                    && !$status->getMail() instanceof Mail
                                ) {
                                    $mailModel->setStatus($status);
                                }

                                if (!empty($htmlContent)) {
                                    $mailModel->setContentHtml($htmlContent);
                                } else {
                                    $mailModel->setContentHtml('');
                                }

                                $this->em->persist($mailModel);
                                $this->em->flush($mailModel);
                            } else {
                                $log['message'] = 'Mail template is dirty and therefore not modified';
                            }
                            $log['success'] = true;
                        } catch (\Exception $e) {
                            $log['success'] = false;
                        }
                    } else {
                        try {
                            if ($mailModel instanceof Mail) {
                                if (!$mailModel->isDirty()) {
                                    $localeObject = $localeRepository->findOneBy(['locale' => $locale]);
                                    if ($localeObject instanceof Locale) {
                                        $localeID = $localeObject->getId();
                                        $this->translation->write(
                                            $localeID,
                                            'config_mails',
                                            $mailModel->getId(),
                                            [
                                                'subject'     => $subject,
                                                'content'     => $plainContent,
                                                'contentHtml' => $htmlContent,
                                            ]
                                        );
                                        $log['success'] = true;
                                    } else {
                                        $log['success'] = false;
                                        $log['message'] = 'Locale "' . $locale . '" not found';
                                    }
                                } else {
                                    $log['success'] = true;
                                    $log['message'] = 'Mail template is dirty and therefore not modified';
                                }
                            } else {
                                $log['success'] = false;
                                $log['message'] = 'Mail model not found';
                            }
                        } catch (\Exception $e) {
                            $log['success'] = false;
                            $log['message'] = $e->getMessage();
                        }
                    }
                    $logs['templates'][] = $log;
                }
            }
        }

        $this->loggingService->write(
            $plugin->getName(),
            __FUNCTION__,
            $logs['success'] ? 'Successful' : 'Error',
            ['mail' => $logs]
        );
    }

    /**
     * @param Plugin $plugin
     * @param string $path
     */
    public function removeMailTemplates(Plugin $plugin, $path)
    {
        // Todo: Implement logging... therefore the param $plugin is required
        
        $config = include $path . '/config.php';
        foreach ($config as $mailConfig) {
            $mailConfig = new MailTemplate($mailConfig);

            $template = $mailConfig->getTemplate();
            $locales  = $mailConfig->getLocales();
            if (! empty($locales)) {
                foreach ($locales as $localData) {
                    /**
                     * @var Mail $mailModel
                     */
                    $locale    = $localData->getLocale();
                    $mailModel = $this->em->getRepository('Shopware\Models\Mail\Mail')->findOneBy(array(
                        'name' => $template
                    ));

                    if ($mailModel instanceof Mail) {
                        $this->translation->delete(
                            $locale,
                            'config_mails',
                            $mailModel->getId()
                        );

                        $this->em->remove($mailModel);
                        $this->em->flush();
                    }
                }
            }
        }
    }
}
