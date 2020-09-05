<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

/**
 * Class Shopware_Controllers_Backend_StuttSeoRedirects
 *
 * Backend controller
 *
 * @author STUTTGART MEDIA GmbH <shopware@stuttgartmedia.de>
 */
class Shopware_Controllers_Backend_StuttSeoRedirects extends Shopware_Controllers_Backend_Application
{
    protected $model = 'Shopware\CustomModels\Stutt\Redirect';
    protected $alias = 'redirect';

    /**
     * Helper function which creates the listing query builder.
     * If the class property model isn't configured, the init function throws an exception.
     * The listing alias for the from table can be configured over the class property alias.
     *
     * @return \Doctrine\ORM\QueryBuilder|\Shopware\Components\Model\QueryBuilder
     */
    protected function getListQuery()
    {
        $builder = $this->getManager()->createQueryBuilder();

        $builder->select(array('redirect.id as id, redirect.active as active, redirect.oldUrl as oldUrl, redirect.newUrl as newUrl, redirect.overrideShopUrl as overrideShopUrl, redirect.temporaryRedirect as temporaryRedirect, redirect.externalRedirect as externalRedirect, redirect.gone as gone, shop.name as shopName'))
            ->from($this->model, 'redirect');
        $builder->leftJoin('redirect.shop', 'shop');

        return $builder;
    }
    /**
     * Backend module detail query function
     *
     * @param $id
     *
     * @return \Doctrine\ORM\QueryBuilder|\Shopware\Components\Model\QueryBuilder
     */
    protected function getDetailQuery($id)
    {
        $builder = parent::getDetailQuery($id);

        $builder->leftJoin('redirect.shop', 'shop');
        $builder->addSelect(array('shop'));

        return $builder;
    }

    public function changeActiveStateAction()
    {
        try {
            $redirectId = $this->Request()->getParam('redirectId');

            /**@var \Shopware\CustomModels\Stutt\Redirect $redirect */
            $redirect = $this->getManager()->find(
                $this->model,
                $redirectId
            );

            $redirect->setActive(abs($redirect->getActive() - 1));

            $this->getManager()->flush($redirect);

            $this->View()->assign(array('success' => TRUE));
        } catch (Exception $e) {
            $this->View()->assign(array(
                'success' => FALSE,
                'error' => $e->getMessage()
            ));
        }
    }

    public function exportAction() {

        try {
            $csvFormat = $this->Request()->getPost('csv_format');
            $separatorChar = $this->Request()->getPost('separator_char');
            $firstLineHasHeadings = ($this->Request()->getPost('first_line_has_headings') == 'true');

            $csvData = [];
            if ($firstLineHasHeadings) {
                switch ($csvFormat) {
                    case 6:
                        $csvData[] = implode($separatorChar, ['active', 'oldUrl', 'newUrl', 'overrideShopwareUrl', 'temporaryRedirect', 'externalTarget']);
                        break;
                    case 5:
                        $csvData[] = implode($separatorChar, ['active', 'oldUrl', 'newUrl', 'overrideShopwareUrl', 'temporaryRedirect']);
                        break;
                    case 4:
                        $csvData[] = implode($separatorChar, ['active', 'oldUrl', 'newUrl', 'temporaryRedirect']);
                        break;
                    case 3:
                        $csvData[] = implode($separatorChar, ['active', 'oldUrl', 'newUrl']);
                        break;
                    case 2:
                    default:
                        $csvData[] = implode($separatorChar, ['oldUrl', 'newUrl']);
                        break;
                }
            }

            foreach (Shopware()->Db()->fetchAll('SELECT * FROM s_stutt_redirect') as $redirect) {
                $csvLine = [];
                switch ($csvFormat) {
                    case 6:
                        $csvLine[] = (int) $redirect['active'];
                        $csvLine[] = '"' . $redirect['oldUrl'] . '"';
                        $csvLine[] = '"' . $redirect['newUrl'] . '"';
                        $csvLine[] = (int) $redirect['overrideShopwareUrl'];
                        $csvLine[] = (int) $redirect['temporaryRedirect'];
                        $csvLine[] = (int) $redirect['externalRedirect'];
                        break;
                    case 5:
                        $csvLine[] = (int) $redirect['active'];
                        $csvLine[] = '"' . $redirect['oldUrl'] . '"';
                        $csvLine[] = '"' . $redirect['newUrl'] . '"';
                        $csvLine[] = (int) $redirect['overrideShopwareUrl'];
                        $csvLine[] = (int) $redirect['temporaryRedirect'];
                        break;
                    case 4:
                        $csvLine[] = (int) $redirect['active'];
                        $csvLine[] = '"' . $redirect['oldUrl'] . '"';
                        $csvLine[] = '"' . $redirect['newUrl'] . '"';
                        $csvLine[] = (int) $redirect['temporaryRedirect'];
                        break;
                    case 3:
                        $csvLine[] = (int) $redirect['active'];
                        $csvLine[] = '"' . $redirect['oldUrl'] . '"';
                        $csvLine[] = '"' . $redirect['newUrl'] . '"';
                        break;
                    case 2:
                    default:
                        $csvLine[] = '"' . $redirect['oldUrl'] . '"';
                        $csvLine[] = '"' . $redirect['newUrl'] . '"';
                        break;
                }
                $csvData[] = implode($separatorChar, $csvLine);
            }

            $this->View()->assign([
                'success' => TRUE,
                'data' => implode(PHP_EOL, $csvData)
            ]);
            return;
        }
        catch (\Exception $e) {
            $this->View()->assign([
                'success' => FALSE,
                'message' => $e->getMessage()
            ]);
            return;
        }
    }

