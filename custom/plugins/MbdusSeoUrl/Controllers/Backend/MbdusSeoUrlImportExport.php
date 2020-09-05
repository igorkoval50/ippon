<?php
/**
 * Plugin-Backend-Controller for seo ulrs import export
 * @package MbdusSeoUrls
 * @subpackage Controller
 * @author Mathias Bauer <info@mbdus.de>
 */
class Shopware_Controllers_Backend_MbdusSeoUrlImportExport extends Shopware_Controllers_Backend_ExtJs {
	
	/**
	 * settings for export csv
	 * @var array
	 */
	private $sSettings = [
			'fieldmark' => '"',
			'separator' => ';',
			'encoding' => 'ISO-8859-1', //UTF-8
			'escaped_separator' => '',
			'escaped_fieldmark' => '""', 'newline' => "\n", 'escaped_newline' => '',
	];
	
	/**
	 *
	 * @var string path to termporary uploaded file for import
	 */
	protected $uploadedFilePath;
	
	/**
	 * Inits ACL-Permissions
	 */
	protected function initAcl() {
		$this->addAclPermission ( 'import', 'export', 'Insufficient Permissions' );
	}
	
	/**
	 * Initials the default template directory for this plugin
	 * 
	 * @see Shopware_Controllers_Backend_ExtJs::init()
	 */
	public function init() {
		$this->View ()->addTemplateDir ( dirname(__FILE__) . '/../../Resources/views/' );
		parent::init ();
	}
	
