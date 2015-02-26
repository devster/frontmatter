<?php

namespace Tests\Devster\Frontmatter\Parser;

use Devster\Frontmatter\Parser\Yaml;

class YamlTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $j = new Yaml;

        $this->assertEquals('yaml', $j->getName());
    }

    public function testAliases()
    {
        $j = new Yaml;

        $this->assertContains('yml', $j->getAliases());
    }

    public function testGetParserClass()
    {
        $j = new Yaml;

        $this->assertEquals('Devster\Frontmatter\Parser\YamlParser', $j->getParserClass());
    }

    public function testInstanciateParser()
    {
        $j = new Yaml;

        $p = $j->instanciateParser();

        $this->assertInstanceOf('Devster\Frontmatter\Parser\YamlParser', $p);

        $this->assertInstanceOf('Devster\Frontmatter\Parser\Yaml', $p->getMetaParser());
    }
}
