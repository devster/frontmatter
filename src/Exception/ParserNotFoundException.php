<?php

namespace Devster\Frontmatter\Exception;

class ParserNotFoundException extends Exception
{
    public function __construct($parser, $type, \Exception $previous = null, $code = 0)
    {
        $message = sprintf('Parser "%s" not found for %s parsing', $parser, $type);

        parent::__construct($message, $code, $previous);
    }
}