	/**
	 * index action is called if no other action is triggered
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->View ()->loadTemplate ( "backend/mbdus_seo_url_import_export/app.js" );
	}
	
	/**
	 * Exports seo article Data as CSV
	 */
	public function exportSeoUrlsAction() {
		$this->Front ()->Plugins ()->Json ()->setRenderer ( false );
		
		$joinStatements = [];
		$selectStatements = [];
		$pseudoStatements = [];
		
		$sql = '
               SELECT id
               FROM s_core_shops
               WHERE `default`=0
        ';
			
		$languages = Shopware ()->Db ()->fetchCol ( $sql );
			
		$translationFields = array (
				"mbdusSeoUrl" => "mbdusSeoUrl",
		);
			
		$joinStatements[] = "
					INNER JOIN s_articles_details ad
            ON ad.id = a.main_detail_id AND ad.kind = 1
				";
			
		$joinStatements[] = "
					INNER JOIN s_articles_attributes aa
           ON ad.id = aa.articledetailsID
				";
			
		$joinStatements[] = "
					LEFT JOIN s_articles_supplier as s
		ON a.supplierID = s.id
				";
			
		foreach ( $languages as $language ) {
			$joinStatements [] = "
			LEFT JOIN s_core_translations as ta_$language
			ON ta_$language.objectkey=a.id AND ta_$language.objecttype='article' AND ta_$language.objectlanguage='$language'";
			
			$selectStatements[] = " IF(ta_$language.objectdata IS NULL or ta_$language.objectdata = '' or ta_$language.objectdata NOT LIKE '%mbdusSeoUrl%', (SELECT path FROM s_core_rewrite_urls WHERE org_path = CONCAT('sViewport=detail&sArticle=',ad.articleID) AND main = 1 AND subshopID = $language LIMIT 1), ta_$language.objectdata) as mbdusSeoUrl_$language ";
				
			$pseudoStatements[] = "'' as article_translation_$language";
		}
				
		if (!empty($selectStatements)) {
			$selectStatements = ', ' . implode ( ', ', $selectStatements );
		} else {
			$selectStatements = '';
		}
		if (!empty($pseudoStatements)) {
			$pseudoStatements = ', ' . implode ( ', ', $pseudoStatements );
		} else {
			$pseudoStatements = '';
		}
		$joinStatements = implode ( " \n ", $joinStatements );
			
		$sql = "SELECT ad.ordernumber, a.id, a.name, IF(mbdus_seoUrl IS NULL or mbdus_seoUrl = '', (SELECT path FROM s_core_rewrite_urls WHERE org_path = CONCAT('sViewport=detail&sArticle=',ad.articleID) AND main = 1 AND subshopID = 1 LIMIT 1), mbdus_seoUrl) as mbdusSeoUrl, 'article' as type {$selectStatements} FROM s_articles a {$joinStatements}";
		
		$sql .=" UNION ";
		
		$sql .= "SELECT '' as ordernumber, c.id, c.description, IFNULL(mbdus_seoUrl,(SELECT path FROM s_core_rewrite_urls WHERE org_path = CONCAT('sViewport=cat&sCategory=',c.id) AND main = 1 AND subshopID = 1 LIMIT 1)) as mbdusSeoUrl, 'category' as type {$pseudoStatements} FROM s_categories c 
				LEFT JOIN s_categories_attributes ca
				ON ca.categoryID = c.id
				WHERE c.blog=0 AND c.parent IS NOT NULL AND c.parent != 1  
				";
		
		$sql .=" UNION ";
		
		$sql .= "SELECT '' as ordernumber, sa.id, sa.name, IFNULL(mbdus_seoUrl,(SELECT path FROM s_core_rewrite_urls WHERE org_path = CONCAT('sViewport=listing&sAction=manufacturer&sSupplier=',sa.id) AND main = 1 AND subshopID = 1 LIMIT 1)) as mbdusSeoUrl, 'supplier' as type {$pseudoStatements} FROM s_articles_supplier sa
				LEFT JOIN s_articles_supplier_attributes saa
				ON saa.supplierID = sa.id
 				";
		
		$sql .=" UNION ";
		
		$sql .= "SELECT '' as ordernumber, b.id, b.title, IFNULL(mbdus_seoUrl,(SELECT path FROM s_core_rewrite_urls WHERE org_path LIKE CONCAT('%','&blogArticle=',b.id) AND main = 1 AND subshopID = 1 LIMIT 1)) as mbdusSeoUrl, 'blogArticle' as type {$pseudoStatements} FROM s_blog b
				LEFT JOIN s_blog_attributes ba
				ON ba.blog_id = b.id
 				";
		
		$sql .=" UNION ";
		
		$sql .= "SELECT '' as ordernumber, em.id, em.name, IFNULL(mbdus_seoUrl,(SELECT path FROM s_core_rewrite_urls WHERE org_path LIKE CONCAT('sViewport=campaign&emotionId=',em.id) AND main = 1 AND subshopID = 1 LIMIT 1)) as mbdusSeoUrl, 'emotion' as type {$pseudoStatements} FROM s_emotion em 
				LEFT JOIN s_emotion_attributes ea
				ON ea.emotionID = em.id
				WHERE em.is_landingpage = 1 
 				";
		
		$sql .=" UNION ";
		
		$sql .= "SELECT '' as ordernumber, cms.id, cms.description, IFNULL(mbdus_seoUrl,(SELECT path FROM s_core_rewrite_urls WHERE org_path LIKE CONCAT('sViewport=custom&sCustom=',cms.id) AND main = 1 AND subshopID = 1 LIMIT 1)) as mbdusSeoUrl, 'site' as type {$pseudoStatements} FROM s_cms_static cms
				LEFT JOIN s_cms_static_attributes cmsa
				ON cmsa.cmsStaticID = cms.id
				WHERE cms.link = ''
 				";
		
		$sql .=" UNION ";
		
		$version = Shopware()->Config()->get('version');
		if (version_compare($version, '5.5', '<')) {
			$sql .= "SELECT '' as ordernumber, cmsupport.id, cmsupport.name, IFNULL(mbdus_seoUrl,(SELECT path FROM s_core_rewrite_urls WHERE org_path LIKE CONCAT('sViewport=ticket&sFid=',cmsupport.id) AND main = 1 AND subshopID = 1 LIMIT 1)) as mbdusSeoUrl, 'formular' as type {$pseudoStatements} FROM s_cms_support cmsupport
					LEFT JOIN s_cms_support_attributes cmsupporta
					ON cmsupporta.cmsSupportID = cmsupport.id
	 				";
		}
		else{
			$sql .= "SELECT '' as ordernumber, cmsupport.id, cmsupport.name, IFNULL(mbdus_seoUrl,(SELECT path FROM s_core_rewrite_urls WHERE org_path LIKE CONCAT('sViewport=forms&sFid=',cmsupport.id) AND main = 1 AND subshopID = 1 LIMIT 1)) as mbdusSeoUrl, 'formular' as type {$pseudoStatements} FROM s_cms_support cmsupport
					LEFT JOIN s_cms_support_attributes cmsupporta
					ON cmsupporta.cmsSupportID = cmsupport.id
					";
		}
		
		$stmt = Shopware ()->Db ()->query ( $sql );
		
		$this->sendCsvWithTranslation ( $stmt, 'export.seo-urls.' . date ( "Y.m.d" ) . '.csv',$languages, $translationFields );
	}
	
