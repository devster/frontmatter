<?php

namespace Devster\Frontmatter\Exception;

class ParsingException extends Exception
{
    public function __construct($parser, $message = null, \Exception $previous = null, $code = 0)
    {
        $message = $message ?: ($previous ? $previous->getMessage() : "Unknown error");

        $message = sprintf('Parsing error from %s parser: %s', $parser, $message);

        parent::__construct($message, $code, $previous);
    }
}
