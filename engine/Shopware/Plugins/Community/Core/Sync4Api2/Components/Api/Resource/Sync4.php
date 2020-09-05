<?php

/**
 * sync4 REST API Extensions
 * @copyright Copyright (c) 2014, Dupp GmbH (http://www.dupp.de)
 * @author Jan Eichmann
 * @author Mario Schwarz
 */

namespace Shopware\Components\Api\Resource;

use Shopware\Components\Api\Exception as ApiException;
use Shopware\Models\Article\Article;
use Shopware\Models\Article\Detail;

/**
 * Sync4 API Resource
 */
class Sync4 extends Resource {

    /**
     * @var array Parameters
     */
    protected $_params;

    /**
     * @var array POST data
     */
    protected $_data;

    /**
     * Set an array of parameters
     * @param array $value
     * @return \Shopware\Components\Api\Resource\Sync4
     */
    public function setParams($value) {
        $this->_params = $value;
        return $this;
    }

    /**
     * Set the post data
     * @param type $value
     * @return \Shopware\Components\Api\Resource\Sync4
     */
    public function setData($value) {
        $this->_data = $value;
        return $this;
    }

    /**
     * Get the post data
     * @param type $value
     * @return \Shopware\Components\Api\Resource\Sync4
     */
    public function getData() {
        return $this->_data;
    }

    /**
     * Retrieve a parameter
     * @param mixed $key
     * @param mixed $default Default value to use if key not found
     * @return mixed
     */
    protected function getParam($key, $default = null) {
        $key = (string) $key;
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        }

        return $default;
    }

    /**
     * Retrieve an array of status information.
     * @return array
     */
    public function getStatus() {
        return array('success' => true, 'message' => 'sync4 REST API Extension is running');
    }

    /**
     * Retrieve an array of shops.
     * @return array
     */
    public function getShops() {
        $this->checkPrivilege('read');

        $limit = $this->getParam('limit', 1000);
        $offset = $this->getParam('start', 0);
        $sort = $this->getParam('sort', array());
        $filter = $this->getParam('filter', array());


        $builder = Shopware()->Models()->createQueryBuilder()
                ->select('shop', 'category')
                ->from('Shopware\Models\Shop\Shop', 'shop')
                ->leftJoin('shop.category', 'category');

        $builder->addFilter($filter);
        $builder->addOrderBy($sort);
        $builder->setFirstResult($offset)
                ->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->getResultMode());

        $paginator = Shopware()->Models()->createPaginator($query);

        $totalResult = $paginator->count();
        $countries = $paginator->getIterator()->getArrayCopy();

        return array('success' => true, 'data' => $countries, 'total' => $totalResult);
    }

    /**
     * Retrieve an array of countries.
     * @return array
     */
    public function getCountries() {
        $this->checkPrivilege('read');

        $limit = $this->getParam('limit', 1000);
        $offset = $this->getParam('start', 0);
        $sort = $this->getParam('sort', array());
        $filter = $this->getParam('filter', array());

        $repository = $this->getManager()->getRepository('Shopware\Models\Country\Country');
        $builder = $repository->createQueryBuilder('Country');

        $builder->addFilter($filter);
        $builder->addOrderBy($sort);
        $builder->setFirstResult($offset)
                ->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->getResultMode());

        $paginator = Shopware()->Models()->createPaginator($query);

        $totalResult = $paginator->count();
        $countries = $paginator->getIterator()->getArrayCopy();

        return array('success' => true, 'data' => $countries, 'total' => $totalResult);
    }

    /**
     * Retrieve an array of country areas.
     * @return array
     */
    public function getAreas() {
        $this->checkPrivilege('read');

        $limit = $this->getParam('limit', 1000);
        $offset = $this->getParam('start', 0);
        $sort = $this->getParam('sort', array());
        $filter = $this->getParam('filter', array());

        $repository = $this->getManager()->getRepository('Shopware\Models\Country\Area');
        $builder = $repository->createQueryBuilder('Area');

        $builder->addFilter($filter);
        $builder->addOrderBy($sort);
        $builder->setFirstResult($offset)
                ->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->getResultMode());

        $paginator = Shopware()->Models()->createPaginator($query);

        $totalResult = $paginator->count();
        $areas = $paginator->getIterator()->getArrayCopy();

        return array('success' => true, 'data' => $areas, 'total' => $totalResult);
    }

		 /**
     * Return Variant ID by name
     * @param type $name
		 * @param type $article
     * @return integer
     */
    private function getVariantId($name, $article) {
        foreach ($article["details"] as $variant) {
            if ($variant["number"] == $name) {
                return $variant["id"];
            }
        }
        return 0;
    }


		/**
     * Update the variant translations
     * @return array
     */
    public function updateVariantTranslation() {
        $this->checkPrivilege('update');
        $id = $this->getParam('articleId', null);
        $data = $this->getData();

        if (empty($id)) {
            throw new ApiException\ParameterMissingException("articleId");
        }

        if (empty($data)) {
            throw new ApiException\ParameterMissingException('data');
        }

        $articleResource = \Shopware\Components\Api\Manager::getResource('article');

        $article = $articleResource->getOne($id);

        $translationResource = \Shopware\Components\Api\Manager::getResource('translation');
        if ($data["translations"]["groups"]) {
            foreach ($data["translations"]["groups"] as $groups) {
                foreach ($groups as $group) {
                    $params = array();
                    $params["key"] = $group["name"];
                    $params["shopId"] = $group["shopId"];
                    $params["type"] = "configuratorgroup";
                    $params["data"] = array();
                    $params["data"]["name"] = $group["translation"];
                    $translationResource->createByNumber($params);
                    if ($group["options"]) {
                        foreach ($group["options"] as $option) {
                                $params = array();
                                $params["key"] = $group["name"] . '|' . $option["key"];
                                $params["shopId"] = $option["shopId"];
                                $params["type"] = "configuratoroption";
                                $params["data"] = array();
                                $params["data"]["name"] = $option["name"];
                                $translationResource->createByNumber($params);
                        }
                    }
                }
            }
        }


        if ($data["translations"]["variants"]) {
            foreach ($data["translations"]["variants"] as $variant) {
                    $params = array();
                    $key = $this->getVariantId($variant["key"], $article);
                    if ($key > 0 || $article["mainDetail"]["number"] == $variant["key"]) {
                        $params["key"] = $article["mainDetail"]["number"] == $variant["key"] ? $id:$key;
                        $params["shopId"] = $variant["shopId"];
                        $params["type"] = $article["mainDetail"]["number"] == $variant["key"] ? "article":"variant";
                        $params["data"] = array();
                        $params["data"]["additionalText"] = $variant["additionalText"];
						
						if ( array_key_exists("translation", $variant) &&
								is_array($variant["translation"]) )
						{
							foreach ($variant["translation"] as $translationKey => $translationValue)
							{
								$params["data"]["".$translationKey] = $translationValue;
							}
						}
						
                        $translationResource->create($params);
                    }

            }
        }

        return array('success' => true);
    }


		/**
     * Returns an Order with additional information
     * @return array
     */
    public function getOrder() {
        $this->checkPrivilege('read');

        $id = $this->getParam('ordersId');
        $useNumberAsId = (boolean) $this->getParam('useNumberAsId', 0);
        $resource = \Shopware\Components\Api\Manager::getResource('order');
        if ($useNumberAsId) {
            $order = $resource->getOneByNumber($id);
        } else {
            $order = $resource->getOne($id);
        }

        $repository = $this->getManager()->getRepository('Shopware\Models\Article\Article');

        $details = array();
        foreach ($order["details"] as $orderDetail) {
            $taxrate = $orderDetail["taxRate"];
            $taxid = $orderDetail["taxId"];
            $billing = $order["billing"];
            $sql = "SELECT id FROM s_core_tax_rules WHERE groupID = '" . $taxid . "' AND tax = '" . $taxrate . "' AND CountryID = '" . $billing["countryId"] . "' Limit 1";
            $erg = Shopware()->Db()->fetchRow($sql);
            if ($erg){
                $orderDetail["tax_shopID"] = $erg["id"];
            }else{
                 $orderDetail["tax_shopID"] = null;
            }
            $article = $repository->findOneById($orderDetail["articleId"]);

            if ($article  && $orderDetail["mode"] == '0') {
                $mainDetail = $article->getMainDetail();
                if ($mainDetail->getNumber() != $orderDetail["articleNumber"]) {
                    $variantResource = \Shopware\Components\Api\Manager::getResource('variant');
                    $variant = $variantResource->getOneByNumber($orderDetail["articleNumber"]);
                    $orderDetail["isVariant"] = true;
                    $orderDetail["variantMainArticleNumber"] = $mainDetail->getNumber();
                    $options = array();

                    $grouprepository = $this->getManager()->getRepository('Shopware\Models\Article\Configurator\Group');
                    foreach ($variant["configuratorOptions"] as $option) {
                        $group = $grouprepository->findOneById($option["groupId"]);
                        if ($group) {
                            $optionvalue = array("groupName" => $group->getName(), "optionName" => $option["name"]);
                            array_push($options, $optionvalue);
                        }
                    }
                    $orderDetail["variantOptions"] = $options;
                } else {
                    $orderDetail["isVariant"] = false;
                }
            } else {
                $orderDetail["isVariant"] = false;
            }

            array_push($details, $orderDetail);
        }

        $order["details"] = $details;
        return array('success' => true, 'data' => $order);
    }

    /**
     * Delete all products.
     * @return array
     */
    public function deleteAllProducts() {
        set_time_limit(9999);

        $this->checkPrivilege('delete');

        $deleteUnsafe = $this->getParam('unsafe', null);

        if (!isset($deleteUnsafe)) {
            $builder = Shopware()->Models()->createQueryBuilder();
            $builder->select('d')
                    ->from('Shopware\Models\Article\Detail', 'd');

            $query = $builder->getQuery();
            $articles = $query->getResult();

            if (count($articles) > 0) {
                foreach ($articles as $articleDetail) {

                    if (is_object($articleDetail)) {
                        if ($articleDetail->getKind() != 1) {
                            Shopware()->Models()->remove($articleDetail);
							Shopware()->Models()->flush();
						}
                    }
                }
			}

			 $builder = Shopware()->Models()->createQueryBuilder();
            $builder->select('d')
                    ->from('Shopware\Models\Article\Article', 'd');

            $query = $builder->getQuery();
            $articles = $query->getResult();

            if (count($articles) > 0) {
                foreach ($articles as $article) {

                    if (is_object($article)) {
                            Shopware()->Models()->remove($article);
							Shopware()->Models()->flush();
                        }

                    }
				}

        } else {
            $this->unsafeDeleteProducts();
        }

        return array('success' => true);
    }

    /**
     * Delete a specific product detail.
     * @return array
     */
    public function deleteProductDetail() {
        set_time_limit(9999);

        $this->checkPrivilege('delete','read');

        $detailId = $this->getParam('Id', null);
        $repository = $this->getManager()->getRepository('Shopware\Models\Article\Detail');
		    $articleDetail = $repository->findOneById($detailId);
	    	if ($articleDetail){
			    Shopware()->Models()->remove($articleDetail);
			    Shopware()->Models()->flush();
			    return array('success' => true);
		    }
		
        return array('success' => false);
    }


		/**
     * Delete all products.
     */
    private function unsafeDeleteProducts() {
        Shopware()->Models()->createQueryBuilder()->delete('Shopware\Models\Article\Price', 'price')->getQuery()->execute();
        Shopware()->Models()->createQueryBuilder()->delete('Shopware\Models\Article\Esd', 'esd')->getQuery()->execute();
        Shopware()->Models()->createQueryBuilder()->delete('Shopware\Models\Attribute\Article', 'attribute')->getQuery()->execute();
        Shopware()->Models()->createQueryBuilder()->delete('Shopware\Models\Article\Image', 'image')->getQuery()->execute();
        Shopware()->Db()->query("DELETE FROM s_article_configurator_option_relations");
        Shopware()->Db()->query("DELETE FROM s_articles_translations");
        Shopware()->Db()->query("DELETE FROM s_core_translations where objecttype = 'variant'");
        Shopware()->Models()->createQueryBuilder()->delete('Shopware\Models\Article\Detail', 'detail')->getQuery()->execute();
        Shopware()->Models()->createQueryBuilder()->delete('Shopware\Models\Article\Article', 'article')->getQuery()->execute();
    }


		/**
     * Delete all category assignments in a category
     */
    public function deleteCategoryAssignments() {
        $this->checkPrivilege('delete');

        $id = $this->getParam('categoryId', null);
        if (empty($id)) {
            throw new ApiException\ParameterMissingException("categoryId");
        }

        $repository = $this->getManager()->getRepository('Shopware\Models\Category\Category');
        $category = $repository->findOneById($id);


        if (!$category) {
            throw new ApiException\NotFoundException("Category by id $id not found");
        }

        $articles = $category->getArticles();
        foreach ($articles as $article) {
            $article->removeCategory($category);
        }

        $category->setArticles(null);

        $this->flush();

        return array('success' => true, 'data' => $category->getId());
    }

 

    /**
     * Delete all categories.
     * @return array
     */
