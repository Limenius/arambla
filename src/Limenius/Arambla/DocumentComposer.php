<?php

namespace Limenius\Arambla;

use Symfony\Component\Yaml\Yaml;

class DocumentComposer
{
    private $rootDir;

    public function __construct()
    {
        $this->rootDir = dirname(__FILE__);
    }

    public function setRootDir($dir)
    {
        $this->rootDir = $dir;
    }

    public function buildTree($document)
    {
        $spec = Yaml::parse($document);
        return $this->composeNode($spec);
    }

    private function composeNode($node)
    {
        if (is_array($node)) {
            $node = $this->composeArrayNode($node);
        } else {
            $node = $this->composeScalarNode($node);
        }

        return $node;
    }

    private function composeArrayNode($node)
    {
        foreach ($node as $key => $subnode) {
            $node[$key] = $this->composeNode($subnode);
        }
        return $node;
    }

    private function composeScalarNode($literal)
    {

        if (strpos($literal, '!include') === 0) {
            return $this->includeFile(str_replace('!include ', '', $literal));
        } else {
            return $literal;
        }
    }

    private function includeFile($filename)
    {
        $contents = rtrim(file_get_contents ($this->rootDir . '/'. $filename));
        if (in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), ['yml', 'yaml', 'rml', 'raml'])) {
            $contents = Yaml::parse($contents);
        }
        return $contents;
    }

}
