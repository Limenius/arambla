<?php

namespace Limenius\Arambla\Tests;

use Limenius\Arambla\DocumentComposer;
use Limenius\Arambla\Exception\ParseException;

class DocumentComposerTest extends \PHPUnit_Framework_TestCase
{
    public function testResolveIncludes()
    {
        $composer = new DocumentComposer();
        $composer->setRootDir(dirname(__FILE__).'/fixtures');
        $document = $composer->buildTree(join([
            '#%RAML 0.8',
            '---',
            'somefield: !include myfile.txt',
        ], "\n"));

        $expected  = [
            'somefield' => 'hola amigos'
            ];
        $this->assertEquals($expected, $document);
    }

    public function testResolveYamlIncludes()
    {
        $composer = new DocumentComposer();
        $composer->setRootDir(dirname(__FILE__).'/fixtures');
        $document = $composer->buildTree(join([
            '#%RAML 0.8',
            '---',
            'somefield: !include myincludefile.yaml',
        ], "\n"));

        $expected  = [
            'somefield' => ['first' => '1st', 'second' => '2nd' ]
            ];
        $this->assertEquals($expected, $document);
    }
}
