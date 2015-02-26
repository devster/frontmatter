<?php

namespace Devster\Frontmatter\Parser;

abstract class MetaParser
{
    /**
     * Get parser name
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Get aliases
     *
     * ex: md, MD etc
     *
     * @return array
     */
    abstract public function getAliases();

    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get the parser class
     *
     * @return string
     */
    public function getParserClass()
    {
        return get_class($this).'Parser';
    }

    /**
     * Instanciate the corresponding parser
     *
     * @return ParserInterface
     */
    public function instanciateParser()
    {
        $class = $this->getParserClass();
        $parser = new $class;
        $parser->setMetaParser($this);

        return $parser;
    }
}
