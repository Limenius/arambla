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
}
