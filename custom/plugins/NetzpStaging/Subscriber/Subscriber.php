<?php namespace NetzpStaging\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use NetzpStaging\Component\Task;
use NetzpStaging\Component\WorkerTask;
use NetzpStaging\Component\FilesTask;
use NetzpStaging\Component\DatabaseTask;

class Subscriber implements \Enlight\Event\SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Inheritance_Template_Directories_Collected'
                => 'onCollectDirectories',

        	'Enlight_Controller_Action_PreDispatch_Frontend'
                => 'onPreDispatchFrontend',
	        'Enlight_Controller_Action_PostDispatch_Frontend'
                => 'onPostDispatchFrontend',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Index'
                => 'onPostDispatchBackend',

            'Shopware_CronJob_NetzpStagingCron'
                => 'onCronJob'
		];
    }

    public function onCollectDirectories(\Enlight_Event_EventArgs $args)
    {
        $directories = $args->getReturn();
        array_push($directories, __DIR__ . "/../Resources/views/");

        return $directories;
    }

    public function onPreDispatchFrontend(\Enlight_Controller_ActionEventArgs $args) {

        // don't block the cache cleaning from auth
        if($args->getRequest()->getControllerName() == 'netzpstaging' &&
           $args->getRequest()->getActionName() == 'clearcache') {
            return;
        }

        if(Shopware()->Container()->hasParameter('shopware.netzpstaging')) {
            $config = Shopware()->Container()->getParameter('shopware.netzpstaging');
            if($config['istestserver'] == '1' && 
               $config['authuser'] != '' && $config['authpassword'] != '') {
               $this->checkAuth($args->getRequest(), $config);
            }
        }
    }

    public function onPostDispatchFrontend(\Enlight_Controller_ActionEventArgs $args) {

        if(Shopware()->Container()->hasParameter('shopware.netzpstaging')) {
            $config = Shopware()->Container()->getParameter('shopware.netzpstaging');
            if($config['istestserver'] == '1') {
                $serverName = $config['servername'] . ' ' . 
                                ($config['anonymized'] == 1 ? '- anonymisiert' : '');
                $view = $args->getSubject()->View();
                $view->assign('netzpIsStagingServer', 1);
                $view->assign('netzpStagingServerName', $serverName);
            }
        }
    }

    public function onPostDispatchBackend(\Enlight_Controller_ActionEventArgs $args)
    {
        $view = $args->getSubject()->View();
        if(Shopware()->Container()->hasParameter('shopware.netzpstaging')) {
            $config = Shopware()->Container()->getParameter('shopware.netzpstaging');
            if($config['istestserver'] == '1') {
                $serverName = $config['servername'] . ' ' . 
                                ($config['anonymized'] == 1 ? '- anonymisiert' : '');
                $view->assign('netzpIsStagingServer', 1);
                $view->assign('netzpStagingServerName', $serverName);
            }
        }
        $view->extendsTemplate(__DIR__ . '/../Resources/views/backend/netzp_staging/index/index.tpl');
    }

	private function sendBasicAuth($serverName = '') {

        header('WWW-Authenticate: Basic realm="Testserver: ' . $serverName. '"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Sie haben falsche Zugangsdaten eingegeben.';
        exit;
    }

    private function checkAuth($request, $config) 
    {
        if($request->getServer('HTTP_AUTHORIZATION')) {
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = 
                explode(':' , base64_decode(substr($request->getServer('HTTP_AUTHORIZATION'), 6)));
        }
        $authUser = $request->getServer('PHP_AUTH_USER');
        if ( ! isset($_SERVER['PHP_AUTH_USER'])) {
            $this->sendBasicAuth($config['servername']);
        } 
        else {
            $authPass = $request->getServer('PHP_AUTH_PW');

            if($config['authuser'] != $authUser ||
               $config['authpassword'] != $authPass) {
                $this->sendBasicAuth($config['servername']);
            }
        }
    }

    public function onCronJob($job)
    {
        set_time_limit(0);

        $helper = Shopware()->Container()->get('netzp_staging.helper');
        $profiles = $helper->getProfiles(true);
        $liveDir = getcwd();

        foreach($profiles as $profile) {
            $helper->log($liveDir, $profile, 'CRON', [], true);
            $configLive = $helper->readConfigFile($liveDir);

            $filesTask = new FilesTask('files', $profile['id'], TASK::TASK_FILES);
            $filesTask->setParams($liveDir, $liveDir . '/' . $profile['dirname'], 
                                  $profile, $configLive);
            $filesTask->run();

            $databaseTask = new DatabaseTask('database', $profile['id'], TASK::TASK_DATABASE);
            $databaseTask->setAnonymize($profile['dbconfig']['anonymize'] == 1);
            $databaseTask->setParams($liveDir, $liveDir . '/' . $profile['dirname'], 
                                     $profile, $configLive);
            $databaseTask->run();

            $helper->log($liveDir, $profile, 'CRON: Fertig');
            sleep(5);
        }
    }
}