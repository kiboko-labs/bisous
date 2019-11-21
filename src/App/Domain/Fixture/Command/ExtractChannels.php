<?php

namespace App\Domain\Fixture\Command;

use App\Domain\Fixture\IterableToCsv;
use App\Domain\Magento\Locale;
use App\Domain\Magento\LocaleStore;
use App\Domain\Magento\Scope;

class ExtractChannels
{
    /**
     * @param \SplFileObject $output
     * @param Scope[] $scopes
     * @param Locale[] $locales
     */
    public function __invoke(
        \SplFileObject $output,
        array $scopes,
        array $locales
    ): void {
        $columns = array_merge(
            [
                'code',
                'tree',
                'locales',
                'currencies',
            ],
            array_map(function(LocaleStore $locale) {
                return 'label-' . $locale->code();
            }, $locales)
        );

        (new IterableToCsv($columns))
        (
            (function(array $scopes, array $locales) {
                /** @var Scope $scope */
                foreach ($scopes as $scope) {
                    yield array_merge(
                        [
                            'code' => $scope->code(),
                            'tree' => 'root_catalog',
                            'locales' => implode(',',  array_map(function(LocaleStore $locale) {
                                return $locale->code();
                            }, $scope->locales())),
                            'currencies' => implode(',', array_values(array_unique(array_map(
                                function(Locale $item) {
                                    return $item->currency();
                                },
                                array_filter(
                                    $locales,
                                    function (Locale $locale) use ($scope) {
                                        return in_array($locale->code(), array_map(function(LocaleStore $locale) {
                                            return $locale->code();
                                        }, $scope->locales()));
                                    }
                                )
                            )))),
                        ],
                        ...array_map(function(LocaleStore $locale) use($scope) {
                            return [
                                'label-' . $locale->code() => sprintf('%s (%s)', $scope->code(), $locale->code()),
                            ];
                        }, $scope->locales())
                    );
                }
            })($scopes, $locales),
            $output
        );
    }
}