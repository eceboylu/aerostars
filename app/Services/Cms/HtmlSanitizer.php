<?php

namespace App\Services\Cms;

use DOMDocument;
use DOMElement;
use DOMXPath;

class HtmlSanitizer
{
    private const FRAGMENT_ID = '__cms_html_sanitizer_fragment__';

    private const URL_ATTRIBUTES = ['href', 'src'];

    private const SAFE_URL_SCHEMES = ['http', 'https', 'mailto', 'tel', 'ftp'];

    public function stripEventHandlers(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $dom = new DOMDocument;

        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="UTF-8"?><!DOCTYPE html><html><body><div id="'.self::FRAGMENT_ID.'">'.$html.'</div></body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($dom);

        foreach ($xpath->query('//script | //style') as $element) {
            $element->parentNode?->removeChild($element);
        }

        foreach ($xpath->query('//*') as $element) {
            if (! $element instanceof DOMElement || ! $element->hasAttributes()) {
                continue;
            }

            $attributesToRemove = [];

            foreach ($element->attributes as $attribute) {
                $name = $attribute->nodeName;

                if (stripos($name, 'on') === 0) {
                    $attributesToRemove[] = $name;

                    continue;
                }

                if (in_array(strtolower($name), self::URL_ATTRIBUTES, true)
                    && ! $this->isSafeUrl($attribute->nodeValue)) {
                    $attributesToRemove[] = $name;
                }
            }

            foreach ($attributesToRemove as $name) {
                $element->removeAttribute($name);
            }
        }

        $fragment = $xpath->query('//*[@id="'.self::FRAGMENT_ID.'"]')->item(0);

        if (! $fragment instanceof DOMElement) {
            return $html;
        }

        $output = '';

        foreach ($fragment->childNodes as $child) {
            $output .= $dom->saveHTML($child);
        }

        return $output;
    }

    public function isSafeUrl(string $url): bool
    {
        $url = preg_replace('/[\t\r\n]/', '', $url);
        $url = trim($url, "\x00..\x20");

        if ($url === '') {
            return true;
        }

        if (! preg_match('/^([a-zA-Z][a-zA-Z0-9+.\-]*):/', $url, $matches)) {
            return true;
        }

        return in_array(strtolower($matches[1]), self::SAFE_URL_SCHEMES, true);
    }
}
