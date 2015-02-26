<?php

namespace Devster\Frontmatter\Parser;

use Devster\Frontmatter\Exception\ParsingException;

class MarkdownParser extends Parser
{
    /**
     * @var ParsedownExtra
     */
    protected $parsedown;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->parsedown = new \ParsedownExtra();
    }

    /**
     * {@inheritdoc}
     */
    public function parse($content)
    {
        return $this->parsedown->text($content);
    }
}
