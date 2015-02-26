<?php

namespace Devster\Frontmatter\Parser;

abstract class Parser
{
    /**
     * @var MetaParser
     */
    protected $metaParser;

    /**
     * Set metaParser
     *
     * @param  MetaParser $metaParser
     * @return self
     */
    public function setMetaParser($metaParser)
    {
        $this->metaParser = $metaParser;

        return $this;
    }

    /**
     * Get metaParser
     *
     * @return MetaParser
     */
    public function getMetaParser()
    {
        return $this->metaParser;
    }

    /**
     * Parse a string
     *
     * @param  string $content
     * @return string
     */
    abstract public function parse($content);
}