    public function importAction() {

        $csvFormat = $this->Request()->getPost('csv_format');
        $separatorChar = $this->Request()->getPost('separator_char');
        $firstLineHasHeadings = ($this->Request()->getPost('first_line_has_headings') == 'true');
        $overwriteExisting = ($this->Request()->getPost('overwrite_existing') == 'true');

        $fileBag = new FileBag($_FILES);
        /** @var $file UploadedFile */
        $file = $fileBag->get('csv_file');
        $information = pathinfo($file->getClientOriginalName());

        if ($information['extension'] !== 'csv') {
            $this->View()->assign([
                'success' => FALSE,
                'message' => 'Falsche Dateiendung ' . $information['extension'] . '. Bitte laden Sie eine CSV-Datei hoch.'
            ]);
            unlink($file->getPathname());
            unlink($file);
            return;
        }

        $sql = array();
        $csv = @file($file->getPathname());
        unlink($file->getPathname());
        unlink($file);
        $i = 0;

        if (($firstLineHasHeadings && count($csv) <= 1) || count($csv) == 0) {
            $this->View()->assign([
                'success' => FALSE,
                'message' => 'Die Datei enthält zu wenige Zeilen.'
            ]);
            return;
        }
        if ($csvFormat != count(explode($separatorChar, $csv[0])) && $csvFormat != count(explode($separatorChar, $csv[0])) - 1) {
            $this->View()->assign([
                'success' => FALSE,
                'message' => 'Das Format der CSV-Datei ist nicht wie angegeben.'
            ]);
            return;
        }

        foreach ($csv as $csvLine) {
            $i++;
            if ($i == 1 && $firstLineHasHeadings) {
                continue;
            }
            $csvLineArray = explode($separatorChar, $csvLine);
            /*
             * id	oldUrl	newUrl	active	overrideShopUrl	temporaryRedirect
             */
            $active = 1;
            $temporaryRedirect = 0;
            $overrideShopUrl = 1;
            $externalRedirect = 0;
            switch ($csvFormat) {
                case 2:
                    $oldUrl = $this->cleanCsvUrl($csvLineArray[0]);
                    $newUrl = $this->cleanCsvUrl($csvLineArray[1]);
                    break;
                case 3:
                    $active = ((int) $csvLineArray[0] > 0) ? 1 : 0;
                    $oldUrl = $this->cleanCsvUrl($csvLineArray[1]);
                    $newUrl = $this->cleanCsvUrl($csvLineArray[2]);
                    break;
                case 4:
                    $active = ((int) $csvLineArray[0] > 0) ? 1 : 0;
                    $oldUrl = $this->cleanCsvUrl($csvLineArray[1]);
                    $newUrl = $this->cleanCsvUrl($csvLineArray[2]);
                    $temporaryRedirect = ((int) $csvLineArray[3] > 0) ? 1 : 0;
                    break;
                case 5:
                    $active = ((int) $csvLineArray[0] > 0) ? 1 : 0;
                    $oldUrl = $this->cleanCsvUrl($csvLineArray[1]);
                    $newUrl = $this->cleanCsvUrl($csvLineArray[2]);
                    $overrideShopUrl = ((int) $csvLineArray[3] > 0) ? 1 : 0;
                    $temporaryRedirect = ((int) $csvLineArray[4] > 0) ? 1 : 0;
                    break;
                case 6:
                    $active = ((int) $csvLineArray[0] > 0) ? 1 : 0;
                    $oldUrl = $this->cleanCsvUrl($csvLineArray[1]);
                    $newUrl = $this->cleanCsvUrl($csvLineArray[2]);
                    $overrideShopUrl = ((int) $csvLineArray[3] > 0) ? 1 : 0;
                    $temporaryRedirect = ((int) $csvLineArray[4] > 0) ? 1 : 0;
                    $externalRedirect = ((int) $csvLineArray[5] > 0) ? 1 : 0;
                    break;
                default:
                    $this->View()->assign([
                        'success' => FALSE,
                        'message' => 'Fehler: Unbekannte Formatauswahl.'
                    ]);
                    return;
            }

            if (strlen(trim($oldUrl)) == 0 || strlen(trim($newUrl)) == 0) {
                continue;
            }

            $sql = 'SELECT id FROM s_stutt_redirect WHERE oldUrl = ?';
            $existingRedirectObjectId = $this->get('db')->fetchOne($sql, array(
               $oldUrl
            ));
            if ((int) $existingRedirectObjectId > 0) {
                if ($overwriteExisting) {
                    $this->delete($existingRedirectObjectId);
                } else {
                    continue;
                }
            }

            $newRedirectObject = new \Shopware\CustomModels\Stutt\Redirect();
            $newRedirectObject->setActive($active);
            $newRedirectObject->setOldUrl($this->parseUrl($oldUrl));
            if ($externalRedirect) {
                $newRedirectObject->setNewUrl($newUrl);
            }
            else {
                $newRedirectObject->setNewUrl($this->parseUrl($newUrl));
            }
            $newRedirectObject->setOverrideShopUrl($overrideShopUrl);
            $newRedirectObject->setTemporaryRedirect($temporaryRedirect);
            $newRedirectObject->setExternalRedirect($externalRedirect);

            Shopware()->Models()->persist($newRedirectObject);
            Shopware()->Models()->flush();
        }

        $this->View()->assign(array(
            'success' => TRUE,
        ));
    }

