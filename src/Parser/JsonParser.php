<?php

namespace Devster\Frontmatter\Parser;

use Seld\JsonLint\JsonParser as JsonLintParser;
use Devster\Frontmatter\Exception\ParsingException;

class JsonParser extends Parser
{
    /**
     * {@inheritdoc}
     */
    public function parse($content)
    {
        try {
            $parser = new JsonLintParser();
            return $parser->parse($content, JsonLintParser::PARSE_TO_ASSOC);
        } catch (\Exception $e) {
            throw new ParsingException($this->getMetaParser()->getName(), null, $e);
        }
    }
}
