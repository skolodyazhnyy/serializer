<?php
/**
 * This file is part of the serializer project
 *
 * (c) Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 *
 */

namespace Bcn\Component\Serializer\Tests;

use Bcn\Component\Serializer\Type\TextType;
use Bcn\Component\Serializer\Type\TypeFactory;
use Bcn\Component\Serializer\Tests\Type\DocumentType;

class DocumentNormalizerTest extends TestCase
{
    /**
     * Normalize Document
     */
    public function testDocumentNormalize()
    {
        $document = $this->getNestedDocumentObject();

        $normalizer = $this->getFactory()
            ->create('document');

        $data = $normalizer->normalize($document);

        $this->assertEquals($this->getNestedDocumentData(), $data);
    }

    /**
     * Denormalize Document
     */
    public function testDocumentDenormalize()
    {
        $data = $this->getNestedDocumentData();

        $normalizer = $this->getFactory()
            ->create('document');

        $document = $normalizer->denormalize($data);

        $this->assertEquals($this->getNestedDocumentObject(), $document);
    }

    /**
     * @return TypeFactory
     * @throws \Exception
     */
    protected function getFactory()
    {
        $factory = new TypeFactory();
        $factory->addType(new TextType());
        $factory->addType(new DocumentType());

        return $factory;
    }
}