<?php

/**
 * Class Shopware_Controllers_Frontend_KssSliderArticlesDetail
 */
class Shopware_Controllers_Frontend_KssSliderArticlesDetail extends Enlight_Controller_Action {

    /**
     * get Order Number by Group
     */
    public function getOrderNumberByGroupAction() {

        $id = (int) $this->Request()->id;
        $selection = $this->Request()->getParam('selectgroup', []);
        $article = Shopware()->Modules()->Articles()->sGetArticleById(
            $id,
            null,
            null,
            $selection
        );

        $article['detailsArticle'] = $article;

        $this->View()->assign('sArticle', $article);
    }
}
