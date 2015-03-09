<?php

namespace Devster\Frontmatter;

use Devster\Frontmatter\Parser\MetaParser;
use Devster\Frontmatter\Parser;
use Devster\Frontmatter\Exception\ParsingException;
use Devster\Frontmatter\Exception\ParserNotFoundException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Parser
{
    /**
     * @var array
     */
    protected $parsers;

    /**
     * @var string
     */
    protected $headParser;

    /**
     * @var string
     */
    protected $bodyParser;

    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var string
     */
    protected $bodyFormatPath;

    /**
     * @var bool
     */
    protected $guessDelimiter = false;

    /**
     * Constructor.
     */
    public function __construct($headParser = null, $bodyParser = null, $delimiter = '---')
    {
        $this->headParser = $headParser;
        $this->bodyParser = $bodyParser;
        $this->delimiter = $delimiter;

        $this
            ->addParser(new Parser\Markdown)
            ->addParser(new Parser\Yaml)
            ->addParser(new Parser\Json)
        ;
    }

    /**
     * Set the frontmatter delimiter
     *
     * @param string $delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function guessDelimiter($status = true)
    {
        $this->guessDelimiter = $status;

        return $this;
    }

    public static function guessDelimiterFromString($content)
    {
        if (preg_match('/^\s*(.*?)[\s\n\r]+/', $content, $matches)) {
            $delimiter = isset($matches[1]) ? $matches[1] : null;

            $pos = strpos($content, $delimiter);

            if (1 <= substr_count($content, $delimiter, $pos + strlen($delimiter))) {
                return $delimiter;
            }
        }
    }

    public function guessParsersFromFilename($file)
    {
        $parts = explode('.', $file);

        if (3 <= count($parts)) {
            $this->bodyParser = array_pop($parts);
            $this->headParser = array_pop($parts);
        } elseif (2 == count($parts)) {
            $this->bodyParser = array_pop($parts);
        }

        return $this;
    }

    public function guessBodyParserFromHead($propertyPath)
    {
        $this->bodyFormatPath = $propertyPath;

        return $this;
    }

    public function parseFrontmatter($content)
    {
        $delimiter = $this->delimiter;

        if ($this->guessDelimiter && !$delimiter = self::guessDelimiterFromString($content)) {
            throw new ParsingException("frontmatter", "Unable to find frontmatter delimiter");
        }

        $head = $body = null;

        $pattern = sprintf(
            "/^\s*(?:%s)[\n\r\s]*(.*?)[\n\r\s]*(?:%s)[\s\n\r]*(.*)$/s",
            $delimiter,
            $delimiter
        );

        $result = preg_match($pattern, $content, $matches);

        $head = isset($matches[1]) ? $matches[1] : null;
        $body = isset($matches[2]) ? $matches[2] : null;

        if (!$result || (is_null($head) && is_null($body))) {
            throw new ParsingException(
                "frontmatter",
                sprintf("Unable to find frontmatter head with delimiter: %s", $delimiter)
            );
        }

        return array('head' => $head, 'body' => $body);
    }

    public function parse($content)
    {
        $content = $this->parseFrontmatter($content);

        if (!$headParser = $this->findParser($this->headParser)) {
            throw new ParserNotFoundException($this->headParser, 'head');
        }

        $head = $headParser->parse($content['head']);

        // find body parser in head data
        $bodyParserName = null;
        if ($this->bodyFormatPath) {
            $accessor = PropertyAccess::createPropertyAccessorBuilder()->enableMagicCall()->getPropertyAccessor();
            $bodyParserName = $accessor->getValue($head, $this->bodyFormatPath);
        }

        $bodyParserName = $bodyParserName ?: $this->bodyParser;

        if (!$bodyParser = $this->findParser($bodyParserName)) {
            throw new ParserNotFoundException($bodyParserName, 'body');
        }

        $body = $bodyParser->parse($content['body']);

        return new Frontmatter($head, $body);
    }

    /**
     * Add Parser
     *
     * @param  MetaParser $parser
     * @return self
     */
    public function addParser(MetaParser $parser)
    {
        $this->parsers[$parser->getName()] = $parser;

        return $this;
    }

    /**
     * Has parser
     *
     * @param  string $name
     * @return bool
     */
    public function hasParser($name)
    {
        return array_key_exists($name, $this->parsers);
    }

    /**
     * Find a parser from its name or aliases
     *
     * @param  string $name
     * @return Devster\Frontmatter\Parser\Parser
     */
    public function findParser($name)
    {
        $meta = null;

        if (array_key_exists($name, $this->parsers)) {
            $meta = $this->parsers[$name];
        } else {
            foreach ($this->parsers as $p) {
                if (in_array($name, $p->getAliases())) {
                    $meta = $p;
                    break;
                }
            }
        }

        if ($meta) {
            return $meta->instanciateParser();
        }
    }
}
