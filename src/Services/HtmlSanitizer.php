<?php
namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlSanitizer
{
    private $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $config->set('Cache.DefinitionImpl', null);
        $config->set('AutoFormat.AutoParagraph', true);
        $this->purifier = new HTMLPurifier($config);
    }

    public function sanitize(string $html): string
    {
        return $this->purifier->purify($html);
    }

    public function sanitizeObj($object)
    {
        $reflection = new \ReflectionClass($object);
        $properties = $reflection->getProperties();
        
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            
            if (is_string($value)) {
                $property->setValue($object, $this->purifier->purify($value));
            }
        }

        return $object;
    }
}