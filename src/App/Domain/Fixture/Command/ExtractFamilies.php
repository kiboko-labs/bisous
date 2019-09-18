<?php

namespace App\Domain\Fixture\Command;

use App\Domain\Fixture\IterableToCsv;

class ExtractFamilies
{
    public function __invoke(
        \SplFileObject $output,
        array $families,
        array $scopes,
        array $locales
    ): void {
        $columns = array_merge(
            [
                'code',
            ],
            array_map(function($locale) {
                return 'label-' . $locale;
            }, $locales),
            [
                'attributes',
                'attribute_as_image',
                'attribute_as_label',
            ],
            array_map(function($scope) {
                return 'requirements-' . $scope;
            }, array_keys($scopes))
        );

        (new IterableToCsv($columns))
        (
            (function(array $families, array $locales) {
                foreach ($families as $familyCode => $family) {
                    yield array_merge(
                        [
                            'code' => $familyCode,
                            'attributes' => implode(',', $family['attributes']),
                            'attribute_as_label' => $family['label'],
                            'attribute_as_image' => $family['image'],
                        ],
                        ...array_map(function($requirements) {
                            return [
                                'requirements-' . $requirements['scope'] => implode(',', $requirements['attributes'])
                            ];
                        }, $family['requirements']),
                        ...array_map(function($locale) use($familyCode) {
                            return [
                                'label-' . $locale => sprintf('%s (%s)', $familyCode, $locale),
                            ];
                        }, $locales)
                    );
                }
            })($families, $locales),
            $output
        );
    }
}