	/**
	 *
	 * @param array $row
	 * @param array $languages
	 * @param array $translationFields
	 * @return array
	 */
	public function prepareArticleRowForSeo($row, $languages, $translationFields) {
	    if (! empty ( $languages )) {
			foreach ( $languages as $language ) {
			    $objectdata = "";
			 
				if (! empty ( $row ['mbdusSeoUrl_' . $language] )) {
				    if($this->is_serialized($row ['mbdusSeoUrl_' . $language])){
					   $objectdata = unserialize ( $row ['mbdusSeoUrl_' . $language] );
				    }
				    else{
				        $objectdata = array('mbdusSeoUrl'=>$row ['mbdusSeoUrl_' . $language]);
				    }
				} elseif (! empty ( $row ['detail_translation_' . $language] )) {
				    if($this->is_serialized($row ['detail_translation_' . $language])){
					   $objectdata = unserialize ( $row ['detail_translation_' . $language] );
				    }
				} else {
					continue;
				}
	         
        		foreach ( $objectdata as $key => $value ) {
        			if (isset ( $translationFields [$key] )) {
        		        $row [$translationFields [$key] . '_' . $language] = $value;
        			}
        		}
			}
	
			foreach ( $languages as $language ) {
				if (isset ( $row ['article_translation_' . $language] ))
					unset ( $row ['article_translation_' . $language] );
				if (isset ( $row ['detail_translation_' . $language] ))
					unset ( $row ['detail_translation_' . $language] );
			}
		}
		return $row;
	}
	
	/**
	 * Check if a string is serialized
	 * @param string $string
	 */
	private function is_serialized($string) {
	    return (@unserialize($string) !== false);
	}
	
	/**
	 *
	 * @param Zend_Db_Statement_Interface $stmt
	 * @param string $filename
	 */
	public function sendCsvWithTranslation(Zend_Db_Statement_Interface $stmt, $filename, $languages, $translationFields) {
		$this->Response ()->setHeader ( 'Content-Type', 'text/x-comma-separated-values;charset=utf-8' );
		$this->Response ()->setHeader ( 'Content-Disposition', sprintf ( 'attachment; filename="%s"', $filename ) );
		$this->Response ()->setHeader ( 'Content-Transfer-Encoding', 'binary' );
	
		$first = true;
		$keys = array ();
		while ( $row = $stmt->fetch () ) {
			$row = $this->prepareArticleRowForSeo( $row, $languages, $translationFields );
			if ($first) {
				$first = false;
				$keys = array_keys ( $row );
				echo "\xEF\xBB\xBF"; // UTF-8 BOM
				echo $this->_encode_line ( array_combine ( $keys, $keys ), $keys ) . "\r\n";
			}
			echo $this->_encode_line ( $row, $keys ) . "\r\n";
		}
	}
	
