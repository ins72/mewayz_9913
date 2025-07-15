<?php

namespace InlineSvg\Transformers;

use DOMDocument;

class Cleaner
{
    /**
     * Execute the transformer.
     *
     * @param DOMDocument $dom
     */
    public function __invoke(DOMDocument $dom)
    {
        $elements = $dom->getElementsByTagName('*');

        //Remove ids
        foreach ($elements as $element) {
            $element->removeAttribute('id');
        }

        //Remove <desc>
        foreach ($dom->getElementsByTagName('desc') as $element) {
            $element->parentNode->removeChild($element);
        }

        //Remove empty <defs>
        foreach ($dom->getElementsByTagName('defs') as $element) {
            if (!$element->hasChildNodes()) {
                $element->parentNode->removeChild($element);
            }
        }
    }
}