    protected function cleanCsvUrl($url) {
        return trim($url, "\" \t\n\r\0\x0B");
    }

    /**
     * Contains the logic to create or update an existing record.
     * If the passed $data parameter contains a filled "id" property,
     * the function executes an entity manager find query for the configured
     * model and the passed id. If the $data parameter contains no id property,
     * this function creates a new instance of the configured model.
     *
     * If you have some doctrine association in your model, or you want
     * to modify the passed data object, you can use the { @link #resolveExtJsData } function
     * to modify the data property.
     *
     * You can implement \Symfony\Component\Validator\Constraints asserts in your model
     * which will be validate in the save process.
     * If the asserts throws an exception or some fields are invalid, the function returns
     * an array like this:
     *
     * array(
     *      'success' => false,
     *      'violations' => array(
     *          array(
     *              'message' => 'Property can not be null',
     *              'property' => 'article.name'
     *          ),
     *          ...
     *      )
     * )
     *
     * If the save process was successfully, the function returns a success array with the
     * updated model data.
     *
     * @param $data
     * @return array
     */
    public function save($data)
    {
        try {
            /**@var $model \Shopware\Components\Model\ModelEntity */
            if (!empty($data['id'])) {
                $model = $this->getRepository()->find($data['id']);
            } else {
                $model = new $this->model();
                $this->getManager()->persist($model);
            }

            $dataProcessed = $this->resolveExtJsData($data);
            if (!array_key_exists('shop_id', $data) || $data['shop_id'] == 0) {
                $dataProcessed['shop_id'] = NULL;
                $dataProcessed['shop'] = NULL;
            }
            else {
                $dataProcessed['shop'] = Shopware()->Models()->find('\Shopware\Models\Shop\Shop', $data['shop_id']);;
            }

            $dataProcessed['oldUrl'] = $this->parseUrl($dataProcessed['oldUrl']);
            if (!$data['externalRedirect']) {
                $dataProcessed['newUrl'] = $this->parseUrl($dataProcessed['newUrl']);
            }

            $model->fromArray($dataProcessed);

            $violations = $this->getManager()->validate($model);
            $errors = array();
            /** @var $violation Symfony\Component\Validator\ConstraintViolation */
            foreach ($violations as $violation) {
                $errors[] = array(
                    'message' => $violation->getMessage(),
                    'property' => $violation->getPropertyPath()
                );
            }

            if (!empty($errors)) {
                return array('success' => false, 'violations' => $errors);
            }

            $this->getManager()->flush();

            $detail = $this->getDetail($model->getId());

            return array('success' => true, 'data' => $detail['data']);
        } catch (Exception $e) {
            return array('success' => true, 'error' => $e->getMessage());
        }
    }

    protected function parseUrl($url) {

        $parsedUrl = parse_url($url);

        $path     = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query    = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';

        return "$path$query$fragment";

    }
}