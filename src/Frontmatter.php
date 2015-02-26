<?php

namespace Devster\Frontmatter;

class Frontmatter
{
    /**
     * @var mixed
     */
    public $head;

    /**
     * @var mixed
     */
    protected $body;

    /**
     * Constructor.
     */
    public function __construct($head, $body)
    {
        $this->head = $head;
        $this->body = $body;
    }

    /**
     * Get body
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }
}