public function deleteAllCategories() {
        set_time_limit(9999);

        $this->checkPrivilege('delete');

		$shopsBuilder = Shopware()->Models()->createQueryBuilder();
        $shopsBuilder->select('shop')
					 ->from('Shopware\Models\Shop\Shop', 'shop')
					 ->where($shopsBuilder->expr()->isNotNull('shop.category'));
					 //->leftJoin('shop.category', 'category');




		$builder = Shopware()->Models()->createQueryBuilder();
		$builder->select('category')
				->from('Shopware\Models\Category\Category', 'category')
				->where($builder->expr()->andX($builder->expr()->isNotNull('category.parent'), $builder->expr()->isNotNull('category.path')))
				->orWhere($builder->expr()->andX($builder->expr()->isNotNull('category.position'), $builder->expr()->isNotNull('category.path')))
				->andWhere($builder->expr()->gt('category.parent', 1))
				->andWhere($builder->expr()->notIn('category', $shopsBuilder->getDQL()));


        $query = $builder->getQuery();
        $categories = $query->getResult();

        foreach ($categories as $category) {
            $this->getManager()->remove($category);
        }


        $this->getManager()->flush();

        return array('success' => true);
    }

    /**
     * Delete all empty categories.
     * @return array
     * @throws Exception
     */
    public function deleteEmptyCategories() {
        set_time_limit(9999);

        $this->checkPrivilege('delete');

        $shopsBuilder = Shopware()->Models()->createQueryBuilder()
                ->select('category')
                ->from('Shopware\Models\Shop\Shop', 'shop')
                ->leftJoin('shop.category', 'category');


        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select('c')
                ->from('Shopware\Models\Category\Category', 'c')
                ->leftJoin('c.articles', 'articles')
                ->where($builder->expr()->notIn('c', $shopsBuilder->getDQL()))
                ->andWhere($builder->expr()->isNotNull('c.parentId'));

        $query = $builder->getQuery();
        $categories = $query->getResult();

        $count = 0;
        $deletedCategories = "";
        foreach ($categories as $category) {
            if ($category->getArticles()->count() == 0 && $category->getChildren()->count() == 0) {
                $deletedCategories .= $category->getId() . ";";
                $this->getManager()->remove($category);
                $count++;
            }
        }

        $this->getManager()->flush();
        return array('success' => true, 'count' => $count, 'data' => $deletedCategories);
    }

    /**
     * Retrieve an array of Suppliers.
     * @return array
     */
    public function getManufacturers() {
        $this->checkPrivilege('read');

        $limit = $this->getParam('limit', 1000);
        $offset = $this->getParam('start', 0);
        $sort = $this->getParam('sort', array());
        $filter = $this->getParam('filter', array());

        $repository = $this->getManager()->getRepository('Shopware\Models\Article\Supplier');
        $builder = $repository->createQueryBuilder('Supplier');

        $builder->addFilter($filter);
        $builder->addOrderBy($sort);
        $builder->setFirstResult($offset)
                ->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->getResultMode());

        $paginator = Shopware()->Models()->createPaginator($query);

        $totalResult = $paginator->count();
        $manufacturers = $paginator->getIterator()->getArrayCopy();

        return array('success' => true, 'data' => $manufacturers, 'total' => $totalResult);
    }

    /**
		 * Create a new manufacturer.
     * @param array $params
     * @return array
     * @throws \Shopware\Components\Api\Exception\ValidationException
     */
    public function createManufacturer() {
        $this->checkPrivilege('create');

        $data = $this->getData();

        $supplier = new \Shopware\Models\Article\Supplier();

        $supplier->fromArray($data);

        $violations = $this->getManager()->validate($supplier);
        if ($violations->count() > 0) {
            throw new ApiException\ValidationException($violations);
        }

        $this->getManager()->persist($supplier);
        $this->flush();



        return array('success' => true, 'data' => $supplier->getId());
    }

    /**
		 * Update an existing manufacturer
     * @param int $id
     * @param array $params
     * @return Supplier ID
     * @throws \Shopware\Components\Api\Exception\ValidationException
     * @throws \Shopware\Components\Api\Exception\NotFoundException
     * @throws \Shopware\Components\Api\Exception\ParameterMissingException
     */
    public function updateManufacturer() {
        $this->checkPrivilege('create');
        $id = $this->getParam('supid', null);
        $data = $this->getData();
        if (empty($id)) {
            throw new ApiException\ParameterMissingException();
        }


        $repository = $this->getManager()->getRepository('Shopware\Models\Article\Supplier');
        $supplier = $repository->findOneById($id);


        if (!$supplier) {
            throw new ApiException\NotFoundException("Supplier by id $id not found");
        }

        $supplier->fromArray($data);
        $violations = $this->getManager()->validate($supplier);
        if ($violations->count() > 0) {
            throw new ApiException\ValidationException($violations);
        }

        $this->flush();

        return array('success' => true, 'data' => $supplier->getId());
    }

    /**
		 * Delete manufacturer by ID
     * @param int $id
     * @throws \Shopware\Components\Api\Exception\NotFoundException
     * @throws \Shopware\Components\Api\Exception\ParameterMissingException
     */
    public function deleteManufacturer() {
        $this->checkPrivilege('delete');
        $id = $this->getParam('supid', null);
        if (empty($id)) {
            throw new ApiException\ParameterMissingException();
        }


        $repository = $this->getManager()->getRepository('Shopware\Models\Article\Supplier');
        $supplier = $repository->findOneById($id);


        if (!$supplier) {
            throw new ApiException\NotFoundException("Supplier by id $id not found");
        }

        $this->getManager()->Remove($supplier);
        $this->getmanager()->flush();

        return array('success' => true);
    }

	  /**
     * Delete all Files in a Album
      * @return array
     */
    public function deleteAllFilesInAlbum() {
        $this->checkPrivilege('delete');
        $data = $this->getData();

        $this->resource = \Shopware\Components\Api\Manager::getResource('media');

        if (empty($data)) {
            throw new Exception\ParameterMissingException();
        }
        $parentAlbum = $this->returnAlbum($data["parentalbum"], 0);

        $albumList = $data["albumlist"];
        foreach ($albumList as $albumToClear) {
            $album = $this->returnAlbum($albumToClear, $parentAlbum);
            if ($album) {
                $mediaToDelete = $album->getMedia();
                foreach ($mediaToDelete as $media) {
                    $this->resource->delete($media->getId());
                }
            }
        }
        return array('success' => true);
    }

		/**
     * Upload a media File.
     * @return array
     */
    public function uploadFile() {
        $this->checkPrivilege('create');
        $mediaResource = \Shopware\Components\Api\Manager::getResource('Media');
        $data = $this->getData();
        if (empty($data)) {
            throw new ApiException\ParameterMissingException();
        }

        $destPath = Shopware()->DocPath('media_' . 'temp');
        if (!is_dir($destPath)) {
            mkdir($destPath, 0777, true);
        }
        $destPath = realpath($destPath);

        if (!file_exists($destPath)) {
            throw new \InvalidArgumentException(
            sprintf("Destination directory '%s' does not exist.", $destPath)
            );
        } elseif (!is_writable($destPath)) {
            throw new \InvalidArgumentException(
            sprintf("Destination directory '%s' does not have write permissions.", $destPath)
            );
        }

        $decodedfile = base64_decode(chunk_split($data["image"]));
        file_put_contents($destPath . "/" . $data["filename"], $decodedfile);

        $parent = $this->returnAlbum($data["parentalbum"], 0);
        if (!$parent) {
            $parent = $this->createNewAlbum($data["parentalbum"], 0, 0, "");
        }


        $album = $this->returnAlbum($data["album"], $parent);
        if (!$album) {
            $album = $this->createNewAlbum($data["album"], $parent, $data["createthumbnails"], $data["thumbnailsize"]);
        }

        $params = array(
            'album' => $album,
            'description' => $data["filename"],
            'file' => $destPath . "/" . $data["filename"],            
            'extension' => $data["extension"]
        );

        $shopID = $data["shopID"];
        if ($shopID != "")
			    $mediaResource->delete($shopID); //Make sure the picture is rewritten (otherwise changes won't be made)

        $mediamodel = $mediaResource->create($params);
        if ($mediamodel->getType() == "IMAGE") {
            $manager = $this->getContainer()->get('thumbnail_manager');
            $manager->createMediaThumbnail($mediamodel, array(), true);
        }
        return array('success' => true, 'data' => $mediamodel->getId());
    }


		/**
     * Return a Album by Name and parentID
     * @param type $value
     * @return Shopware\Models\Media\Album
     */
    private function returnAlbum($name, $parentId) {
        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select('a')
                ->from('Shopware\Models\Media\Album', 'a')
                ->where('a.name = :myname')
                ->andWhere('ifnull(a.parentId,0) = :myparentID')
                ->setParameter('myparentID', $parentId)
                ->setParameter('myname', $name);

        $query = $builder->getQuery();
        $album = $query->getOneOrNullResult();
        $this->getManager()->flush();
        if ($album) {
            return $album;
        }
        return null;
    }


		/**
     * Create a new media album.
     * @return Shopware\Models\Media\Album
     */
    private function createNewAlbum($name, $parent, $createThumbnails, $thumbnailSize) {

        $album = new \Shopware\Models\Media\Album();
        $settings = new \Shopware\Models\Media\Settings();
        $settings->setCreateThumbnails($createThumbnails);
        $settings->setIcon('sprite-blue-folder');
        $settings->setThumbnailSize($thumbnailSize);
		$settings->setThumbnailQuality(100);

        $album->setName($name);
        $album->setPosition(1);
        if ($parent) {
            $album->setParent($parent);
        }


        $this->getManager()->persist($album);
        $this->getManager()->flush();


        $settings->setAlbum($album);
        $this->getManager()->persist($settings);
        $this->getManager()->flush();

        $album->setSettings($settings);

        return $album;
    }


		/**
     * Return all Products with attributes
     * @return array
     */
		public function getAttributProducts()
    {
       $this->checkPrivilege('read');
       $sql = " select a.ordernumber from s_articles_details a ";
       $sql = $sql." inner join s_articles_similar b on a.articleid = b.articleid ";
       $sql = $sql." group by a.ordernumber ";
       $erg = Shopware()->Db()->fetchAll($sql);
       return array('success' => true, 'data' => $erg);
    }


		/**
     * assign a media object to a cetagory
     */
    public function setCategorieMedia() {
        $this->checkPrivilege('create');
        $repositoryMedia = $this->getManager()->getRepository('Shopware\Models\Media\Media');
        $data = $this->getData();
        if (empty($data)) {
            throw new ApiException\ParameterMissingException();
        }

        $media = $repositoryMedia->findOneById($data["MediaId"]);

        foreach ($data["categories"] as $categorie) {
            $repositoryCategorie = $this->getManager()->getRepository('Shopware\Models\Category\Category');
            $categorieObject = $repositoryCategorie->findOneById($categorie["id"]);
            $categorieObject->setMedia($media);
            $this->getManager()->validate($categorie);
            $this->getManager()->flush();
        }
    }

		/**
	* Deletes all cross selling products of a product
	* @return array
	*/
	public function deleteProductsXSell() {
		$this->checkPrivilege('delete');
		$data = $this->getData();
		$parent_model = $data["id"];
		$api = Shopware()->Api();
		$import = &$api->import->shopware;

		$erg = $import->sDeleteArticleSimilar(array("articleID"=>$parent_model));
		return array('success' => $erg, 'data' => '');
	}

    /**
	* Gets taxzones
	* @return array
	*/
    public function getTaxRates(){
        $this->checkPrivilege('read');
        $sql = "SELECT IFNULL(s_core_tax_rules.ID,s_core_tax.ID) AS ShopID , s_core_tax_rules.countryid as CountryID,";
        $sql .= "s_core_tax.ID as tax_class_id, iFNull(s_core_tax_rules.Tax,s_core_tax.tax) as tax_rate , IFNULL(s_core_tax_rules.Name,s_core_tax.description) as Description, IFNULL(s_core_tax_rules.active,'1') as active ";
        $sql .= "FROM s_core_tax JOIN s_core_tax_rules ON s_core_tax_rules.groupid = s_core_tax.ID";
        $erg = Shopware()->Db()->fetchAll($sql);
        return array('success' => true, 'data' => $erg);
    }

    /**
	* Adds esd articles
	* @return array
	*/
    public function addESD(){
      $this->checkPrivilege('create');
      $data = $this->getData();
      $sql = "SELECT Count(*) as anzahl FROM s_articles_esd WHERE articleID = '".$data["articleId"]."' And articledetailsID = '".$data["articledetailsID"]."'";
      $erg = Shopware()->Db()->fetchRow($sql);

      if ($erg["anzahl"] == "0"){
            $sql = "INSERT INTO s_articles_esd (articleID,articledetailsID,serials,notification,maxdownloads,datum ) VALUES ('" . $data["articleId"] . "','" . $data["articledetailsID"] . "','0','0','0',Now())";
            $erg = Shopware()->DB()->query($sql);
            return array('success' => $erg, 'data' => $erg);
      }
      return array('success' => $erg, 'data' => $erg);
    }

	public function deleteESD(){
      $this->checkPrivilege('delete');
      $data = $this->getData();
     
	  $sql = "DELETE FROM s_articles_esd WHERE articleID = '".$data["articleId"]."' And articledetailsID = '".$data["articledetailsID"]."'";
	  $erg = Shopware()->DB()->query($sql);
	  return array('success' => $erg, 'data' => $erg);    
    }

    /**
	* Gets details of an article
    * @param int $id
	* @return array
	*/
    public function getArticleDetail(){
		$this->checkPrivilege('read');
        $id = $this->getParam('artid', null);
        if (empty($id)) {
            throw new ApiException\ParameterMissingException();
        }

		$builder = Shopware()->Models()->createQueryBuilder();
        $builder->select("a")
                ->from("Shopware\Models\Article\Detail", "a")
                ->where("a.number = '".$id."'");

        $query = $builder->getQuery();
        $detail = $query->getOneOrNullResult();
        $this->getManager()->flush();
        if ($detail) {
           return array('success' => true, 'data' => $detail->getArticle()->getId());
        }
        return array('success' => true, 'data' => '');

    }

    /**
	* Creates a Price
    *
	* @return array
	*/
     public function createCustomerPrice(){
        $this->checkPrivilege('read','create','update');
        $data = $this->getData();
        $customerID = $data["customer_id"];
        $cshopID = $data["shopid"];
        $prices = $data["priceList"];
        $groupName = $data["group_name"];
        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select("a")
                ->from("Shopware\Models\Customer\Customer", "a")
                ->where("a.id = '".$customerID."'")
                ->andWhere("a.shopId = '" . $cshopID . "'");

        $query = $builder->getQuery();
        $customer = $query->getOneOrNullResult();

		$group = null;

        if (!isset($groupName) || $groupName == "") {
			$group = new \Shopware\CustomModels\UserPrice\Group();
			$groupName = $customer->getDefaultBillingAddress()->getFirstName() . "_" .  $customer->getDefaultBillingAddress()->getLastName() . "_" . rand(1, 1000);
			$group->setName($groupName);
			$group->setGross(0);
			$group->setActive(1);
			$violations = $this->getManager()->validate($group);
			if ($violations->count() > 0) {
				throw new ApiException\ValidationException($violations);
			}
			$this->getManager()->persist($group);
        }
        else {
			$builder = Shopware()->Models()->createQueryBuilder();
			$builder->select("b")->from("Shopware\CustomModels\UserPrice\Group", "b")->where("b.name = '" . $groupName . "'");
			$query = $builder->getQuery();
			$group = $query->getOneOrNullResult();
        }

        if (!isset($group)) {
			return array('success' => false, 'data' => '');
		}

        foreach ($prices as $price) {
			
			$article = $this->getManager()->find(Article::class, $price["product_id"]);
			$detail = $this->getManager()->find(Detail::class, $price["detailsId"]);
			
			if (!isset($article) || !isset($detail)) {
				return array('success' => false, 'data' => '');
			}
			
			$newprice = new \Shopware\CustomModels\UserPrice\Price();
			$newprice->setArticle($article);
			$newprice->setPriceGroup($group);
			$newprice->setPrice($price["price"]);
			$newprice->setFrom($price["fromAmount"]);
			$newprice->setTo($price["toAmount"]);
			$newprice->setDetail($detail);

			$violations = $this->getManager()->validate($newprice);
			if ($violations->count() > 0) {
			  throw new ApiException\ValidationException($violations);
			}

			$this->getManager()->persist($newprice);
			
        }
          $this->getManager()->flush();

		 if (!$attribute = $customer->getAttribute()) {
			$attribute = new \Shopware\Models\Attribute\Customer();
		}

		$attribute->setCustomer($customer);
		$attribute->setSwagPricegroup($group->getId());

         $violations = $this->getManager()->validate($customer);
        if ($violations->count() > 0) {
            throw new ApiException\ValidationException($violations);
        }

        $this->getManager()->persist($customer);
		 $this->getManager()->flush();
        return array('success' => true, 'data' => $groupName);
    }

    /**
	* Delete all customer pricelists
	* @return array
	*/
    public function deleteAllCustomerPriceLists(){
         set_time_limit(9999);

        $this->checkPrivilege('delete');

		$builder = Shopware()->Models()->createQueryBuilder();
		$builder->select('g')
				->from('Shopware\CustomModels\UserPrice\Group', 'g');

		$query = $builder->getQuery();
		$groups = $query->getResult();

		if (count($groups) > 0) {
			foreach ($groups as $group) {

				if (is_object($group)) {
						Shopware()->Models()->remove($group);
						Shopware()->Models()->flush();
					}

				}
			}

		$builder = Shopware()->Models()->createQueryBuilder();
		$builder->select('p')
				->from('Shopware\CustomModels\UserPrice\Price', 'p');

		$query = $builder->getQuery();
		$prices = $query->getResult();

		if (count($prices) > 0) {
			foreach ($prices as $price) {

				if (is_object($price)) {
						Shopware()->Models()->remove($price);
						Shopware()->Models()->flush();
					}

				}
			}


        return array('success' => true);

    }


    /**
    * Delete filtered customer pricelists
    * @return array
    */
    public function deleteFilteredCustomerPriceLists(){
        set_time_limit(9999);
		$this->checkPrivilege('read', 'create','delete');
        $data = $this->getData();      
		
		foreach ($data as $entry){
			$customerID = $entry["customer_id"];
			$cshopID = $entry["shopid"];
			
			$builder = Shopware()->Models()->createQueryBuilder();
			$builder->select("a")
				->from("Shopware\Models\Customer\Customer", "a")
				->where("a.id = '".$customerID."'")
				->andWhere("a.shopId = '" . $cshopID . "'");

			$query = $builder->getQuery();
			$customer = $query->getOneOrNullResult();			
			$attribute = $customer->getAttribute();	
			
			if ($attribute){
					$sql = "DELETE FROM s_plugin_pricegroups_prices WHERE `pricegroup` = '".$attribute->getSwagPricegroup()."'";
					$result = Shopware()->Db()->query($sql);
					$sql = "DELETE FROM s_plugin_pricegroups WHERE `id` = '".$attribute->getSwagPricegroup()."'";
					$result = Shopware()->Db()->query($sql);			
			}	
			
		}
        return array('success' => true);
    }

    /**
	* Warm up the HTTP cache
	* @return array
	*/
	public function warmUpCache() {
		$shopID = (int)$this->getParam('shopID', 1);

		if ($shopID == null)
			return array('success' => false, 'data' => 'Shop ID not set.');

		$factory = new \Shopware\Components\HttpClient\GuzzleFactory();
		// Get an instance of the CacheWarmer class
		$resource =  new \Shopware\Components\HttpCache\CacheWarmer(Shopware()->Models()->getConnection(), Shopware()->Pluginlogger(), $factory);

		// Get all view ports
		$viewPorts = [];
		$viewPorts[] = $cacheWarmer->ARTICLE_PATH;
		$viewPorts[] = $cacheWarmer->CATEGORY_PATH;
		$viewPorts[] = $cacheWarmer->BlOG_PATH;
		$viewPorts[] = $cacheWarmer->CUSTOM_PATH;
		$viewPorts[] = $cacheWarmer->EMOTION_LANDING_PAGE_PATH;
		$viewPorts[] = $cacheWarmer->SUPPLIER_PATH;

		$urls = $resource->getSEOUrlByViewPort($viewPorts, $shopID, -1, 0);
		// Call the urls
		$resource->callUrls($urls, $shopID);

		return array('success' => true, 'data' => '');
	}

    /*
    * Deletes filter values and filter groups (called options)
    * if they have no assignments.
    * @author Fabian Sunnus
    * @return array
    */
    public function deleteUnassignedFilters()
    {
        set_time_limit(9999);

        $this->checkPrivilege('delete');

	    $queryBuilder = Shopware()->Models()->createQueryBuilder();
	    $queryBuilder->select('option')
		    ->from('Shopware\Models\Property\Option', 'option')
		    ->leftJoin('option.groups', 'g')
		    ->where('g.id IS NULL');

	    $query = $queryBuilder->getQuery();

	    $options_to_delete = $query->getResult();

	    foreach ($options_to_delete as $option)
	    {
		    $this->getManager()->remove($option);
	    }

	    $this->getManager()->flush();

        $queryBuilder = Shopware()->Models()->createQueryBuilder();
        $queryBuilder->select('value')
		    ->from('Shopware\Models\Property\Value', 'value')
		    ->leftJoin('value.articles', 'article')
		    ->where("article.id IS NULL");

        $query = $queryBuilder->getQuery();

        $values_to_delete = $query->getResult();

        foreach ($values_to_delete as $value)
	    {
		    $this->getManager()->remove($value);
	    }

	    $this->getManager()->flush();

	    return array('success' => true);
    }
    
      /**
  * Rebuild the s_core_rewrite_urls table
  * @return array
  */
  public function recreateSEOUrls()  {
		$shops = Shopware()->Db()->fetchCol('SELECT id FROM s_core_shops WHERE active = 1');
		
		// Rebuild the table
		Shopware()->SeoIndex()->registerShop($shops[0]);
		$rewriteTable = Shopware()->Modules()->RewriteTable();       
		$rewriteTable->sCreateRewriteTableCleanup();
		
		foreach ($shops as $shopId) {
			
			$repository = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
			$shop = $repository->getActiveById($shopId);
			 if ($shop === null) {
                throw new Exception('No valid shop id passed');
            }
			
			$shop->registerResources();        
			Shopware()->Modules()->Categories()->baseId = $shop->getCategory()->getId();
			$rewriteTable->baseSetup();
			
			$context =  $this->getContainer()->get('shopware_storefront.context_service')->createShopContext($shopId);
			
			$rewriteTable->sCreateRewriteTableCategories();
			$rewriteTable->sCreateRewriteTableCampaigns();
			$rewriteTable->sCreateRewriteTableContent();
			$rewriteTable->sCreateRewriteTableBlog();
			$rewriteTable->sCreateRewriteTableSuppliers(null, null, $context);
			$rewriteTable->sCreateRewriteTableStatic();			
			
		}   		
		return array('success' => true, 'data' => '');
	}

       /**
	* Creates a relations between filter value and images
    *
	* @return array
	*/
     public function addFilterValueImage(){
        $this->checkPrivilege('update', 'read');
        $data = $this->getData();    
        $repositoryMedia = $this->getManager()->getRepository('Shopware\Models\Media\Media');
        $repositoryPropertyValue = $this->getManager()->getRepository('Shopware\Models\Property\Value');
        if (empty($data)) {
            throw new ApiException\ParameterMissingException();
        }
         
            foreach ($data as $entry){
                $sql = "SELECT fv.id FROM s_filter_values fv ";
                $sql .= "JOIN s_filter_options fo ON fv.optionID = fo.id ";
                $sql .= "JOIN s_filter_relations fr on fr.optionID = fv.optionID ";
                $sql .= "JOIN s_filter f on fr.groupID = f.id ";
                $sql .= "WHERE f.id = '". $entry["setId"] . "' AND fo.name  = '". $entry['option'] ."' AND fv.value = '" . $entry['value'] ."'";
				 //create / update
                 if (!empty($entry['mediaId'])){                
                    $erg = Shopware()->Db()->fetchAll($sql);
                
                    if($erg){
                        foreach($erg as $id){
                            $value = $repositoryPropertyValue->findOneById($id);
                            if($value){
								if(!empty($entry['attribute'])){
									$value->setAttribute($entry['attribute']);									
								}								
                                $media = $repositoryMedia->findOneById($entry['mediaId']);  
                                if($media){
                                    $value->setMedia($media);
                                    $violations = $this->getManager()->validate($value);
                                    if ($violations->count() > 0) {
                                        throw new ApiException\ValidationException($violations);
                                    }
                                    $this->flush();
                                }
                            }
                        }
                    }
                }
                else {//delete
                    $erg = Shopware()->Db()->fetchAll($sql);
                    if($erg){
                        foreach($erg as $id){
                            $value = $repositoryPropertyValue->findOneById($id);
                            if($value){
								if(!empty($entry['attribute'])){
									$value->setAttribute($entry['attribute']);									
								}									
                                $media = $repositoryMedia->findOneById($entry['mediaId']);  
                                if(!$media){
                                    $value->setMedia($media);
                                    $violations = $this->getManager()->validate($value);
                                    if ($violations->count() > 0) {
                                        throw new ApiException\ValidationException($violations);
                                    }
                                    $this->flush();
                                }
                            }
                        }
                    }
                }

            }      

       return array('success' => true, 'data' => '');
    }
    
      public function getTemplates()
  {
  $this->checkPrivilege('get');

 $limit = $this->getParam('limit', 1000);
 $offset = $this->getParam('start', 0);
 $sort = $this->getParam('sort', array());
 $filter = $this->getParam('filter', array());

 $repository = $this->getManager()->getRepository('SwagCustomProducts\Models\Template');
 $builder = $repository->createQueryBuilder('Template');

 $builder->addFilter($filter);
 $builder->addOrderBy($sort);
 $builder->setFirstResult($offset)
         ->setMaxResults($limit);

 $query = $builder->getQuery();
 $query->setHydrationMode($this->getResultMode());
 $paginator = Shopware()->Models()->createPaginator($query);

 $totalResult = $paginator->count();
 $areas = $paginator->getIterator()->getArrayCopy();

 return array('success' => true, 'data' => $areas, 'total' => $totalResult);
 }


  public function createTemplate()
  {
    $this->checkPrivilege('create');
    $data = $this->getData();
    if (empty($data)) {
            throw new ApiException\ParameterMissingException();
        }

    $newTemplate = new \SwagCustomProducts\Models\Template();
    $newTemplate->setInternalName($data['internalName']);
    $newTemplate->setDisplayName($data['displayName']);
    $newTemplate->setDescription($data['description']);
    $newTemplate->setStepByStepConfigurator($data['stepByStepConfigurator']);
    $newTemplate->setActive($data['active']);
    $newTemplate->setConfirmInput($data['confirmInput']);

    $this->getManager()->persist($newTemplate);
    $this->getManager()->flush();

    return array('success'=>true,'data'=> $newTemplate->getId());

  }
	
	 public function removeTemplate()
  {
    $this->checkPrivilege('create');
    $data = $this->getData();
    if (empty($data)) {
            throw new ApiException\ParameterMissingException();
        }
				
		$repository = $this->getManager()->getRepository('\SwagCustomProducts\Models\Template');
		$template = $repository->findOneById($data['templateId']);  
   
    $this->getManager()->remove($template);
    $this->getManager()->flush();

    return array('success'=>true,'data'=>'');

  }



  public function addProductToTemplate()
  {
    try{
    $this->checkPrivilege('create');
    $data = $this->getData();
        if (empty($data)) {
            throw new ApiException\ParameterMissingException();
        }

    $queryBuilder = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();
    $queryBuilder->insert('s_plugin_custom_products_template_product_relation')
            ->values([
            'article_id' => ':articleId',
            'template_id' => ':templateId'
            ])
            ->setParameter('articleId', $data['articleId'])
            ->setParameter('templateId', $data['templateId'])
            ->execute();
     } catch (Exception $ex) {
            return array('success'=>false,'data'=>$ex);
        }
    return array('success'=>true,'data'=>'');
  }


  public function getOptions()
  { 
  $this->checkPrivilege('get');

  $limit = $this->getParam('limit', 1000);
  $offset = $this->getParam('start', 0);
  $sort = $this->getParam('sort', array());
  $filter = $this->getParam('filter', array());

  $repository = $this->getManager()->getRepository('SwagCustomProducts\Models\Option');
  $builder = $repository->createQueryBuilder('Options');

  $builder->addFilter($filter);
  $builder->addOrderBy($sort);
  $builder->setFirstResult($offset)
         ->setMaxResults($limit);

  $query = $builder->getQuery();
  $query->setHydrationMode($this->getResultMode());
  $paginator = Shopware()->Models()->createPaginator($query);

  $totalResult = $paginator->count();
  $areas = $paginator->getIterator()->getArrayCopy();

  return array('success' => true, 'data' => $areas, 'total' => $totalResult);
  }


  public function addOption()
  {
    $this->checkPrivilege('create');
    $data = $this->getData();
    if (empty($data)) {
      throw new ApiException\ParameterMissingException();
    }

    $newOption = new \SwagCustomProducts\Models\Option();
    $newOption->setTemplateId($data['templateId']);
    $newOption->setName($data['name']);
    $newOption->setDescription($data['description']);
    $newOption->setType($data['type']);
    $newOption->setCouldContainValues($data['couldContainValues']);

    $this->getManager()->persist($newOption);
    $this->getManager()->flush();

    return array('success'=>true,'data'=>$newOption->getId());  
  }
  
  
  public function addOptionsAndValues()
  {
    try{
    $this->checkPrivilege('create');
    $data = $this->getData();
    if (empty($data)) {
      throw new ApiException\ParameterMissingException();
    }
    foreach($data['options'] as $entry)
    {
      $builder = Shopware()->Models()->createQueryBuilder();
      $builder->select('a')
                ->from('SwagCustomProducts\Models\Option', 'a')
                ->where('a.name = :name')
                ->andWhere('a.templateId = :templateId')
                ->setParameter('name', $entry['name'])
                ->setParameter('templateId',$data['templateId']);
      $query = $builder->getQuery();
      $newOption = $query->getOneOrNullResult();
      $this->getManager()->flush();
      
      if(!$newOption)
      {
      $newOption = new \SwagCustomProducts\Models\Option();
      }
      
      $newOption->setTemplateId($data['templateId']);
      $newOption->setName($entry['name']);
      $newOption->setDescription($entry['description']);
      $newOption->setType($entry['type']);
      $newOption->setCouldContainValues($entry['couldContainValues']);
      $newOption->setRequired($entry['required']);

      $this->getManager()->persist($newOption);
      $this->getManager()->flush();
      $newOptionId = $newOption->getId();
			
						$builder = Shopware()->Models()->createQueryBuilder();
            $builder->select('a')
                    ->from('Shopware\Models\Customer\Group', 'a')                 
                    ->setMaxResults(1);
            $query = $builder->getQuery();
            $customerGroup = $query->getOneOrNullResult();
        
            $builder = Shopware()->Models()->createQueryBuilder();
            $builder->select('a')
                    ->from('SwagCustomProducts\Models\Price', 'a')
                    ->where('a.optionId = :optionId')
                    ->andWhere('a.customerGroupId = :customerGroupId')
                    ->setParameter('optionId', $newOption->getId())
                    ->setParameter('customerGroupId',$customerGroup->getId());
            $query = $builder->getQuery();
            $newPrice = $query->getOneOrNullResult();
            $this->getManager()->flush();
    
            if(!$newPrice)
            {
            $newPrice = new \SwagCustomProducts\Models\Price();
            }
            $builder = Shopware()->Models()->createQueryBuilder();
            $builder->select('a')
                    ->from('Shopware\Models\Customer\Group', 'a')
                    ->where('a.id = :Id')
                    ->setParameter('Id', $customerGroup->getId());
            $query = $builder->getQuery();
            $customerGroup = $query->getOneOrNullResult();
            $this->getManager()->flush();

            $newPrice->setOptionId($newOption->getId());        
            $newPrice->setCustomerGroupId($customerGroup->getId());
            $newPrice->setCustomerGroupName($customerGroup->getName());
            $newPrice->setSurcharge(0);
            $newPrice->setTaxId(1);
            $newPrice->setPercentage(0.0);
            $newPrice->setIsPercentageSurcharge(false);
            $this->getManager()->persist($newPrice);
            $this->getManager()->flush();     
						
						
			
      foreach($entry['values'] as $value)
      { 
        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select('a')
                ->from('SwagCustomProducts\Models\Value', 'a')
                ->where('a.name = :name')
                ->andWhere('a.optionId = :optionId')
                ->setParameter('name', $value['name'])
                ->setParameter('optionId',$newOptionId);
        $query = $builder->getQuery();
        $newValue = $query->getOneOrNullResult();
        $this->getManager()->flush();

        if(!$newValue)
        {
        $newValue = new \SwagCustomProducts\Models\Value();
        }

        $newValue->setOptionId($newOptionId);
        $newValue->setName($value['name']);
        $newValue->setOrdernumber($value['orderNumber']);
        $newValue->setValue($value['value']);
        if($value['mediaId'] != "")
        {
          $newValue->setMediaId($value['mediaId']);
        }
        $this->getManager()->persist($newValue);
        $this->getManager()->flush();
        
				 if (count($value['prices']) == 0){ 
                $builder = Shopware()->Models()->createQueryBuilder();
                $builder->select('a')
                        ->from('Shopware\Models\Customer\Group', 'a')                 
                        ->setMaxResults(1);
                $query = $builder->getQuery();
                $customerGroup = $query->getOneOrNullResult();
            
                $builder = Shopware()->Models()->createQueryBuilder();
                $builder->select('a')
                        ->from('SwagCustomProducts\Models\Price', 'a')
                        ->where('a.valueId = :valueId')
                        ->andWhere('a.customerGroupId = :customerGroupId')
                        ->setParameter('valueId', $newValue->getId())
                        ->setParameter('customerGroupId',$customerGroup->getId());
                $query = $builder->getQuery();
                $newPrice = $query->getOneOrNullResult();
                $this->getManager()->flush();
        
                if(!$newPrice)
                {
                $newPrice = new \SwagCustomProducts\Models\Price();
                }
                $builder = Shopware()->Models()->createQueryBuilder();
                $builder->select('a')
                        ->from('Shopware\Models\Customer\Group', 'a')
                        ->where('a.id = :Id')
                        ->setParameter('Id', $customerGroup->getId());
                $query = $builder->getQuery();
                $customerGroup = $query->getOneOrNullResult();
                $this->getManager()->flush();

            
                $newPrice->setValueId($newValue->getId());        
                $newPrice->setCustomerGroupId($customerGroup->getId());
                $newPrice->setCustomerGroupName($customerGroup->getName());
                $newPrice->setSurcharge(0);
                $newPrice->setTaxId(1);
                $newPrice->setPercentage(0.0);
                $newPrice->setIsPercentageSurcharge(false);
                $this->getManager()->persist($newPrice);
                $this->getManager()->flush();       
                }
				
        foreach($value['prices'] as $price)
        {
         $builder = Shopware()->Models()->createQueryBuilder();
         $builder->select('a')
                 ->from('SwagCustomProducts\Models\Price', 'a')
                 ->where('a.valueId = :valueId')
                 ->andWhere('a.customerGroupId = :customerGroupId')
                 ->setParameter('valueId', $newValue->getId())
                 ->setParameter('customerGroupId',$price["customerGroupId"]);
         $query = $builder->getQuery();
         $newPrice = $query->getOneOrNullResult();
         $this->getManager()->flush();
  
         if(!$newPrice)
         {
          $newPrice = new \SwagCustomProducts\Models\Price();
         }
         $builder = Shopware()->Models()->createQueryBuilder();
         $builder->select('a')
                 ->from('Shopware\Models\Customer\Group', 'a')
                 ->where('a.id = :Id')
                 ->setParameter('Id', $price['customerGroupId']);
         $query = $builder->getQuery();
         $customerGroup = $query->getOneOrNullResult();
         $this->getManager()->flush();

         $newPrice->setValueId($newValue->getId());
         $newPrice->setCustomerGroupId($price['customerGroupId']);
         $newPrice->setCustomerGroupName($customerGroup->getName());
         $newPrice->setSurcharge($price['price']);
         $newPrice->setTaxId($price['taxId']);
				 $newPrice->setPercentage(0.0);
         $newPrice->setIsPercentageSurcharge(false);
         $this->getManager()->persist($newPrice);
         $this->getManager()->flush();       
        }

      } 
     
    }
    $this->getManager()->flush();
    }
    catch (Exception $ex) {
            return array('success'=>false,'data'=>$ex);
        }

    return array('success'=>true,'data'=>'');  
  }


  public function getValues()
  {
  $this->checkPrivilege('get');

  $limit = $this->getParam('limit', 1000);
  $offset = $this->getParam('start', 0);
  $sort = $this->getParam('sort', array());
  $filter = $this->getParam('filter', array());

  $repository = $this->getManager()->getRepository('\SwagCustomProducts\Models\Value');
  $builder = $repository->createQueryBuilder('Value');

  $builder->addFilter($filter);
  $builder->addOrderBy($sort);
  $builder->setFirstResult($offset)
         ->setMaxResults($limit);

  $query = $builder->getQuery();
  $query->setHydrationMode($this->getResultMode());
  $paginator = Shopware()->Models()->createPaginator($query);

  $totalResult = $paginator->count();
  $areas = $paginator->getIterator()->getArrayCopy();

  return array('success' => true, 'data' => $areas, 'total' => $totalResult);
  }
 
 
  public function addValue()
  {
    $this->checkPrivilege('create');
    $data = $this->getData();
    if (empty($data)) {
      throw new ApiException\ParameterMissingException();
    } 

    $newValue = new \SwagCustomProducts\Models\Value();
    $newValue->setOptionId($data['optionId']);
    $newValue->setName($data['name']);
    $newValue->setOrdernumber($data['ordernumber']);
    $newValue->setValue($data['value']);

    $this->getManager()->persist($newValue);
    $this->getManager()->flush();

    return array('success'=>true,'data'=>$newValue->getId());  
  }
    
    /**
     * Retrieve an array of payments.
     * @return array
     */
    public function getPayments() {
        $this->checkPrivilege('read');

        $limit = $this->getParam('limit', 1000);
        $offset = $this->getParam('start', 0);
        $sort = $this->getParam('sort', array());
        $filter = $this->getParam('filter', array());

        $repository = $this->getManager()->getRepository('Shopware\Models\Payment\Payment');
        $builder = $repository->createQueryBuilder('Payment');

        $builder->addFilter($filter);
        $builder->addOrderBy($sort);
        $builder->setFirstResult($offset)
                ->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode($this->getResultMode());

        $paginator = Shopware()->Models()->createPaginator($query);

        $totalResult = $paginator->count();
        $payments = $paginator->getIterator()->getArrayCopy();

        return array('success' => true, 'data' => $payments, 'total' => $totalResult);
    }
		
		public function updateLockedOptions()
    {
       
        $this->checkPrivilege('create');
        $data = $this->getData();
        if (empty($data)) {
        throw new ApiException\ParameterMissingException();
        }
        $productsModel = $data['productsModel'];
        $sql = "DELETE FROM s_plugin_mojr_custom_products_conditional_options WHERE `selectedOptionValueOrdernumber` = '".$productsModel."'";
        $result = Shopware()->Db()->query($sql);

            foreach($data['lockedOptions'] as $entry)
            {
                if (isset($entry['lockedOption']) and $entry['lockedOption'] != ''){
                $queryBuilder = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();
                $queryBuilder->insert('s_plugin_mojr_custom_products_conditional_options')
                    ->values([
                    'selectedOptionValueOrdernumber' => ':selectedOptionValueOrdernumber',
                    'lockedOptionValueOrdernumber' => ':lockedOptionValueOrdernumber'
                    ])
                    ->setParameter('selectedOptionValueOrdernumber', $productsModel)
                    ->setParameter('lockedOptionValueOrdernumber', $entry['lockedOption'])
                    ->execute();
                    }
            }
        return array('success' => true);        
        } 
        
     /**
     * Retrieve an array of shippments.
     * @return array
     */
    public function getShipments() {
        $this->checkPrivilege('read');
        $sql = "SELECT id, name FROM s_premium_dispatch;";
        $erg = Shopware()->Db()->fetchAll($sql);
       
        return array('success' => true, 'data' => $erg);
    }
    
     /**
     * Retrieve an array of shipment status
     * @return array
     */
    public function getShipmentStatus() {
        $this->checkPrivilege('read');
        $sql = "SELECT id, description FROM s_core_states WHERE `group` = 'state';";
        $erg = Shopware()->Db()->fetchAll($sql);    
        return array('success' => true, 'data' => $erg);
    }  
    
         /**
     * Retrieve an array of payment status.
     * @return array
     */
    public function getPaymentStatus() {
        $this->checkPrivilege('read');
        $sql = "SELECT id, description FROM s_core_states WHERE `group` = 'payment';";
        $erg = Shopware()->Db()->fetchAll($sql);      
        return array('success' => true, 'data' => $erg);
    }  
	
	// -----------------------------------
	// LiveShopping LiveShopping Functions
	// -----------------------------------

	private function updateliveShoppingObjectFromData(&$liveShopping, $data)
	{
		if ( $data != null && is_array($data) && count($data) > 0 )
		{
			if ( array_key_exists("articleId", $data) )
			{
				if ( $data["articleId"] != null )
				{
					$articleRepository = $this->getManager()->getRepository('Shopware\Models\Article\Article');
					$article = $articleRepository->findOneById($data["articleId"]);
					$liveShopping->setArticle($article);
				}
				else
				{
					$liveShopping->setArticle(null);
				}
			}
			
			if ( array_key_exists("customerGroupIds", $data) )
			{
				if ( $data["customerGroupIds"] != null && is_array($data["customerGroupIds"]) )
				{
					$tempBuilder = Shopware()->Models()->createQueryBuilder();
					$tempBuilder->select('g')
									->from('Shopware\Models\Customer\Group', 'g');
					$tempQuery = $tempBuilder->getQuery();
					$customerGroupsArray = $tempQuery->getResult();
					Shopware()->Models()->flush();
					
					$finalCustomerGroupsArray = array();
					
					foreach ( $customerGroupsArray as $customerGroupsArrayItem )
					{
						if ( in_array($customerGroupsArrayItem->getId(), $data["customerGroupIds"]) )
						{						
							array_push($finalCustomerGroupsArray, $customerGroupsArrayItem);
						}
					}
					
					$liveShopping->setCustomerGroups($finalCustomerGroupsArray);
				}
				else
					
				if ( $data["customerGroupIds"] == null )	
				{
					$liveShopping->setCustomerGroups(null);
				}
			}
			
			if ( array_key_exists("shopIds", $data) )
			{
				if ( $data["shopIds"] != null && is_array($data["shopIds"]) )
				{
					$tempBuilder = Shopware()->Models()->createQueryBuilder();
					$tempBuilder->select('s')
									->from('Shopware\Models\Shop\Shop', 's');
					$tempQuery = $tempBuilder->getQuery();
					$shopsArray = $tempQuery->getResult();
					Shopware()->Models()->flush();
					
					$finalShopsArray = array();
					
					foreach ( $shopsArray as $shopsArrayItem )
					{
						if ( in_array($shopsArrayItem->getId(), $data["shopIds"]) )
						{						
							array_push($finalShopsArray, $shopsArrayItem);
						}
					}
					
					$liveShopping->setShops($finalShopsArray);
				}
				else
					
				if ( $data["shopIds"] == null )	
				{
					$liveShopping->setShops(null);
				}
			}
		}
	}

	public function getLiveShoppingLiveShoppingJustPrices()
	{
		$this->checkPrivilege('read');
		
		$id = $this->getParam('liveShoppingId', null);
		
		if ( empty($id) ) {
			throw new ApiException\ParameterMissingException();
		}
		
		$sql = "SELECT * FROM s_articles_live_prices WHERE live_shopping_id = '".$id."'";
		$result = Shopware()->Db()->fetchAll($sql);
		
		return array('success' => true, 'data' => $result);
	}

	public function getLiveShoppingLiveShoppings()
	{
		$this->checkPrivilege('get');
		
        $limit = $this->getParam('limit', 1000);
        $offset = $this->getParam('start', 0);
        $sort = $this->getParam('sort', array());
        $filter = $this->getParam('filter', array());
		
		$repository = $this->getManager()->getRepository('SwagLiveShopping\Models\LiveShopping');
		$builder = $repository->createQueryBuilder('LiveShopping');
		
		$builder->addFilter($filter);
		$builder->addOrderBy($sort);
		$builder->setFirstResult($offset)
				->setMaxResults($limit);
		
		$query = $builder->getQuery();
		$query->setHydrationMode($this->getResultMode());

		$paginator = Shopware()->Models()->createPaginator($query);

		$totalResult = $paginator->count();
		$liveShoppings = $paginator->getIterator()->getArrayCopy();

		return array('success' => true, 'data' => $liveShoppings, 'total' => $totalResult);
	}
	
	public function createLiveShoppingLiveShopping()
	{
		$this->checkPrivilege('create');
		
		$data = $this->getData();
				
		$liveShopping = new \SwagLiveShopping\Models\LiveShopping();
		$liveShopping->fromArray($data);
		
		$this->updateliveShoppingObjectFromData($liveShopping, $data);
		
		$this->getManager()->persist($liveShopping);
		$this->flush();
		
		return array('success' => true, 'data' => $liveShopping->getId());
	}

	public function updateLiveShoppingLiveShopping()
	{
		$this->checkPrivilege('create');
		
		$id = $this->getParam('liveShoppingId', null);
		
		if ( empty($id) ) {
			throw new ApiException\ParameterMissingException();
		}
		
		$repository = $this->getManager()->getRepository('SwagLiveShopping\Models\LiveShopping');
		$liveShopping = $repository->findOneById($id);
		
		if ( $liveShopping == null ) {
			throw new ApiException\NotFoundException("LiveShopping by id $id not found");
		}
		
		$data = $this->getData();
		$liveShopping->fromArray($data);
		
		$this->updateliveShoppingObjectFromData($liveShopping, $data);
		
		$this->getManager()->persist($liveShopping);
		$this->flush();
		
		return array('success' => true, 'data' => $liveShopping->getId());
	}

	public function deleteLiveShoppingLiveShopping()
	{
		$this->checkPrivilege('delete');
		
		$id = $this->getParam('liveShoppingId', null);
		
		if ( empty($id) ) {
			throw new ApiException\ParameterMissingException();
		}
		
		$repository = $this->getManager()->getRepository('SwagLiveShopping\Models\LiveShopping');
		$liveShopping = $repository->findOneById($id);
		
		if ( $liveShopping == null ) {
			throw new ApiException\NotFoundException("LiveShopping by id $id not found");
		}
		
		$this->getManager()->Remove($liveShopping);
		$this->getmanager()->flush();

		return array('success' => true);
	}

	// ----------------------------
	// LiveShopping Price Functions
	// ----------------------------

	private function updateliveShoppingPriceObjectFromData(&$liveShoppingPrice, $data)
	{
		if ( $data != null && is_array($data) && count($data) > 0 )
		{
			if ( array_key_exists("customerGroupId", $data) )
			{
				if ( $data["customerGroupId"] != null )
				{
					$customerGroupRepository = $this->getManager()->getRepository('Shopware\Models\Customer\Group');
					$customerGroup = $customerGroupRepository->findOneById($data["customerGroupId"]);
					$liveShoppingPrice->setCustomerGroup($customerGroup);
				}
				else
				{
					$liveShoppingPrice->setCustomerGroup(null);
				}
			}
		}
	}

	public function getLiveShoppingPrices()
	{
		$this->checkPrivilege('get');
		
        $limit = $this->getParam('limit', 1000);
        $offset = $this->getParam('start', 0);
        $sort = $this->getParam('sort', array());
        $filter = $this->getParam('filter', array());
		
		$repository = $this->getManager()->getRepository('SwagLiveShopping\Models\Price');
		$builder = $repository->createQueryBuilder('Price');
		
		$builder->addFilter($filter);
		$builder->addOrderBy($sort);
		$builder->setFirstResult($offset)
				->setMaxResults($limit);
		
		$query = $builder->getQuery();
		$query->setHydrationMode($this->getResultMode());

		$paginator = Shopware()->Models()->createPaginator($query);

		$totalResult = $paginator->count();
		$prices = $paginator->getIterator()->getArrayCopy();

		return array('success' => true, 'data' => $prices, 'total' => $totalResult);
	}

	public function createLiveShoppingPrice()
	{
		$this->checkPrivilege('create');
		
		$data = $this->getData();
		
		$price = new \SwagLiveShopping\Models\Price();
		$price->fromArray($data);
		
		$this->updateliveShoppingPriceObjectFromData($price, $data);
		
		$this->getManager()->persist($price);
		$this->flush();
		
		return array('success' => true, 'data' => $price->getId());
	}

	public function updateLiveShoppingPrice()
	{
		$this->checkPrivilege('create');
		
		$id = $this->getParam('priceId', null);
		
		if ( empty($id) ) {
			throw new ApiException\ParameterMissingException();
		}
		
		$repository = $this->getManager()->getRepository('SwagLiveShopping\Models\Price');
		$price = $repository->findOneById($id);
		
		if ( $price == null ) {
			throw new ApiException\NotFoundException("Price by id $id not found");
		}
		
		$data = $this->getData();
		$price->fromArray($data);
		
		$this->updateliveShoppingPriceObjectFromData($price, $data);
		
		$this->getManager()->persist($price);
		$this->flush();
		
		return array('success' => true, 'data' => $price->getId());
	}

	public function deleteLiveShoppingPrice()
	{
		$this->checkPrivilege('delete');
		
		$id = $this->getParam('priceId', null);
		
		if ( empty($id) ) {
			throw new ApiException\ParameterMissingException();
		}
		
		$repository = $this->getManager()->getRepository('SwagLiveShopping\Models\Price');
		$price = $repository->findOneById($id);
		
		if ( $price == null ) {
			throw new ApiException\NotFoundException("Price by id $id not found");
		}
		
		$this->getManager()->Remove($price);
		$this->getmanager()->flush();

		return array('success' => true);
	}	
  }  

?>