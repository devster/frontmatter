<?php

namespace Tests\Devster\Frontmatter\Parser;

use Devster\Frontmatter\Parser\Json;
use Devster\Frontmatter\Parser\JsonParser;

class JsonParserTest extends \PHPUnit_Framework_TestCase
{
    protected function createParser()
    {
        $j = new Json;
        return $j->instanciateParser();
    }

    public function testParse()
    {
        $j = new JsonParser;

        $result = $j->parse('{ "name" : "john" }');

        $this->assertEquals(array('name' => 'john'), $result);
    }

    public function testParseError()
    {
        $this->setExpectedException('Devster\Frontmatter\Exception\ParsingException');

        $result = $this->createParser()->parse('{ "name : "john" }');
    }
}
