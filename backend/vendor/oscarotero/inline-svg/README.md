# InlineSvg

PHP library to embed svg icons in the html pages so you can use css to change the style. Why do not use icon fonts? [Here a comparison svg vs fonts](http://css-tricks.com/icon-fonts-vs-svg/)

## Requirements:

* PHP 5.4+
* [DOMDocument extension](http://php.net/DOMDocument) (enabled in PHP by default)

## Installation

This package is installable and autoloadable via Composer as [oscarotero/inline-svg](https://packagist.org/packages/oscarotero/inline-svg).

```
$ composer require oscarotero/inline-svg
```

## Usage:

```php
use InlineSvg\Collection;
use InlineSvg\Transformers\Cleaner;

$icons = Collection::fromPath('path/to/svg/files');

//Add a cleaner transformer to remove ids
$icons->addTransformer(new Cleaner());

//Insert the svg code of the file 'edit.svg':
echo $icons->get('edit'); // <svg ... </svg>

//Modify any attribute
echo $icons->get('edit')->withAttribute('class', 'big-icon'); // <svg class="big-icon" .. </svg>

//Make the svg accesible
echo $icons->get('edit')->withA11y('The edit icon'); // <svg role="img" aria-labelledby="icon-edit-123-title"><title id="icon-edit-123-title">The edit icon</title> .. </svg>
```