	public function _encode_line($line, $keys)
	{
		$csv = '';
	
		if (isset($this->sSettings['fieldmark'])) {
			$fieldmark = $this->sSettings['fieldmark'];
		} else {
			$fieldmark = '';
		}
		$lastkey = end($keys);
		foreach ($keys as $key) {
			if (!empty($line[$key])) {
				if (strpos($line[$key], "\r") !== false || strpos($line[$key], "\n") !== false || strpos(
						$line[$key], $fieldmark
				) !== false || strpos($line[$key], $this->sSettings['separator']) !== false
				) {
					$csv .= $fieldmark;
					if ($this->sSettings['encoding'] == 'UTF-8') {
						$line[$key] = utf8_decode($line[$key]);
					}
					if (!empty($fieldmark)) {
						$csv .= str_replace(
								$fieldmark, $this->sSettings['escaped_fieldmark'], $line[$key]
						);
					} else {
						$csv .= str_replace(
								$this->sSettings['separator'], $this->sSettings['escaped_separator'], $line[$key]
						);
					}
					$csv .= $fieldmark;
				} else {
					$csv .= $line[$key];
				}
			}
			if ($lastkey != $key) {
				$csv .= $this->sSettings['separator'];
			}
		}
	
		return $csv;
	}
	
	/**
	 * Import seo action
	 */
	public function importSeoUrlsAction() {
		try {
			@set_time_limit ( 0 );
			$this->Front ()->Plugins ()->Json ()->setRenderer ( false );
				
			if ($_FILES ['file'] ['error'] !== UPLOAD_ERR_OK) {
				echo json_encode ( array (
						'success' => false,
						'message' => "Could not upload file"
				) );
				return;
			}
				
			$fileName = basename ( $_FILES ['file'] ['name'] );
			$extension = strtolower ( pathinfo ( $fileName, PATHINFO_EXTENSION ) );
				
			if (! in_array ( $extension, array (
					'csv',
					'xml'
			) )) {
				echo json_encode ( array (
						'success' => false,
						'message' => 'Unknown Extension'
				) );
				return;
			}
				
			$destPath = Shopware ()->DocPath ( 'media_' . 'temp' );
			if (! is_dir ( $destPath )) {
				// Try to create directory with write permissions
				mkdir ( $destPath, 0777, true );
			}
				
			$destPath = realpath ( $destPath );
			if (! file_exists ( $destPath )) {
				echo json_encode ( array (
						'success' => false,
						'message' => sprintf ( "Destination directory '%s' does not exist.", $destPath )
				) );
				return;
			}
				
			if (! is_writable ( $destPath )) {
				echo json_encode ( array (
						'success' => false,
						'message' => sprintf ( "Destination directory '%s' does not have write permissions.", $destPath )
				) );
				return;
			}
				
			$filePath = tempnam ( $destPath, 'import_' );
				
			if (false === move_uploaded_file ( $_FILES ['file'] ['tmp_name'], $filePath )) {
				echo json_encode ( array (
						'success' => false,
						'message' => sprintf ( "Could not move %s to %s.", $_FILES ['file'] ['tmp_name'], $filePath )
				) );
				return;
			}
			$this->uploadedFilePath = $filePath;
			chmod ( $filePath, 0644 );
	
			$this->importSeo ( $filePath );
			return;
		} catch ( \Exception $e ) {
			// At this point any Exception would result in the import/export frontend "loading forever"
			// Append stack trace in order to be able to debug
			$message = $e->getMessage () . "<br />\r\nStack Trace:" . $e->getTraceAsString ();
			echo json_encode ( array (
					'success' => false,
					'message' => $message
			) );
			return;
		}
	}
	
