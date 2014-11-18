<?php
/**
 * This file is part of the serializer project
 *
 * (c) Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 *
 */

namespace Bcn\Component\Serializer\Tests\Encoder;

use Bcn\Component\Serializer\Encoder\XmlEncoder;
use Bcn\Component\Serializer\Tests\TestCase;

class XmlEncoderTest extends TestCase
{
    public function testEncodeStructure()
    {
        $data = '<document><first>one</first><second>two</second></document>';

        $encoder = new XmlEncoder($this->getDataStream(), "document");
        $encoder->node('first', 'scalar')->write('one')->end();
        $encoder->node('second', 'scalar')->write('two')->end();

        $this->assertXmlStringEqualsXmlString($data, $encoder->dump());
    }

    public function testEncodeArray()
    {
        $data = '<collection><item>one</item><item>two</item></collection>';

        $encoder = new XmlEncoder($this->getDataStream(), "collection");
        $encoder->node('item', 'scalar')->write('one')->end();
        $encoder->node('item', 'scalar')->write('two')->end();

        $this->assertXmlStringEqualsXmlString($data, $encoder->dump());
    }

    public function testEncodeScalar()
    {
        $encoder = new XmlEncoder($this->getDataStream(), "value");
        $encoder->write('foo');

        $this->assertXmlStringEqualsXmlString('<value>foo</value>', $encoder->dump());
    }

    public function testEncodeDocumentArray()
    {
        $encoder = new XmlEncoder($this->getDataStream(), "documents");
        $encoder
            ->node('document', 'object')
                ->node('name',        'scalar')->write('Test name one')->end()
                ->node('description', 'scalar')->write('Test description one')->end()
                ->node('rank',        'scalar')->write(11)->end()
                ->node('rating',      'scalar')->write(93.31)->end()
            ->end()
            ->node('document', 'object')
                ->node('name',        'scalar')->write('Test name two')->end()
                ->node('description', 'scalar')->write('Test description two')->end()
                ->node('rank',        'scalar')->write(11)->end()
                ->node('rating',      'scalar')->write(93.31)->end()
            ->end();

        $this->assertXmlStringEqualsXmlString(
            $this->getFixtureContent('resources/documents.xml'),
            $encoder->dump()
        );
    }
}
