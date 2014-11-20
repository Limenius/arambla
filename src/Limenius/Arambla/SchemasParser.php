<?php

namespace Limenius\Arambla;

use Limenius\Arambla\Exception\ParseException;

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
            foreach ($schemaSpecArray as $schemaSpec) {
                $this->parseSingleSchema($schemaSpec);
            }
        }

        $this->specification['schemas'] = $schemasSpec;
        return $this->schemas;
    }

    public function parseSingleSchema($schemaSpec)
    {
        if ($schemaSpec === null) {
            throw new ParseException('schemas must not be empty');
        }
    }
}

