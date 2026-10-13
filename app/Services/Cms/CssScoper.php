<?php

namespace App\Services\Cms;

use Sabberworm\CSS\CSSList\CSSList;
use Sabberworm\CSS\CSSList\KeyFrame;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Property\Import;
use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\Settings;
use Throwable;

class CssScoper
{
    public const SCOPE = '#cms-page-content';

    public function scope(?string $css, ?string $scope = null): ?string
    {
        if ($css === null || trim($css) === '') {
            return null;
        }

        $scope ??= self::SCOPE;

        try {
            $document = (new Parser($css, Settings::create()->withLenientParsing(true)))->parse();
        } catch (Throwable) {
            return null;
        }

        $this->scopeList($document, $scope);

        return $document->render(OutputFormat::create());
    }

    private function scopeList(CSSList $list, string $scope): void
    {
        foreach ($list->getContents() as $item) {
            if ($item instanceof Import) {
                $list->remove($item);

                continue;
            }

            if ($item instanceof KeyFrame) {
                continue;
            }

            if ($item instanceof DeclarationBlock) {
                $item->setSelectors(array_map(
                    fn (string $selector): string => $this->prefixSelector($selector, $scope),
                    array_map(fn ($selector) => $selector->getSelector(), $item->getSelectors())
                ));

                continue;
            }

            if ($item instanceof CSSList) {
                $this->scopeList($item, $scope);
            }
        }
    }

    private function prefixSelector(string $selector, string $scope): string
    {
        $selector = trim($selector);

        if ($selector === ':root') {
            return $scope;
        }

        if ($selector === 'body') {
            return $scope;
        }

        if (str_starts_with($selector, 'body ') || str_starts_with($selector, 'body:') || str_starts_with($selector, 'body.') || str_starts_with($selector, 'body[')) {
            return $scope.substr($selector, 4);
        }

        return $scope.' '.$selector;
    }
}
