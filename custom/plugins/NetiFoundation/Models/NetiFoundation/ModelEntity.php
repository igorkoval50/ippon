<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 *
 * @Shopware\noEncryption
 */

namespace NetiFoundation\Models;

use ArrayAccess;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity as SwModelEntity;

/**
 * Class AbstractClass
 *
 * @package NetiFoundation\Models
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class ModelEntity extends SwModelEntity implements ArrayAccess
{
    /**
     * @const ILLEGAL_PARAMETER_TYPE
     */
    const ILLEGAL_PARAMETER_TYPE = 1381832939;

    /**
     * @const REQUIRED_KEY_NOT_FOUND
     */
    const REQUIRED_KEY_NOT_FOUND = 1381832965;

    /**
     * @const READ_ONLY_ACCESS
     */
    const READ_ONLY_ACCESS = 1387649760;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="bigint", name="uid", options={"unsigned"=true})
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="create_date", type="datetime")
     *
     * @var \DateTime
     */
    protected $createDate;

    /**
     * @ORM\Column(name="change_date", type="datetime")
     *
     * @var \DateTime
     */
    protected $changeDate;

    /**
     * The disabled-flag
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="disabled")
     */
    protected $disabled = false;

    /**
     * The deleted-flag
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="deleted")
     */
    protected $deleted = false;

    /**
     * Member to cache the own class name.
     *
     * @var string
     */
    protected $className = null;

    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        /** @var \DateTime $date */
        $date = new \DateTime();
        $this->setCreateDate($date);
        $this->setChangeDate($date);
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setChangeDate(new \DateTime());
    }

    /**
     * Gets the value of uid from the record
     *
     * @return int
     */
    public function getUid()
    {
        return $this->getId();
    }

    /**
     * Sets the Value to uid in the record
     *
     * @param mixed $uid
     *
     * @return self
     */
    public function setUid($uid)
    {
        return $this->setId($uid);
    }

    /**
     * Gets the value of id from the record
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the Value to id in the record
     *
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of createDate from the record
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Sets the Value to createDate in the record
     *
     * @param \DateTime $createDate
     *
     * @return self
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Gets the value of changeDate from the record
     *
     * @return \DateTime
     */
    public function getChangeDate()
    {
        return $this->changeDate;
    }

    /**
     * Sets the Value to changeDate in the record
     *
     * @param \DateTime $changeDate
     *
     * @return self
     */
    public function setChangeDate($changeDate)
    {
        $this->changeDate = $changeDate;

        return $this;
    }

    /**
     * @param string $key
     * @param null   $fallback
     *
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function get($key, $fallback = null)
    {
        if (! is_string($key)) {
            throw new \BadMethodCallException(
                'Illegal type of parameter given.',
                self::ILLEGAL_PARAMETER_TYPE
            );
        }

        $getter     = 'get' . ucfirst($key);
        $reflection = $this->reflection(true);

        if ($reflection->hasMethod($getter)
            && $reflection->getMethod($getter)->isPublic()
        ) {
            return $this->$getter();
        }

        return $fallback;
    }

    /**
     * @param string $key
     *
     * @return array|null
     * @throws \BadMethodCallException
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     * @throws \BadMethodCallException
     */
    public function offsetExists($offset)
    {
        return null !== $this->get($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return array|null
     * @throws \BadMethodCallException
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param bool   $nullable
     * @param bool   $raw
     *
     * @return $this
     * @throws \BadMethodCallException
     */
    public function set($key, $value, $nullable = true, $raw = false)
    {
        if (! is_string($key)) {
            throw new \BadMethodCallException(
                'Illegal type of key given.',
                self::ILLEGAL_PARAMETER_TYPE
            );
        }
        if (! $nullable && null === $value) {
            throw new \BadMethodCallException(
                'Illegal type of value given.',
                self::ILLEGAL_PARAMETER_TYPE
            );
        }
        $setter     = 'set' . ucfirst($key);
        $reflection = $this->reflection(true);
        if ($reflection->hasMethod($setter)
            && $reflection->getMethod($setter)->isPublic()
        ) {
            if (false === $raw) {
                $this->$setter($value);
            } else {
                $this->$key = $value;
            }

            return $this;
        }
        throw new \BadMethodCallException(
            sprintf(
                'Required key "%s" not found.',
                $key
            ),
            self::REQUIRED_KEY_NOT_FOUND
        );
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return ModelEntity
     * @throws \BadMethodCallException
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * This check whether or not $getGetter() or $isGetter() exists
     * (if it does not exist, it's assumed that the property also does not exist)
     * and returns a non-null value.
     * So NULL will cause it to return false,
     * as you would expect after reading the php manual for isset().
     *
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        $key = ucfirst($key);
        $getGetter = 'get' . $key;
        $isGetter  = 'is' . $key;

        return (method_exists($this, $getGetter) && null !== $this->$getGetter())
            || (method_exists($this, $isGetter) && null !== $this->$isGetter());
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws \BadMethodCallException
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException(
            sprintf(
                'Array access of class "%s" is read-only.',
                $this->getClassName()
            ),
            self::READ_ONLY_ACCESS
        );
    }

    /**
     * @param mixed $offset
     * @throws \BadMethodCallException
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException(
            sprintf(
                'Array access of class "%s" is read-only.',
                $this->getClassName()
            ),
            self::READ_ONLY_ACCESS
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
    }

    /**
     * Getter method to instantiate the reflection object of the current object.
     *
     * @param boolean $noCache optional Disable the use of cache mechanism
     *
     * @return \ReflectionClass The reflection object
     */
    public function reflection($noCache = false)
    {
        $reflection = $this->reflection;
        if (($noCache = (bool) $noCache) || null === $reflection) {
            $reflection = new \ReflectionClass($this->getClassName());
            if ($noCache) {
                return $reflection;
            }
            $this->reflection = $reflection;
        }

        return $this->reflection;
    }

    /**
     * Method to get the class name of the current object.
     *
     * @return string The class name
     */
    public function getClassName()
    {
        if (null === $this->className) {
            $this->className = get_class($this);
        }

        return $this->className;
    }

    /**
     * @param bool $saveImmediately
     *
     * @return bool
     */
    public function store($saveImmediately = true)
    {
        $date = new \DateTime;
        $this->setChangeDate($date);

        try {
            try {
                $this->getEntityManager()->persist($this);
                if ($saveImmediately) {
                    $this->getEntityManager()->flush();
                }

                return true;
            } catch (DBALException $e) {
                //$this->getEntityManager()->rollback();
                $this->setException($e);

                return false;
            }
        } catch (ORMException $e) {
            $this->setException($e);

            //$this->getEntityManager()->rollback();
            return false;
        }
    }

    /**
     * @param bool $saveImmediately
     *
     * @return $this
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function deleteRow($saveImmediately = true)
    {
        $this->getEntityManager()->remove($this);
        if ($saveImmediately) {
            $this->getEntityManager()->flush();
        }

        return $this;
    }

    /**
     * @return \Shopware\Components\Model\ModelManager
     */
    protected function getEntityManager()
    {
        return Shopware()->Models();
    }

    /**
     * @param $stream
     *
     * @return resource|string
     */
    protected function getContentFromStream($stream)
    {
        $data = $stream;
        if (is_resource($stream)) {
            $data = stream_get_contents($stream);
            if ($data && $this->isSerialized($data)) {
                $data = unserialize($data);
            }
            rewind($stream);
        } elseif (is_string($stream)) {
            if ($stream && $this->isSerialized($stream)) {
                $data = unserialize($stream);
            }
        }

        return $data;
    }

    /**
     * @param string $value
     * @param null   $result
     *
     * @return bool
     */
    protected function isSerialized($value, &$result = null)
    {
        // Bit of a give away this one
        if (! is_string($value)) {
            return false;
        }
        // Serialized false, return true. unserialize() returns false on an
        // invalid string or it could return false if the string is serialized
        // false, eliminate that possibility.
        if ($value === 'b:0;') {
            $result = false;

            return true;
        }
        $length = strlen($value);
        $end    = '';
        switch ($value[0]) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case 's':
                if ($value[$length - 2] !== '"') {
                    return false;
                }
            case 'b':
            case 'i':
                /** @noinspection PhpMissingBreakStatementInspection */
            case 'd':
                // This looks odd but it is quicker than isset()ing
                $end .= ';';
            case 'a':
                /** @noinspection PhpMissingBreakStatementInspection */
            case 'O':
                $end .= '}';
                if ($value[1] !== ':') {
                    return false;
                }
                switch ($value[2]) {
                    case 0:
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        break;
                    default:
                        return false;
                }
            case 'N':
                $end .= ';';
                if ($value[$length - 1] !== $end[0]) {
                    return false;
                }
                break;
            default:
                return false;
        }
        if (($result = @unserialize($value)) === false) {
            $result = null;

            return false;
        }

        return true;
    }

    /**
     * Gets the value of exception from the record
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Sets the Value to exception in the record
     *
     * @param \Exception $exception
     *
     * @return self
     */
    public function setException($exception)
    {
        $this->exception = null;

        if ($exception instanceof \Exception) {
            $this->exception = $exception;
        }

        return $this;
    }

    /**
     * Gets the value of disabled from the record
     *
     * @return boolean
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * Sets the Value to disabled in the record
     *
     * @param boolean $disabled
     *
     * @return self
     */
    public function setDisabled($disabled)
    {
        $this->disabled = (boolean) $disabled;

        return $this;
    }

    /**
     * Gets the value of deleted from the record
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Sets the Value to deleted in the record
     *
     * @param boolean $deleted
     *
     * @return self
     */
    public function setDeleted($deleted)
    {
        $this->deleted = (boolean) $deleted;

        return $this;
    }
}

