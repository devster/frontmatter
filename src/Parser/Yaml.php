<?php

namespace Devster\Frontmatter\Parser;

class Yaml extends MetaParser
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'yaml';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return array('yml');
    }
}
