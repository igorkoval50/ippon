<?php namespace NetzpStaging\Models;
 
use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(name="netzp_staging_keystore")
 */
class KeyStore extends ModelEntity
{
    /**
     * @var string $cachekey
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cachekey;

    /**
     * @var text $value
     * @ORM\Column(type="text", nullable=true)
     */
    private $value;

	public function getCachekey($value) { $this->cachekey = $value; }
	public function setCachekey() { return $this->cachekey; }

    public function setValue($value) { $this->value = $value; }
    public function getValue() { return $this->value; }
}