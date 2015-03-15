# Frontmatter parser

[![Latest Version](https://img.shields.io/github/release/devster/frontmatter.svg?style=flat-square)](https://github.com/devster/frontmatter/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/devster/frontmatter/master.svg?style=flat-square)](https://travis-ci.org/devster/frontmatter)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/devster/frontmatter.svg?style=flat-square)](https://scrutinizer-ci.com/g/devster/frontmatter/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/devster/frontmatter.svg?style=flat-square)](https://scrutinizer-ci.com/g/devster/frontmatter)
[![Total Downloads](https://img.shields.io/packagist/dt/devster/frontmatter.svg?style=flat-square)](https://packagist.org/packages/devster/frontmatter)

[WIP] Frontmatter Jekyll style parser

Available parsers:

 * yaml (use [symfony/yaml](https://github.com/symfony/Yaml))
 * Markdown (use [erusev/parsedown-extra](https://github.com/erusev/parsedown-extra))
 * Json (use [seld/jsonlint](https://github.com/seld/jsonlint))

## Install

Via Composer

``` bash
$ composer require devster/frontmatter
```

And add extra packages that built-in parsers use:

```bash
# YAML
$ composer require symfony/yaml

# Markdown
$ composer require erusev/parsedown-extra

# Json
$ composer require seld/jsonlint
```

*These packages are not required by default to minimize the footprint and speed up your install if you only need few of them*

## Usage

### Basic usage

``` php
require '../vendor/autoload.php';

$parser = new Devster\Frontmatter\Parser('yaml', 'markdown');
$content = <<<EOF
---
title: My Content
description: "This is a description"
---
This is *Markdown* content
EOF;

$frontmatter = $parser->parse($content);

echo $frontmatter->head['title']; // My content
echo $frontmatter->getBody(); // This is <em>Markdown</em> content
```

And because the frontmatter format is not only used by developers, this parser is quite permissive

All content examples above are parsed like normal frontmatter content:

```php
$content = <<<EOF
    --- title: My Title ---
# Title 1

## Title 2
EOF;

$content = <<<EOF

 ---
   title: My Title ---

# Title 1

## Title 2
EOF;

```

### Just parse frontmatter, and don't process head and body

```php
$p = new Parser;
$result = $p->parseFrontmatter($content);

echo $result['head'];
echo $result['body'];
```

### Customize the frontmatter delimiter

```php
$p = new Parser('json', 'markdown', '##');
$p->parse(<<<EOF
##
{ "title": "My title" }
##
Body content
EOF);

// You can also let your user use its own delimiter
$p = new Parser;
$p
    ->guessDelimiter()
    ->parse(<<<EOF
~~X~~
head
~~X~~
body
EOF);
```

### Guess parsers from filename

The frontmatter parsers can be guessed from a filename, based on the extensions.

Take a look at these examples below:

* `my_file.json.md`: Head in Json and Body in Markdown
* `my_file.md`: Head will be parse with the parser set in the constructor, Body in Markdown
* `my_file.unknown`: An exception will be thrown
* `my_file.yml.unknown`: An exception will be thrown

```php
$p = new Parser;
$p
    ->guessParsersFromFilename('my_file.yml.md')
    ->parse(file_gets_content('my_file.yml.md'))
;

// Or you can set the default head parser

$p = new Parser('json');
$p
    ->guessParsersFromFilename('my_file.md')
    ->parse(file_gets_content('my_file.md'))
;
```

### Guess body parser from head

You can also define explicitly in the head which parser the body should be parsed with.

```php
$p = new Parser('yaml');
$p
    ->guessBodyParserFromHead('[options][format]')
    ->parse(<<<EOF
---
title: My Title
options:
    format: json
---
{
    "body": "This is my body"
}
EOF);
```

Internally the [Property Access Component](http://symfony.com/doc/current/components/property_access/introduction.html) from symfony is used. Refer to its documentation to find the path that will be used to grab the parser from the head

If the parser could not be fetch from the head, the default body parser will be use.

### More complex usage

```php

$p = new Devster\Frontmatter\Parser('yml', 'md');
$p
    ->guessDelimiter()
    ->guessParsersFromFilename('my_file.md')
    ->guessBodyParserFromHead('[format]')
;

try {
    $p->parse($content);
} catch (Devster\Frontmatter\Exception\Exception $e) {
    if ($e instanceof Devster\Frontmatter\Exception\ParserNotFoundException) {
        // The head or the body parser is not found
    }

    if ($e instanceof Devster\Frontmatter\Exception\ParsingException) {
        // Unable to parse the frontmatter content
        // or
        // an error occured in head or body parsing
    }
}
```

## Testing

``` bash
$ vendor/bin/phpunit
```

## Roadmap

 * Rename head to header
 * Add an INI parser
 * Allow to not parse the body to avoid the creation of a new custom parser if the need is not built-in
 * Add a validate feature
 * Add dumping feature

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

Special thank to Etienne Zannelli for his help on Regex ❤

- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
