<?php

namespace Limenius\Arambla;

class Raml
{
    public static function load($input)
    {
        $file = '';
        if (strpos($input, "\n") === false && is_file($input)) {
            if (false === is_readable($input)) {
                throw new ParseException(sprintf('Unable to parse "%s" as the file is not readable.', $input));
            }
            $file = $input;
            $input = file_get_contents($file);
        }
        $parser = new Parser();
        return $parser->load($input);
    }
}
