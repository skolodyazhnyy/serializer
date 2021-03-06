<?php
/**
 * This file is part of the serializer project
 *
 * (c) Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 *
 */

namespace Bcn\Component\Serializer;

class Normalizer
{
    /**
     * @param  mixed        $origin
     * @param  Definition   $definition
     * @return array|string
     */
    public function normalize($origin, Definition $definition)
    {
        if ($definition->isArray()) {
            return $this->normalizeArray($origin, $definition);
        }

        if ($definition->isObject()) {
            return $this->normalizeObject($origin, $definition);
        }

        return $definition->extract($origin);
    }

    /**
     * @param  mixed      $origin
     * @param  Definition $definition
     * @return array
     */
    protected function normalizeObject(&$origin, Definition $definition)
    {
        $object = $definition->extract($origin);

        if ($object === null) {
            return null;
        }

        $normalized = array();
        foreach ($definition->getProperties() as $propertyName => $propertyDefinition) {
            $normalized[$propertyName] = $this->normalize($object, $propertyDefinition);
        }

        return $normalized;
    }

    /**
     * @param  mixed      $origin
     * @param  Definition $definition
     * @return array
     */
    protected function normalizeArray(&$origin, Definition $definition)
    {
        $collection = $definition->extract($origin);

        if (!is_array($collection) && !$collection instanceof \Traversable) {
            $collection = array();
        }

        $normalized = array();
        foreach ($collection as $index => $entry) {
            $normalized[$index] = $this->normalize($entry, $definition->getPrototype());
        }

        return $normalized;
    }

    /**
     * @param  mixed      $data
     * @param  Definition $definition
     * @param  mixed      $origin
     * @return null
     */
    public function denormalize($data, Definition $definition, &$origin = null)
    {
        if ($definition->isArray()) {
            $this->denormalizeArray($data, $definition, $origin);
        }

        if ($definition->isObject()) {
            $this->denormalizeObject($data, $definition, $origin);
        }

        if ($definition->isScalar()) {
            $definition->settle($origin, $data);
        }

        return $origin;
    }

    /**
     * @param mixed      $data
     * @param Definition $definition
     * @param mixed      $origin
     */
    protected function denormalizeArray($data, Definition $definition, &$origin)
    {
        $collection = ($origin !== null) ? $definition->extract($origin) : $definition->create($origin);
        $prototype = $definition->getPrototype();

        if (!is_array($data) && !$data instanceof \Traversable) {
            $data = array();
        }

        foreach ($data as $index => $entry) {
            $item = isset($collection[$index]) ? $collection[$index] : $prototype->create($origin);
            $definition->settleKey($item, $index);
            $collection[$index] = $this->denormalize($entry, $prototype, $item);
        }

        $definition->settle($origin, $collection);
    }

    /**
     * @param  mixed      $data
     * @param  Definition $definition
     * @param  mixed      $origin
     * @return array
     */
    protected function denormalizeObject($data, Definition $definition, &$origin)
    {
        if ($data === null) {
            $definition->settle($origin, null);

            return;
        }

        $object = $definition->extract($origin) ?: $definition->create($origin);

        foreach ($definition->getProperties() as $propertyName => $propertyDefinition) {
            $propertyData = isset($data[$propertyName]) ? $data[$propertyName] : null;
            $this->denormalize($propertyData, $propertyDefinition, $object);
        }

        $definition->settle($origin, $object);
    }
}
