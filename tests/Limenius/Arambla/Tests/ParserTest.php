<?php

namespace Limenius\Arambla\Tests;

use Limenius\Arambla\Parser;
use Limenius\Arambla\Exception\ParseException;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldFailWhenMalformedStart()
    {
        $parser = new Parser();
        $this->setExpectedException('Limenius\Arambla\Exception\ParseException');
        $parser->load(join([
            'something',
            '#%RAML 0.8',
            '---',
            'title: MyApi'
        ], "\n"));

    }
    public function testShouldFailIfNoTitle()
    {
        $parser = new Parser();
        $this->setExpectedException('Limenius\Arambla\Exception\ParseException');
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'baseUri: http://myapi.com',
            '/:',
            '  displayName: Root'
        ], "\n"));
    }
    public function testShouldFailIfTitleIsNotString()
    {
        $parser = new Parser();
        $this->setExpectedException('Limenius\Arambla\Exception\ParseException');
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: ["title1", "title2"]',
            'baseUri: http://myapi.com',
            '/:',
            '  displayName: Root'
        ], "\n"));
    }

    public function testShouldSetHttpProtocolIfNotSpecified()
    {
        $parser = new Parser();
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: MyApi',
            'version: "1.0"',
        ], "\n"));

        $expected  = [
            'title' => 'MyApi',
            'version' => '1.0',
            'protocols' => ['HTTP'],
            ];
        $this->assertEquals($expected, $document);
    }

    public function testShouldParseVersion()
    {
        $parser = new Parser();
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: MyApi',
            'version: "1.0"',
        ], "\n"));
        $expected  = [
            'title' => 'MyApi',
            'version' => '1.0',
            'protocols' => [ 'HTTP' ]
            ];
        $this->assertEquals($expected, $document);
    }

    public function testShouldParseBasicUri()
    {
        $parser = new Parser();
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: MyApi',
            'baseUri: http://myapi.com',
            'version: "1.0"',
        ], "\n"));

        $expected  = [
            'title' => 'MyApi',
            'version' => '1.0',
            'baseUri' => 'http://myapi.com',
            'protocols' => [ 'HTTP' ],
            ];
        $this->assertEquals($expected, $document);
    }

    public function testShouldParseProtocolsExplicit()
    {
        $parser = new Parser();
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: MyApi',
            'baseUri: http://myapi.com',
            'protocols: [ HTTP, HTTPS ] ',
            'version: "1.0"'
        ], "\n"));

        $expected  = [
            'title' => 'MyApi',
            'version' => '1.0',
            'baseUri' => 'http://myapi.com',
            'protocols' => ['HTTP', 'HTTPS']
            ];
        $this->assertEquals($expected, $document);
    }

    public function testShouldFailIfProtocolNotHttpORHttps()
    {
        $parser = new Parser();
        $this->setExpectedException('Limenius\Arambla\Exception\ParseException');
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: MyApi',
            'baseUri: http://myapi.com',
            'protocols: [ HTTP, HTTPSO ] ',
            'version: "1.0"'
        ], "\n"));

        $this->assertEquals($expected, $document);
    }

    public function testShouldParseProtocolImplicit()
    {
        $parser = new Parser();
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: MyApi',
            'baseUri: http://myapi.com',
            'version: "1.0"'
        ], "\n"));

        $expected  = [
            'title' => 'MyApi',
            'version' => '1.0',
            'baseUri' => 'http://myapi.com',
            'protocols' => ['HTTP']
            ];
        $this->assertEquals($expected, $document);
    }

    public function testShouldParseMediaType()
    {
        $parser = new Parser();
        $document = $parser->load(join([
            '#%RAML 0.8',
            '---',
            'title: MyApi',
            'baseUri: http://myapi.com',
            'version: "1.0"',
            'mediaType: application/json'
        ], "\n"));

        $expected  = [
            'title' => 'MyApi',
            'version' => '1.0',
            'baseUri' => 'http://myapi.com',
            'protocols' => ['HTTP'],
            'mediaType' => 'application/json'
            ];
        $this->assertEquals($expected, $document);
    }


    //public function testShouldLoadBasicInformation()
    //{
    //    $parser = new Parser();
    //    $document = $parser->load(join([
    //        '#%RAML 0.8',
    //        '---',
    //        'title: MyApi',
    //        'baseUri: http://myapi.com',
    //        '/:',
    //        '  displayName: Root'
    //    ], "\n"));
    //    $expected  = [
    //        'title' => 'MyApi',
    //        'baseUri' => 'http://myapi.com',
    //        'resources' => [
    //            'displayName' => 'Root',
    //            'relativeUri' => '/',
    //            'relativeUriPathSegments' =>  []
    //            ],
    //        'protocols' => 'HTTP'
    //        ];
    //    $this->assertEquals($expected, $document);
    //}

}
