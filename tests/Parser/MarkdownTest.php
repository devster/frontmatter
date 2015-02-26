<?php

namespace Tests\Devster\Frontmatter\Parser;

use Devster\Frontmatter\Parser\Markdown;

class MarkdownTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $j = new Markdown;

        $this->assertEquals('markdown', $j->getName());
    }

    public function testAliases()
    {
        $j = new Markdown;

        $this->assertContains('md', $j->getAliases());
    }

    public function testGetParserClass()
    {
        $j = new Markdown;

        $this->assertEquals('Devster\Frontmatter\Parser\MarkdownParser', $j->getParserClass());
    }

    public function testInstanciateParser()
    {
        $j = new Markdown;

        $p = $j->instanciateParser();

        $this->assertInstanceOf('Devster\Frontmatter\Parser\MarkdownParser', $p);

        $this->assertInstanceOf('Devster\Frontmatter\Parser\Markdown', $p->getMetaParser());
    }
}
