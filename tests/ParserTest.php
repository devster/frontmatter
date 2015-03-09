<?php

namespace Tests\Devster\Frontmatter;

use Devster\Frontmatter\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testAddParser()
    {
        $stub = $this->getMockBuilder('Devster\Frontmatter\Parser\Json')
                     ->getMock();

        $stub->method('getName')
             ->willReturn('foo');

        $p = new Parser;

        $this->assertFalse($p->hasParser('foo'));

        $p->addParser($stub);

        $this->assertTrue($p->hasParser('foo'));
    }

    public function testFindParser()
    {
        $p = new Parser;

        $this->assertInstanceOf('Devster\Frontmatter\Parser\MarkdownParser', $p->findParser('markdown'));
        $this->assertInstanceOf('Devster\Frontmatter\Parser\MarkdownParser', $p->findParser('md'));
        $this->assertInstanceOf('Devster\Frontmatter\Parser\YamlParser', $p->findParser('yml'));
        $this->assertNull($p->findParser('YAML'));
        $this->assertNull($p->findParser('YML'));

    }

    public function testParseFrontmatter()
    {
        $p = new Parser;

        $content = <<<EOF
---
head
---
body
EOF;
        $result = $p->parseFrontmatter($content);
        $this->assertEquals('head', $result['head']);
        $this->assertEquals('body', $result['body']);

        // Dirty frontmatter content cases

        $content = <<<EOF

  --- head--- body
EOF;
        $result = $p->parseFrontmatter($content);
        $this->assertEquals('head', $result['head']);
        $this->assertEquals('body', $result['body']);

        $content = <<<EOF
  ---
    head
    ---
    body
EOF;
        $result = $p->parseFrontmatter($content);
        $this->assertEquals('head', $result['head']);
        $this->assertEquals('body', $result['body']);

        // Changing the delimiter
        $p->setDelimiter('##');
        $content = <<<EOF
##head## body
EOF;
        $result = $p->parseFrontmatter($content);
        $this->assertEquals('head', $result['head']);
        $this->assertEquals('body', $result['body']);

        // Empty head
        $p->setDelimiter('---');
        $content = <<<EOF
---
---
body
EOF;
        $result = $p->parseFrontmatter($content);
        $this->assertEquals('', $result['head']);
        $this->assertEquals('body', $result['body']);

        $content = <<<EOF
  ------
body
EOF;
        $result = $p->parseFrontmatter($content);
        $this->assertEquals('', $result['head']);
        $this->assertEquals('body', $result['body']);

        // Guessing delimiter
        $p->guessDelimiter();
        $content = <<<EOF
xxx
head
xxx


body
EOF;
        $result = $p->parseFrontmatter($content);
        $this->assertEquals('head', $result['head']);
        $this->assertEquals('body', $result['body']);

        $content = <<<EOF
  x~xx head x~xx
body
EOF;
        $result = $p->parseFrontmatter($content);
        $this->assertEquals('head', $result['head']);
        $this->assertEquals('body', $result['body']);
    }

    public function testUnableToParseFrontmatter()
    {
        $this->setExpectedException(
            'Devster\Frontmatter\Exception\ParsingException', 'Unable to find frontmatter head with delimiter: ---'
        );

        $p = new Parser;

        $content = " body ";

        $p->parseFrontmatter($content);
    }

    public function testUnableToGuessDelimiter()
    {
        $this->setExpectedException(
            'Devster\Frontmatter\Exception\ParsingException', 'Unable to find frontmatter delimiter'
        );

        $p = new Parser;
        $p->guessDelimiter();

        $content = <<<EOF
---
head
--
Body
EOF;

        $p->parseFrontmatter($content);
    }

    public function testGuessDelimiterFromString()
    {
        $this->assertEquals(null, Parser::guessDelimiterFromString(' -x- head -x body'));

        $this->assertEquals('---', Parser::guessDelimiterFromString(' --- head --- body'));

        $content = <<<EOF
--
head
--
EOF;
        $this->assertEquals('--', Parser::guessDelimiterFromString($content));
    }

    public function testParse()
    {
        $p = new Parser('yaml', 'markdown');

        $content = <<<EOF
---
name: john
---

# Hello
EOF;

        $frontmatter = $p->parse($content);

        $this->assertinstanceOf('Devster\Frontmatter\Frontmatter', $frontmatter);
        $this->assertEquals(array('name' => 'john'), $frontmatter->head);
        $this->assertEquals('<h1>Hello</h1>', $frontmatter->getBody());

        // Inverse parsers for the POC
        $p = new Parser('md', 'yml');

        $content = <<<EOF
---
# Hello
---

name: john
EOF;

        $frontmatter = $p->parse($content);

        $this->assertEquals('<h1>Hello</h1>', $frontmatter->head);
        $this->assertEquals(array('name' => 'john'), $frontmatter->getBody());

        // Guess body format from head
        $p = new Parser('yaml', 'json');
        $p->guessBodyParserFromHead('[options][format]');

        $content = <<<EOF
---
name: john
options:
    format: markdown
---

# Hello
EOF;

        $frontmatter = $p->parse($content);

        $this->assertEquals('markdown', $frontmatter->head['options']['format']);
        $this->assertEquals('<h1>Hello</h1>', $frontmatter->getBody());

        // Guess format from head but not used
        $content = <<<EOF
---
name: john
---
{ "lastname": "doe" }
EOF;

        $frontmatter = $p->parse($content);

        $this->assertEquals(array('name' => 'john'), $frontmatter->head);
        $this->assertEquals(array('lastname' => 'doe'), $frontmatter->getBody());

        // Guess parsers from filename
        $p = new Parser('yaml', 'json');
        $p->guessParsersFromFilename('my_file.md');

        $content = <<<EOF
---
name: john
---
# Hello
EOF;

        $frontmatter = $p->parse($content);

        $this->assertEquals('<h1>Hello</h1>', $frontmatter->getBody());

        $p = new Parser('json');
        $p->guessParsersFromFilename('my_file.yml.markdown');

        $content = <<<EOF
---
name: john
---
# Hello
EOF;

        $frontmatter = $p->parse($content);

        $this->assertEquals(array('name' => 'john'), $frontmatter->head);
        $this->assertEquals('<h1>Hello</h1>', $frontmatter->getBody());
    }

    public function testUnableToGuessHeadParserFromFilename()
    {
        $this->setExpectedException(
            'Devster\Frontmatter\Exception\ParserNotFoundException',
            'Parser "something" not found for head parsing'
        );

        $p = new Parser;
        $p->guessParsersFromFilename('my_file.something.md');
        $p->parse('--- --- body');
    }

    public function testUnableToGuessBodyParserFromFilename()
    {
        $this->setExpectedException(
            'Devster\Frontmatter\Exception\ParserNotFoundException',
            'Parser "something" not found for body parsing'
        );

        $p = new Parser;
        $p->guessParsersFromFilename('my_file.yml.something');
        $p->parse('--- --- body');
    }

    public function testUnableToGuessBodyParserFromHead()
    {
        $this->setExpectedException(
            'Devster\Frontmatter\Exception\ParserNotFoundException',
            'Parser "something" not found for body parsing'
        );

        $p = new Parser('yaml');
        $p->guessBodyParserFromHead('[format]');
        $p->parse('--- format: something --- body');
    }
}
