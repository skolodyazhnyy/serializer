<?php
/**
 * This file is part of the serializer project
 *
 * (c) Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 *
 */

namespace Bcn\Component\Serializer;

use Bcn\Component\Serializer\Decoder\DecoderInterface;
use Bcn\Component\Serializer\Encoder\EncoderInterface;
use Bcn\Component\Serializer\Type\TypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Bcn\Component\Serializer\Serializer\SerializerInterface;
use Bcn\Component\Serializer\Type\Extension\TypeExtensionInterface;

class SerializerFactory
{
    /** @var TypeInterface[] */
    protected $types = array();

    /**
     * @param  TypeInterface|string $type
     * @param  array                $options
     * @return SerializerInterface
     */
    public function create($type, array $options = array())
    {
        $optionsResolver = new OptionsResolver();

        $resolved = $this->resolveType($type);
        $resolved->setDefaultOptions($optionsResolver);

        return $resolved->getSerializer($this, $optionsResolver->resolve($options));
    }

    /**
     * @param  mixed            $data
     * @param  EncoderInterface $encoder
     * @param  string           $type
     * @param  array            $options
     * @return EncoderInterface
     */
    public function serialize($data, EncoderInterface $encoder, $type, array $options = array())
    {
        return $this->create($type, $options)->serialize($data, $encoder);
    }

    /**
     * @param  string           $type
     * @param  array            $options
     * @param  DecoderInterface $decoder
     * @param  mixed            $object
     * @return object
     */
    public function unserialize(DecoderInterface $decoder, $type, array $options = array(), &$object = null)
    {
        return $this->create($type, $options)->unserialize($decoder, $object);
    }

    /**
     * @param  TypeInterface $type
     * @return $this
     * @throws \Exception
     */
    public function addType(TypeInterface $type)
    {
        $name = $type->getName();
        if ($this->hasType($name)) {
            throw new \Exception("Type \"$name\" already defined");
        }

        $this->types[$name] = $type;

        return $this;
    }

    /**
     * @param  string        $type
     * @return TypeInterface
     * @throws \Exception
     */
    public function getType($type)
    {
        $name = $this->resolveTypeName($type);

        if (!isset($this->types[$name])) {
            throw new \Exception("Type \"$type\" is not defined");
        }

        return $this->types[$name];
    }

    /**
     * @param  TypeInterface|string $type
     * @return bool
     */
    public function hasType($type)
    {
        return isset($this->types[$this->resolveTypeName($type)]);
    }

    /**
     * @param  TypeExtensionInterface $extension
     * @throws \Exception
     */
    public function extend(TypeExtensionInterface $extension)
    {
        foreach ($extension->getTypes() as $type) {
            $this->addType($type);
        }
    }

    /**
     * @param $type
     * @return string
     */
    protected function resolveTypeName($type)
    {
        return $type instanceof TypeInterface ? $type->getName() : $type;
    }

    /**
     * @param $type
     * @throws \InvalidArgumentException
     * @return TypeInterface
     */
    protected function resolveType($type)
    {
        if ($type instanceof TypeInterface) {
            return $type;
        }

        if ($this->hasType($type)) {
            return $this->getType($type);
        }

        throw new \InvalidArgumentException("Type \"$type\" is not registered");
    }
}