<?php namespace NetzpStaging\Models;
 
use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(name="netzp_staging_files",indexes={@Index(name="profile_idx", columns={"profileid"})})
 */
class StagingFile extends ModelEntity
{
    /**
     * @var integer $id
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer $profileid
     * @ORM\Column(type="integer")
     */
    private $profileid;

    /**
     * @var string $file
     * @ORM\Column(type="string", length=2048, nullable=true)
     */
    private $file;

    /**
     * @var boolean $copied
     * @ORM\Column(type="boolean")
     */
    private $copied;

    /**
     * @var integer $size
     * @ORM\Column(type="integer")
     */
    private $size;

	public function getId()
    {
        return $this->id;
    }

    public function setProfileid($value) { $this->profileid = $value; }
    public function getProfileid() { return $this->profileid; }

	public function setFile($value) { $this->file = $value; }
	public function getFile() { return $this->file; }

    public function setCopied($value) { $this->copied = $value; }
    public function getCopied() { return $this->copied; }

	public function setSize($value) { $this->size = $value; }
	public function getSize() { return $this->size; }
}