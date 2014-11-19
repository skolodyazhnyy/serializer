<?php
/**
 * This file is part of the serializer project
 *
 * (c) Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 *
 */

namespace Bcn\Component\Serializer\Tests\Integration\Type;

use Bcn\Component\Serializer\Serializer\RootSerializer;
use Bcn\Component\Serializer\Serializer\SerializerInterface;
use Bcn\Component\Serializer\SerializerFactory;
use Bcn\Component\Serializer\Type\AbstractType;

class DocumentArrayType extends AbstractType
{
    /**
     * @param  SerializerFactory   $factory
     * @param  array               $options
     * @return SerializerInterface
     */
    public function getSerializer(SerializerFactory $factory, array $options = array())
    {
        return new RootSerializer('documents', $factory->create('array', array(
            'item_type' => 'document',
            'item_node' => 'document',
        )));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'document_array';
    }
}