	/**
	 *
	 * @param
	 *        	$filePath
	 */
	public function importSeo($filePath) {
		$results = $this->readCsvToArray($filePath,';');
		$updateCount = 0;
	
		$errors = array ();
	
		foreach ( $results as $seoData ) {
			$seoData = $this->toUtf8 ( $seoData );
			
			$sql ="SELECT id,articleID FROM s_articles_details WHERE ordernumber = ?";
			$row = Shopware()->Db()->fetchRow($sql,array($seoData ['ordernumber']));
			$articleID = $row['articleID'];
			$articledetailsID = $row['id'];
			
			$mbdusSeoUrl = "";
			$metaTitle = "";
			$description = "";
			$keywords = "";
			$keys = array_keys ( $seoData );
			foreach ( $keys as $key ) {
				if (strpos ( $key, '_' ) === false) {
					$value = $seoData [$key];
					
					if($key=='mbdusSeoUrl'){
						$mbdusSeoUrl = $value;
					}					
				}
				
				if (strpos ( $key, '_' ) !== false) {
					$keyArray = explode ( '_', $key );
					$languageId = $keyArray [1];
					$value = $seoData [$key];
					$name = $keyArray [0];
					
					if($name=='mbdusSeoUrl'){
						$sql = "SELECT objectdata FROM s_core_translations WHERE objectkey=? and objectlanguage=? and objecttype='article'";
						$data = Shopware ()->Db ()->fetchOne ( $sql, array (
								$articleID,
								$languageId
						) );
						if(!empty($data)){
							$data = unserialize($data);
							$data['mbdusSeoUrl'] = $value;
							$data = serialize($data);
								
							$sql = "UPDATE s_core_translations SET objectdata = ? WHERE objectkey=? and objectlanguage=? and objecttype='article'";
							Shopware ()->Db ()->query ( $sql, array (
									$data,
									$articleID,
									$languageId
							) );
						}
						else{
							$data['mbdusSeoUrl'] = $value;
							$data = serialize($data);
								
							if(!empty($value)){
								$sql = "INSERT INTO s_core_translations (objecttype,objectdata,objectkey,objectlanguage,dirty) VALUES (?,?,?,?,1)";
								Shopware ()->Db ()->query ( $sql, array (
										'article',
										$data,
										$articleID,
										$languageId
								) );
							}
						}
					}
				}
			}

			if($seoData['type']=='article'){
				$sql="UPDATE s_articles_attributes SET mbdus_seourl = ? WHERE articledetailsID = ?";
				Shopware()->Db()->query($sql, array($mbdusSeoUrl,$articledetailsID));
				$updateCount++;
			}
			
			if($seoData['type']=='category'){
				$id = $seoData['id'];
				$sql="UPDATE s_categories_attributes SET mbdus_seourl = ? WHERE categoryID = ?";
				Shopware()->Db()->query($sql, array($mbdusSeoUrl, $id));
				$updateCount++;
			}
			
			if($seoData['type']=='supplier'){
				$id = $seoData['id'];
				$sql = "SELECT id FROM s_articles_supplier_attributes WHERE supplierID = ?";
				$supplierExist = Shopware()->Db()->fetchOne($sql,$id);
				if(!empty($supplierExist)){
					$sql="UPDATE s_articles_supplier_attributes SET mbdus_seourl = ? WHERE supplierID = ?";
					Shopware()->Db()->query($sql, array($mbdusSeoUrl, $id));
					$updateCount++;
				}
				else{
					$sql="INSERT INTO s_articles_supplier_attributes (mbdus_seourl,supplierID) VALUES (?,?)";
					Shopware()->Db()->query($sql, array($mbdusSeoUrl, $id));
					$updateCount++;
				}
			}
			
			if($seoData['type']=='blogArticle'){
				$id = $seoData['id'];
				$sql = "SELECT id FROM s_blog_attributes WHERE blog_id = ?";
				$blogArticleExist = Shopware()->Db()->fetchOne($sql,$id);
				if(!empty($blogArticleExist)){
					$sql="UPDATE s_blog_attributes SET mbdus_seourl = ? WHERE blog_id = ?";
					Shopware()->Db()->query($sql, array($mbdusSeoUrl, $id));
					$updateCount++;
				}
				else{
					$sql="INSERT INTO s_blog_attributes (mbdus_seourl,blog_id) VALUES (?,?)";
					Shopware()->Db()->query($sql, array($mbdusSeoUrl, $id));
					$updateCount++;
				}
			}
			
			if($seoData['type']=='emotion'){
				$id = $seoData['id'];
				$sql = "SELECT id FROM s_emotion_attributes WHERE emotionID = ?";
				$emotionExist = Shopware()->Db()->fetchOne($sql,$id);
				if(!empty($emotionExist)){
					$sql="UPDATE s_emotion_attributes SET mbdus_seoUrl = ? WHERE emotionID = ?";
					Shopware()->Db()->query($sql, array($mbdusSeoUrl, $id));
					$updateCount++;
				}
				else{
					$sql="INSERT INTO s_emotion_attributes (mbdus_seourl,emotionID) VALUES (?,?)";
					Shopware()->Db()->query($sql, array($mbdusSeoUrl, $id));
					$updateCount++;
				}
			}
			
			if($seoData['type']=='site'){
				$id = $seoData['id'];
				$sql = "SELECT id FROM s_cms_static_attributes WHERE cmsStaticID = ?";
				$cmsStaticExist = Shopware()->Db()->fetchOne($sql,$id);
				if(!empty($cmsStaticExist)){
					$sql="UPDATE s_cms_static_attributes SET mbdus_seourl = ? WHERE cmsStaticID = ?";
					Shopware()->Db()->query($sql, array($mbdusSeoUrl, $id));
					$updateCount++;
				}
				else{
					$sql="INSERT INTO s_cms_static_attributes (mbdus_seourl,cmsStaticID) VALUES (?,?)";
					Shopware()->Db()->query($sql, array($mbdusSeoUrl, $id));
					$updateCount++;
				}
			}
			
			if($seoData['type']=='formular'){
				$id = $seoData['id'];
				$sql = "SELECT id FROM s_cms_support_attributes WHERE cmsSupportID = ?";
				$cmsSupportExist = Shopware()->Db()->fetchOne($sql,$id);
				if(!empty($cmsSupportExist)){
					$sql="UPDATE s_cms_support_attributes SET mbdus_seourl = ? WHERE cmsSupportID = ?";
					Shopware()->Db()->query($sql, array($mbdusSeoUrl, $id));
					$updateCount++;
				}
				else{
					$sql="INSERT INTO s_cms_support_attributes (mbdus_seourl,cmsSupportID) VALUES (?,?)";
					Shopware()->Db()->query($sql, array($mbdusSeoUrl, $id));
					$updateCount++;
				}
			}
		}
		if (! empty ( $errors )) {
			$message = implode ( "<br>\n", $errors );
			echo json_encode ( array (
					'success' => false,
					'message' => sprintf ( "Errors: $message" )
			) );
			return;
		}
	
		echo json_encode ( array (
				'success' => true,
				'message' => sprintf ( "Updated: %s.", $updateCount )
		) );
	
		return;
	}
	
