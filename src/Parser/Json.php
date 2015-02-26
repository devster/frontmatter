<?php

namespace Devster\Frontmatter\Parser;

class Json extends MetaParser
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'json';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return array('JSON');
    }
}
