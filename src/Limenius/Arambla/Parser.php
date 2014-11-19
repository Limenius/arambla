<?php

namespace Limenius\Arambla;

use Limenius\Arambla\Exception\ParseException;

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
        if (!preg_match('/#%RAML 0.8/',trim($version))) {
            throw new ParseException('Document must start with #%RAML 0.8');
        }
        $document = \Symfony\Component\Yaml\Yaml::parse($input);

        $this->parseTitle($document);
        $this->parseVersion($document);
        $this->parseBaseUri($document);
        $this->parseProtocols($document);

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
                return;
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


}
