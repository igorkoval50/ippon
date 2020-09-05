<?php

namespace MagNewsletterBox\Models;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="s_plugin_mag_emarketing_voucher_codes")
 */
class ReservedCodes extends ModelEntity
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
     * @var string $voucherCodeID
     *
     * @ORM\Column()
     */
    private $voucherCodeID;

    /**
     * @var string $email
     *
     * @ORM\Column()
     */
    private $email;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getVoucherCodeID(): string
    {
        return $this->voucherCodeID;
    }

    /**
     * @param string $voucherCodeID
     */
    public function setVoucherCodeID(string $voucherCodeID): void
    {
        $this->voucherCodeID = $voucherCodeID;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
