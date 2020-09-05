<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 *
 * @Shopware\noEncryption
 */

namespace NetiFoundation\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractClass
 *
 * @package NetiFoundation\Models
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class AbstractClass extends ModelEntity
{
}
