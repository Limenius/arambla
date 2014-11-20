<?php

namespace Limenius\Arambla;

use Limenius\Arambla\Exception\ParseException;
use JsonSchema\Validator;

class SchemasParser
{

    private $schemas;

    public function __construct()
    {
        $schemas = [];
    }

    public function parse($schemasSpec)
    {
        if (!is_array($schemasSpec)) {
            throw new ParseException('schemas must be an array');
        }
        // TODO: check for the empty {} case
        if ($schemasSpec !== array_values($schemasSpec)) {
            throw new ParseException('schemas must be a non-associative array');
        }

        foreach ($schemasSpec as $schemaSpecArray) {
            if (!is_array($schemaSpecArray)) {
                throw new ParseException('schemas must be an array of maps');
            }
            foreach ($schemaSpecArray as $key => $schemeSpec) {
                $this->schemas[$key] = $this->parseSingleSchema($schemeSpec);
            }
        }

        return $this->schemas;
    }

    public function parseSingleSchema($schemeSpec)
    {
        if ($schemeSpec === null) {
            throw new ParseException('scheme must not be empty');
        }
        if (!is_string($schemeSpec)) {
            throw new ParseException('scheme must be a string');
        }

        $schemaJson = json_decode($schemeSpec);
        if (json_last_error() == JSON_ERROR_NONE) {
            $schemeUri = $schemaJson->{'$schema'};
            $retriever = new \JsonSchema\Uri\UriRetriever;
            $scheme = $retriever->retrieve($schemeUri);
            $validator = new Validator();
            $validator->check($schemaJson, $scheme);
            if ($validator->isValid()) {
                $arrayScheme = json_decode($schemeSpec, true);
                unset($arrayScheme['$schema']);
                return $arrayScheme;
            } else {
                foreach ($validator->getErrors() as $error) {
                    echo sprintf("[%s] %s\n", $error['property'], $error['message']);
                }
            }
        }

    }

    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
