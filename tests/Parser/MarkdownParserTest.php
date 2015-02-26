<?php

namespace Tests\Devster\Frontmatter\Parser;

use Devster\Frontmatter\Parser\Markdown;
use Devster\Frontmatter\Parser\MarkdownParser;

class MarkdownParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $j = new MarkdownParser;

        $result = $j->parse('# Hello');

        $this->assertEquals('<h1>Hello</h1>', $result);
    }
}
