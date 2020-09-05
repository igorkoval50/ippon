<?php
/**
 * @copyright  Copyright (c) 2016, Net Inventors GmbH
 * @category   Shopware
 * @author     Net Inventors GmbH
 */

namespace NetiFoundation\Struct;

use BadMethodCallException;

/**
 * Class AbstractClass
 *
 * @package NetiFoundation\Struct
 */
abstract class AbstractClass implements \Iterator, StructInterface
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
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var array
     */
    protected $_data;

    /**
     * @param array $data
     * @param bool  $camelize
     */
    public function __construct(array $data, $camelize = true)
    {
        if (is_array($data)) {
            foreach ($data as $name => $value) {
                if (true === $camelize) {
                    $name = $this->camelize($name);
                }
                $this->set($name, $value);
            }
        }
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param bool   $nullable
     *
     * @return $this
     * @throws BadMethodCallException
     */
    public function set($name, $value, $nullable = true)
    {
        if (! is_string($name) || '' === $name) {
            throw new BadMethodCallException(
                'Illegal type of key given.',
                self::ILLEGAL_PARAMETER_TYPE
            );
        }
        if (! $nullable && null === $value) {
            throw new BadMethodCallException(
                'Illegal type of value given.',
                self::ILLEGAL_PARAMETER_TYPE
            );
        }
        $setter     = 'set' . ucfirst($name);
        $reflection = $this->reflection();
        if ($reflection->hasMethod($setter) && ! $reflection->getMethod($setter)->isPrivate()) {
            $this->$setter($value);

            return $this;
        }

        if ($reflection->hasProperty($name) && ! $reflection->getProperty($name)->isPrivate()) {
            $this->$name = $value;

            return $this;
        }

        return $this;
    }

    /**
     * @param string     $name
     * @param null|mixed $fallback
     *
     * @return mixed
     */
    public function get($name, $fallback = null)
    {
        if (! is_string($name)) {
            throw new BadMethodCallException(
                'Illegal type of parameter given.',
                self::ILLEGAL_PARAMETER_TYPE
            );
        }

        $getter     = 'get' . $name;
        $reflection = $this->reflection();

        if ($reflection->hasMethod($getter)
            && $reflection->getMethod($getter)->isPublic()
        ) {
            return $this->$getter();
        }

        // boolean fields.
        $getter     = 'is' . $name;

        if ($reflection->hasMethod($getter)
            && $reflection->getMethod($getter)->isPublic()
        ) {
            return $this->$getter();
        }

        return $fallback;
    }

    /**
     * @param bool $noCache
     *
     * @return array
     */
    public function toArray($noCache = false)
    {
        $data = $this->_data;
        if (($noCache = (bool) $noCache) || null === $data) {
            $this->_data          = [];
            $reflection           = $this->reflection();
            $reflectionProperties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);

            foreach ($reflectionProperties as $reflectionProperty) {
                if ($this->getClassName() === $reflectionProperty->getDeclaringClass()->getName()) {
                    $name  = $reflectionProperty->getName();
                    $value = $this->get($name);

                    if ($value instanceof AbstractClass) {
                        $value = $value->toArray($noCache);
                    }

                    if (is_array($value) && ! $this->isAssoc($value)) {
                        foreach ($value as &$item) {
                            if ($item instanceof AbstractClass) {
                                $item = $item->toArray($noCache);
                            }
                        }
                    }

                    $this->_data[$name] = $value;
                }
            }
        }

        return $this->_data;
    }

    /**
     * @param       $name
     * @param array $arguments
     */
    public function __call($name, array $arguments)
    {
        throw new \RuntimeException(sprintf('Call to undefined method "%s::%s"', $this->getClassName(), $name));
    }

    /**
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        throw new \RuntimeException(sprintf('Trying to set non-existing property "%s::%s"', $this->getClassName(), $property));
    }

    /**
     * @param $property
     */
    public function __get($property)
    {
        throw new \RuntimeException(sprintf('Trying to get non-existing property "%s::%s"', $this->getClassName(), $property));
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        if (null === $this->className) {
            $this->className = get_class($this);
        }

        return $this->className;
    }

    /**
     * @param bool $noCache
     *
     * @return \ReflectionClass
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
     * @param $var
     *
     * @return bool
     */
    protected function isAssoc($var)
    {
        return is_array($var) && array_diff_key($var, array_keys(array_keys($var)));
    }

    /**
     * @param        $input
     * @param string $separator
     *
     * @return string
     */
    protected function camelize($input, $separator = '_')
    {
        return lcfirst(str_replace($separator, '', ucwords($input, $separator)));
    }

    /**
     *
     */
    function rewind()
    {
        if (! $this->_data) {
            $this->toArray();
        }

        reset($this->_data);
    }

    /**
     * @return mixed
     */
    function current()
    {
        if (! $this->_data) {
            $this->toArray();
        }

        return current($this->_data);
    }

    /**
     * @return mixed
     */
    function key()
    {
        if (! $this->_data) {
            $this->toArray();
        }

        return key($this->_data);
    }

    /**
     * @return mixed
     */
    function next()
    {
        if (! $this->_data) {
            $this->toArray();
        }

        return next($this->_data);
    }

    /**
     * @return bool
     */
    function valid()
    {
        if (! $this->_data) {
            $this->toArray();
        }

        return ($this->current() !== false);
    }
}
