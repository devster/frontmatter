<?php

namespace Devster\Frontmatter\Parser;

class Markdown extends MetaParser
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'markdown';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return array('md', 'MD');
    }
}
