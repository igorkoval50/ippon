<?php namespace NetzpStaging\Models;
 
use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(name="netzp_staging_profiles")
 */
class StagingProfile extends ModelEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

	/**
     * @var datetime $createdfiles
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdfiles;

    /**
     * @var datetime $createddb
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createddb;

    /**
     * @var string $title
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @var string $dirname
     * @ORM\Column(type="string", nullable=true)
     */
    private $dirname;

    /**
     * @var text $dbconfig
     * @ORM\Column(type="text", nullable=true)
     */
    private $dbconfig;

    /**
     * @var text $settings
     * @ORM\Column(type="text", nullable=true)
     */
    private $settings;

    /**
     * @var text $dirsexcluded
     * @ORM\Column(type="text", nullable=true)
     */
    private $dirsexcluded;

    /**
     * @var text $dirsnotsynced
     * @ORM\Column(type="text", nullable=true)
     */
    private $dirsnotsynced;

    /**
     * @var boolean $runfromcron
     * @ORM\Column(type="boolean")
     */
    private $runfromcron;

    /**
     * @var integer $statusdb
     * @ORM\Column(type="integer")
     */
    private $statusdb;

    /**
     * @var integer $statusfiles
     * @ORM\Column(type="integer")
     */
    private $statusfiles;

    /**
     * @var integer $creationsfiles
     * @ORM\Column(type="integer")
     */
    private $creationsfiles;

    /**
     * @var integer $creationsdb
     * @ORM\Column(type="integer")
     */
    private $creationsdb;

	public function getId()
    {
        return $this->id;
    }

	public function setCreatedfiles($value) { $this->createdfiles = $value; }
	public function getCreatedfiles() { return $this->createdfiles; }

    public function setCreateddb($value) { $this->createddb = $value; }
    public function getCreateddb() { return $this->createddb; }

	public function setTitle($value) { $this->title = $value; }
	public function getTitle() { return $this->title; }

    public function setDirname($value) { $this->dirname = $value; }
    public function getDirname() { return $this->dirname; }

	public function setDbconfig($value) { $this->dbconfig = $value; }
	public function getDbconfig() { return $this->dbconfig; }

    public function setSettings($value) { $this->settings = $value; }
    public function getSettings() { return $this->settings; }

	public function setDirsexcluded($value) { $this->dirsexcluded = $value; }
	public function getDirsexcluded() { return $this->dirsexcluded; }

	public function setDirsnotsynced($value) { $this->dirsnotsynced = $value; }
	public function getDirsnotsynced() { return $this->dirsnotsynced; }

	public function setRunfromcron($value) { $this->runfromcron = $value; }
	public function getRunfromcron() { return $this->runfromcron; }

    public function setStatusdb($value) { $this->statusdb = $value; }
    public function getStatusdb() { return $this->statusdb; }

    public function setStatusfiles($value) { $this->statusfiles = $value; }
    public function getStatusfiles() { return $this->statusfiles; }

    public function setCreationsfiles($value) { $this->creationsfiles = $value; }
    public function getCreationsfiles() { return $this->creationsfiles; }

    public function setCreationsdb($value) { $this->creationsdb = $value; }
    public function getCreationsdb() { return $this->creationsdb; }
}