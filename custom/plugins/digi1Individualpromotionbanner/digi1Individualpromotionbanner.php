<?php
    namespace digi1Individualpromotionbanner;

    use Shopware\Components\Plugin\Context\ActivateContext;
    use Shopware\Components\Plugin\Context\DeactivateContext;
    use Shopware\Components\Plugin\Context\InstallContext;
    use Shopware\Components\Plugin\Context\UpdateContext;
    use Shopware\Components\Plugin\Context\UninstallContext;
    use Doctrine\ORM\Tools\SchemaTool;
    use digi1Individualpromotionbanner\Models\Promotionbanner;
    use Shopware\Components\Model\ModelManager;
    use Shopware\Bundle\CookieBundle\CookieCollection;
    use Shopware\Bundle\CookieBundle\Structs\CookieGroupStruct;
    use Shopware\Bundle\CookieBundle\Structs\CookieStruct;

    class digi1Individualpromotionbanner extends \Shopware\Components\Plugin {
        public function install(InstallContext $context){
            /* * @var ModelManager $entityManager */
            $entityManager = $this->container->get('models');

            $tool = new SchemaTool($entityManager);

            $classMetaData = [
                $entityManager->getClassMetadata(Promotionbanner::class)
            ];
            
            if (!$entityManager->getConnection()->getSchemaManager()->tablesExist(array('s_plugin_digi1_individualpromotionbanner'))) {
                 $tool->createSchema($classMetaData);
            }else{                
                $tool->updateSchema($classMetaData, true);
            }
        }

        public function activate(ActivateContext $context){
            $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
        }

        public function deactivate(DeactivateContext $context){

        }

        public function update(UpdateContext $context){
            $oldVersion = $context->getUpdateVersion();
            $currentVersion = $context->getCurrentVersion();

            if(version_compare($oldVersion,'2.0.0','<=')){

            }
        }

        public function uninstall(UninstallContext $context){
            if ($context->keepUserData()) {
                return;
            }
            
             /* * @var ModelManager $entityManager */
            $entityManager = $this->container->get('models');

            $tool = new SchemaTool($entityManager);

            $classMetaData = [
                $entityManager->getClassMetadata(Promotionbanner::class)
            ];

            $tool->dropSchema($classMetaData);
            
            $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
        }

        public static function getSubscribedEvents(){
            return [
                'Enlight_Controller_Action_PostDispatch_Frontend' => 'onPostDispatchFrontend',
                'Enlight_Controller_Action_PreDispatch' => 'onPreDispatch',
                'Enlight_Controller_Action_PreDispatch_Widgets' => 'onPreDispatch',
                'Enlight_Controller_Dispatcher_ControllerPath_Widgets_Promotionbanner' => 'onGetWidgetsControllerPathPromotionbanner',
                'Enlight_Controller_Dispatcher_ControllerPath_Backend_Promotionbanner' => 'onGetBackendControllerPathPromotionbanner',
                'Enlight_Controller_Action_PostDispatch_Backend' => 'onPostDispatchBackend',
                'CookieCollector_Collect_Cookies' => 'addComfortCookie'
            ];
        }
        
        public function onPostDispatchFrontend(\Enlight_Event_EventArgs $args){
            /* get the config parameters */
            $plugin_active = Shopware()->Config()->getByNamespace('digi1Individualpromotionbanner', 'plugin_active');

            if ($plugin_active == 1) {
                return;
            }

            $controller = $args->getSubject();

            $request = $controller->Request();

            $response = $controller->Response();

            $view = $controller->View();

            $this->container->get('Template')->addTemplateDir(
                $this->getPath() . '/Resources/views/responsive/'
            );
        }
        
        public function onPreDispatch(){
            /* get the config parameters */
            $plugin_active = Shopware()->Config()->getByNamespace('digi1Individualpromotionbanner', 'plugin_active');

            if($plugin_active == 1){
                return;
            }

            $this->container->get('Template')->addTemplateDir(
                $this->getPath() . '/Resources/views/responsive/'
            );
        }
        
        public function onGetWidgetsControllerPathPromotionbanner(){
            $this->container->get('Template')->addTemplateDir(
                $this->getPath() . '/Resources/views/responsive/'
            );
            
            return __DIR__ . '/Controllers/Widgets/Promotionbanner.php';
        }

        public function onGetBackendControllerPathPromotionbanner(){
            return __DIR__ . '/Controllers/Backend/Promotionbanner.php';
        }
               
        public function onPostDispatchBackend(\Enlight_Controller_ActionEventArgs $args){
            $request = $args->getRequest();
            $view = $args->getSubject()->View();

            $view->addTemplateDir($this->getPath() . '/Resources/views');  
        }
        
        public function addComfortCookie() {
            $promotionbanner_cookie_group = Shopware()->Config()->getByNamespace('digi1Individualpromotionbanner', 'promotionbanner_cookie_group');
            $pluginNamespace = $this->container->get('snippets')->getNamespace('frontend/cookieconsentmanager/promotionbanner/cookie');
            
            $collection = new CookieCollection();
            
            if($promotionbanner_cookie_group == 0){
                $collection->add(new CookieStruct(
                    'promotionbanner-',
                    '/^promotionbanner-/',       
                    $pluginNamespace->get('PromotionbannerCookieLabel'),
                    CookieGroupStruct::TECHNICAL
                ));
            }elseif($promotionbanner_cookie_group == 1){
                $collection->add(new CookieStruct(
                    'promotionbanner-',
                    '/^promotionbanner-/',       
                    $pluginNamespace->get('PromotionbannerCookieLabel'),
                    CookieGroupStruct::COMFORT
                ));
            }elseif($promotionbanner_cookie_group == 2){
                $collection->add(new CookieStruct(
                    'promotionbanner-',
                    '/^promotionbanner-/',       
                    $pluginNamespace->get('PromotionbannerCookieLabel'),
                    CookieGroupStruct::PERSONALIZATION
                ));
            }elseif($promotionbanner_cookie_group == 3){
                $collection->add(new CookieStruct(
                    'promotionbanner-',
                    '/^promotionbanner-/',       
                    $pluginNamespace->get('PromotionbannerCookieLabel'),
                    CookieGroupStruct::STATISTICS
                ));
            }elseif($promotionbanner_cookie_group == 4){
                $collection->add(new CookieStruct(
                    'promotionbanner-',
                    '/^promotionbanner-/',       
                    $pluginNamespace->get('PromotionbannerCookieLabel'),
                    CookieGroupStruct::OTHERS
                ));
            }

            return $collection;
        }
    }