<?php

namespace Limenius\Arambla\Tests;

use Limenius\Arambla\Parser;
use Limenius\Arambla\Exception\ParseException;

class SchemasTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldFailWhenSchemasIsScalar()
    {
        $this->setExpectedException('Limenius\Arambla\Exception\ParseException');
        $parser = new Parser();
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: MyApi',
            'baseUri: http://myapi.com',
            'schemas: 42'
        ], "\n"));
    }

    public function testShouldFailWhenSchemasIsHash()
    {
        $this->setExpectedException('Limenius\Arambla\Exception\ParseException');
        $parser = new Parser();
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: MyApi',
            'baseUri: http://myapi.com',
            'schemas: { a: 1, 2: 3}'
        ], "\n"));
    }

    public function testShouldFailWhenSchemasIsNotAnArrayOfMaps()
    {
        $this->setExpectedException('Limenius\Arambla\Exception\ParseException');
        $parser = new Parser();
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: MyApi',
            'baseUri: http://myapi.com',
            'schemas:',
            '  foo:',
            '  bar:',
        ], "\n"));
    }

    public function testShouldFailWhenSchemasIsNull()
    {
        $this->setExpectedException('Limenius\Arambla\Exception\ParseException');
        $parser = new Parser();
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: MyApi',
            'baseUri: http://myapi.com',
            'schemas:',
            '  - foo:',
        ], "\n"));
    }

    public function testShouldFailWhenSchemaIsArray()
    {
        $this->setExpectedException('Limenius\Arambla\Exception\ParseException');
        $parser = new Parser();
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: MyApi',
            'baseUri: http://myapi.com',
            'schemas:',
            '  - foo: []',
        ], "\n"));
    }

    public function testShouldParseJsonSchema()
    {
        $parser = new Parser();
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: MyApi',
            'baseUri: http://myapi.com',
            'schemas:',
            '  - foo: |',
            '      {',
            '        "$schema": "http://json-schema.org/schema",',
            '        "type": "object",',
            '        "description": "A canonical song",',
            '        "properties": {',
            '          "title":  { "type": "string" },',
            '          "artist": { "type": "string" }',
            '        },',
            '        "required": [ "title", "artist" ]',
            '      }'

        ], "\n"));
        $expected  = [
            'title' => 'MyApi',
            'baseUri' => 'http://myapi.com',
            'protocols' => ['HTTP'],
            'schemas' => 
                ['foo' => 
                    [
                        'type' => 'object',
                        'description' => 'A canonical song',
                        'properties' => [
                            'title' =>  [ 'type' => 'string' ],
                            'artist' => [ 'type' => 'string' ]
                        ],
                        'required' => [
                            'title', 'artist'
                        ]
                    ]
                ]
            ] ;
        $this->assertEquals($expected, $document);
    }
}
