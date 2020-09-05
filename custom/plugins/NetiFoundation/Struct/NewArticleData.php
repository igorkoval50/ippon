<?php
/**
 * Copyright notice
 *
 * (c) 2009-2017 Net Inventors - Agentur für digitale Medien GmbH
 * All rights reserved
 *
 * This script is part of the Spirit-Project.
 * The Spirit-Project is property of the Net Inventors GmbH and
 * may not be used in projects not related to the Net Inventors
 * without explicit permission by the authors.
 *
 * PHP version 5
 *
 * @package    NetiFoundation
 * @subpackage NetiFoundation/NewArticleData.php
 * @author     bmueller
 * @copyright  2017 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */

namespace NetiFoundation\Struct;

/**
 * Class NewArticleData
 *
 * @package NetiFoundation\Struct
 */
class NewArticleData extends AbstractClass
{
    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $autoNumber;

    /**
     * Gets the value of number from the record
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Gets the value of autoNumber from the record
     *
     * @return string
     */
    public function getAutoNumber()
    {
        return $this->autoNumber;
    }
}
