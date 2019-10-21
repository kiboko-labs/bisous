<?php

namespace App\Domain\Fixture\Command;

use App\Domain\Fixture\IterableToCsv;

class ExtractChannels
{
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
            array_map(function($locale) {
                return 'label-' . $locale;
            }, array_keys($locales))
        );

        (new IterableToCsv($columns))
        (
            (function(array $scopes, array $locales) {
                foreach ($scopes as $code => $scope) {
                    yield array_merge(
                        [
                            'code' => $code,
                            'tree' => 'root_catalog',
                            'locales' => implode(',',  array_map(function($locale) {
                                return $locale['code'];
                            }, $scope['locales'])),
                            'currencies' => implode(',', array_values(array_unique(array_map(
                                function(array $item) {
                                    return $item['currency'];
                                },
                                array_filter(
                                    $locales,
                                    function (array $locale) use ($scope) {
                                        return in_array($locale['code'], array_column($scope['locales'], 'code'));
                                    }
                                )
                            )))),
                        ],
                        ...array_map(function($locale) use($code) {
                            return [
                                'label-' . $locale['code'] => sprintf('%s (%s)', $code, $locale['code']),
                            ];
                        }, $scope['locales'])
                    );
                }
            })($scopes, $locales),
            $output
        );
    }
}