	/**
	 *
	 * @param array $input        	
	 * @return array
	 */
	protected function toUtf8(array $input) {
		// detect whether the input is UTF-8 or ISO-8859-1
		array_walk_recursive ( $input, function (&$value) {
			// will fail, if special chars are encoded to latin-1
			// $isUtf8 = (utf8_encode(utf8_decode($value)) == $value);
			
			// might have issues with encodings other than utf-8 and latin-1
			$isUtf8 = (mb_detect_encoding ( $value, 'UTF-8', true ) !== false);
			if (! $isUtf8) {
				$value = utf8_encode ( $value );
			}
			return $value;
		} );
		
		return $input;
	}
	
	public function readCsvToArray($filename,$delimiter){
		$content = file_get_contents($filename);
		file_put_contents($filename, str_replace("\xEF\xBB\xBF",'', $content));
		if (($handle = fopen($filename, 'r')) === false) {
			throw new Exception("The file '$filename' cannot be opened");
		}
		$rows = array();
		$headers = fgetcsv($handle, 0, $delimiter);
		while (($line = fgetcsv($handle,0,$delimiter)) !== false) {
			$rows[] = array_combine($headers, $line);
		}
		return $rows;
	}
	
	/**
	 * Garbage-Collector
	 * Deletes uploaded file
	 */
	public function __destruct() {
		if (! empty ( $this->uploadedFilePath ) && file_exists ( $this->uploadedFilePath )) {
			@unlink ( $this->uploadedFilePath );
		}
	}
}
?>
