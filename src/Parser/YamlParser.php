<?php

namespace Devster\Frontmatter\Parser;

use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use Devster\Frontmatter\Exception\ParsingException;

class YamlParser extends Parser
{
    /**
     * {@inheritdoc}
     */
    public function parse($content)
    {
        try {
            return SymfonyYaml::parse($content);
        } catch (\Exception $e) {
            throw new ParsingException($this->getMetaParser()->getName(), null, $e);
        }
    }
}
