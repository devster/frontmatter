<?php

namespace Tests\Devster\Frontmatter\Parser;

use Devster\Frontmatter\Parser\Json;

class JsonTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $j = new Json;

        $this->assertEquals('json', $j->getName());
    }

    public function testAliases()
    {
        $j = new Json;

        $this->assertContains('JSON', $j->getAliases());
    }

    public function testGetParserClass()
    {
        $j = new Json;

        $this->assertEquals('Devster\Frontmatter\Parser\JsonParser', $j->getParserClass());
    }

    public function testInstanciateParser()
    {
        $j = new Json;

        $p = $j->instanciateParser();

        $this->assertInstanceOf('Devster\Frontmatter\Parser\JsonParser', $p);

        $this->assertInstanceOf('Devster\Frontmatter\Parser\Json', $p->getMetaParser());
    }
}
