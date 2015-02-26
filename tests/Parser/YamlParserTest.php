<?php

namespace Tests\Devster\Frontmatter\Parser;

use Devster\Frontmatter\Parser\Yaml;
use Devster\Frontmatter\Parser\YamlParser;

class YamlParserTest extends \PHPUnit_Framework_TestCase
{
    protected function createParser()
    {
        $j = new Yaml;
        return $j->instanciateParser();
    }

    public function testParse()
    {
        $j = new YamlParser;

        $result = $j->parse('name: john');

        $this->assertEquals(array('name' => 'john'), $result);
    }

    public function testParseError()
    {
        $this->setExpectedException('Devster\Frontmatter\Exception\ParsingException');

        $result = $this->createParser()->parse('"name" john');
    }
}
