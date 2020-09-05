<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

use Enlight_Exception as Exception;

/**
 * Class Shopware_Controllers_Backend_Foundation
 */
class Shopware_Controllers_Backend_Foundation extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * Send the support mail
     */
    public function sendSupportMailAction()
    {
        $tm = $this->container->get('template');
        // clone the template manager, so that the assigned values don't affect the current backend view
        $tmMail = clone $tm;
        $em = $this->container->get('models');

        $mailPath  = __DIR__ . '/../../Views/email/';

        $mailTpl  = array(
            'user' => $mailPath . 'support_user.tpl',
            'neti' => $mailPath . 'support_neti.tpl',
        );

        // Throw error if user email address is invalid
        if (! filter_var($this->Request()->getParam('email'), FILTER_VALIDATE_EMAIL)) {
            $error = '{s namespace="backend/NetiFoundation/support" name="error_invalid_email"}Bitte geben Sie ' .
                'eine gültige Email-Adresse an.{/s}';
            $error = $tm->fetch('snippet:string:' . $error);
            $this->View()->assign(array('success' => false, 'message' => $error));

            return;
        }

        $tmMail->caching = false;

        // get plugin information
        /** @var \Shopware\Models\Plugin\Plugin $plugin */
        $plugin = $em->getRepository('Shopware\Models\Plugin\Plugin')
            ->findOneBy(array('name' => $this->Request()->getParam('plugin')));

        // set values
        $tmMail->assign('product', $plugin->getName());
        $tmMail->assign('name', $this->Request()->getParam('name'));
        $tmMail->assign('company', $this->Request()->getParam('company'));
        $tmMail->assign('email', $this->Request()->getParam('email'));
        $tmMail->assign('tel', $this->Request()->getParam('tel'));
        $tmMail->assign('subject', $this->Request()->getParam('subject'));
        $tmMail->assign('message', $this->Request()->getParam('message'));
        $tmMail->assign('messagePlain', str_replace('<br>', "\n", $this->Request()->getParam('message')));
        $tmMail->assign('type', $this->Request()->getParam('type'));
        $tmMail->assign('time', date('d.m.Y - H:i', time()));
        $tmMail->assign('shopUrl', $this->Request()->getScheme() . '://' . $this->Request()->getHttpHost());
        $tmMail->assign('pluginVersion', $plugin->getVersion());
        $tmMail->assign('shopwareVersion', $this->container->get('config')->get('version'));
        $tmMail->assign('phpVersion', PHP_VERSION);
        $tmMail->assign('serverSoftware', $_SERVER['SERVER_SOFTWARE']);
        $tmMail->assign('browser', $_SERVER['HTTP_USER_AGENT']);

        // assign shops and their templates to the mail #18515
        $shops = $this->getShopTemplates();
        $tmMail->assign('shops', $shops);

        // Only mail if at least one of the neti-mail templates is there
        if (! file_exists($mailTpl['neti'])) {
            $error = '{s namespace="backend/NetiFoundation/support" name="error_no_tpl"}' .
                'Mailtemplates konnten nicht gefunden werden!<br />Bitte nehmen Sie über support@netinventors.de ' .
                'mit uns Kontakt auf.{/s}';
            $error = $tm->fetch('snippet:string:' . $error);
            $this->View()->assign(array('success' => false, 'message' => $error));

            return;
        }

        /*** create basic mail ***/
        $mailUser = $this->container->get('mail');
        $mailUser->setFrom($this->container->get('config')->get('Mail'));

        /*** clone, modify and send Neti Mail ***/
        // only run if template exists
        if (file_exists($mailTpl['neti'])) {
            $mailNeti = clone $mailUser;
            $mailNeti->IsHTML(true);
            $mailNeti->addTo('support@netinventors.de');
            if ($this->Request()->getParam('email')) {
                $mailNeti->setReplyTo($this->Request()->getParam('email'));
            }
            $mailNeti->setSubject($plugin->getName() . ' - ' . $this->Request()->getParam('type') . ' von ' .
                $this->Request()->getParam('name') . ' (' . $this->Request()->getParam('company') . ')');
            $mailNeti->setBodyHtml($tmMail->fetch($mailTpl['neti']));
            try {
                $mailNeti->send();
            } catch (Exception $xcpt) {
                $this->View()->assign(array('success' => false, 'message' => $xcpt->getMessage()));
            }
        }

        $this->View()->assign(array('success' => true));
    }

    /**
     * Returns a list of article variants.
     * Inspired by Shopware_Controllers_Backend_Base::getArticlesAction()
     */
    public function getVariantsAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        //load shop repository
        $repository = $this->container->get('models')->getRepository('Shopware\Models\Article\Article');

        $builder = $repository->createQueryBuilder('articles');

        $fields = array(
            'id' => 'details.id',
            'name' => 'articles.name',
            'number' => 'details.number',
            'detailId' => 'details.id as detailId'
        );
        $builder->select($fields);

        $builder->leftJoin('articles.details', 'details');

        $filters = $this->Request()->getParam('filter', array());
        foreach ($filters as $filter) {
            if ($filter['property'] === 'free') {
                $builder->andWhere(
                    $builder->expr()->orX(
                        'details.number LIKE :free',
                        'articles.name LIKE :free'
                    )
                );
                $builder->setParameter(':free', $filter['value']);
            } else {
                $repository->addFilter($builder, $filter);
            }
        }

        $repository->addOrderBy($builder, $this->prepareParam($this->Request()->getParam('sort', array()), $fields));

        $builder->setFirstResult($this->Request()->getParam('start'))
            ->setMaxResults($this->Request()->getParam('limit'));

        $query = $builder->getQuery();

        //get total result of the query
        $total = $this->container->get('models')->getQueryCount($query);

        //select all shop as array
        $data = $query->getArrayResult();

        //return the data and total count
        $this->View()->assign(array('success' => true, 'data' => $data, 'total' => $total));
    }

    /**
     * Add the table alias to the passed filter and sort parameters.
     * @param $properties
     * @param $fields
     * @return array|mixed
     */
    private function prepareParam($properties, $fields)
    {
        foreach ($properties as $key => $property) {
            if (array_key_exists($property['property'], $fields)) {
                $property['property'] = $fields[$property['property']];
            }
            $properties[$key] = $property;
        }
        return $properties;
    }

    /**
     * delivers all shops and their templates
     *
     * @return array
     */
    public function getShopTemplates()
    {
        /** @var \Shopware\Models\Shop\Shop[] $shopObjects */
        $shopObjects = $this->container->get('models')->getRepository('Shopware\Models\Shop\Shop')->findAll();
        $shops = array();
        foreach ($shopObjects as $shopObject) {
            $templateObject = $this->getShopTemplate($shopObject);
            $shops[$shopObject->getId()] = array(
                'name' => $shopObject->getName(),
                'template' => array(
                    'name' => $templateObject ? $templateObject->getName() : 'n/a',
                    'version' => $templateObject ? $templateObject->getVersion() : 'n/a'
                )
            );
        }
        return $shops;
    }

    /**
     * @param \Shopware\Models\Shop\Shop $shop
     *
     * @return \Shopware\Models\Shop\Template|bool
     */
    protected function getShopTemplate($shop)
    {
        if (! $shop->getTemplate()) {
            if (! $shop->getMain()) {
                return false;
            } else {
                return $this->getShopTemplate($shop->getMain());
            }
        } else {
            return $shop->getTemplate();
        }
    }
}

