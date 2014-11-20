<?php

namespace Limenius\Arambla;

use Limenius\Arambla\Exception\ParseException;
use Limenius\Arambla\SchemasParser;
use Limenius\Arambla\DocumentCompser;

class Parser
{
    private $specification;

    public function __construct()
    {
        $specificacion = [];
    }

    public function load($input)
    {
        $version = strtok($input, "\n");
        if (!preg_match('/#%RAML 0.8/', trim($version))) {
            throw new ParseException('Document must start with #%RAML 0.8');
        }
        $documentComposer = new DocumentComposer();
        $document = $documentComposer->buildTree($input);

        $this->parseTitle($document);
        $this->parseVersion($document);
        $this->parseBaseUri($document);
        $this->parseProtocols($document);
        $this->parseMediaType($document);
        $this->parseSchemas($document);

        return $this->specification;

    }

    private function parseTitle($document)
    {
        if (!array_key_exists('title', $document)) {
            throw new ParseException('Document must have a title');
        }
        if (!is_string($document['title'])) {
            throw new ParseException('Title must be a string');
        }

        $this->specification['title'] = $document['title'];
    }

    private function parseVersion($document)
    {
        if (!array_key_exists('version', $document)) {
            return;
        }
        $version = $document['version'];
        if (!is_numeric($version) && !is_string($version)) {
            throw new ParseException('Version must be a string');
        }

        $this->specification['version'] = (string)$version;
    }

    private function parseBaseUri($document)
    {
        //baseUri is only required during production, optinal during development
        if (!array_key_exists('baseUri', $document)) {
            return;
        }
        $baseUri = $document['baseUri'];
        if (!is_string($baseUri)) {
            throw new ParseException('baseUri must be a string');
        }

        $this->specification['baseUri'] = (string)$baseUri;
    }

    private function parseProtocols($document)
    {
        //baseUri is only required during production, optinal during development
        if (!array_key_exists('protocols', $document)) {
            if (array_key_exists('baseUri', $this->specification)) {
                $urlComponents = parse_url($this->specification['baseUri']);
                if (!array_key_exists('scheme', $urlComponents)) {
                    throw new ParseException('No valid protocols specified');
                }
                $guessed = strtoupper($urlComponents['scheme']);
                if ($guessed != 'HTTP' && $guessed != 'HTTPS') {
                    throw new ParseException('No valid protocols specified');
                }
                $protocols[] = $guessed;
            } else {
                $protocols[] = 'HTTP';
            }
        } else {
            $rawProtocols = $document['protocols'];
            if (!is_array($rawProtocols)) {
                throw new ParseException('protocols must be an array');
            }

            $protocols = array_intersect($rawProtocols, ['HTTP', 'HTTPS']);
            $notValid = array_diff($rawProtocols, ['HTTP', 'HTTPS']);
            if (!empty($notValid)) {
                throw new ParseException('HTTP and HTTPS are the only valid protocols');
            }
        }

        $this->specification['protocols'] = $protocols;
    }

    private function parseMediaType($document)
    {
        // TODO: Validate media type
        //baseUri is only required during production, optinal during development
        if (!array_key_exists('mediaType', $document)) {
            return;
        }
        $mediaType = $document['mediaType'];
        if (!is_string($mediaType)) {
            throw new ParseException('mediaType must be a string');
        }

        $this->specification['mediaType'] = $mediaType;
    }

    private function parseSchemas($document)
    {
        $schemasParser = new SchemasParser();
        if (!array_key_exists('schemas', $document)) {
            return;
        }
        $this->specification['schemas'] = $schemasParser->parse($document['schemas']);

    }
}
