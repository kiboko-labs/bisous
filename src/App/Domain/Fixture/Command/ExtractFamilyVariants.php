<?php

namespace App\Domain\Fixture\Command;

use App\Domain\Fixture\IterableToCsv;

class ExtractFamilyVariants
{
    public function __invoke(\SplFileObject $output, array $families, array $locales): void
    {
        $columns = array_merge(
            [
                'code',
                'family',
            ],
            array_map(function($locale) {
                return 'label-' . $locale;
            }, $locales),
            [
                'variant-axes_1',
                'variant-axes_2',
                'variant-attributes_1',
                'variant-attributes_2',
            ]
        );

        (new IterableToCsv($columns))
        (
            (function(array $families, array $locales) {
                foreach ($families as $familyCode => $family) {
                    foreach ($family['variations'] as $variation) {
                        yield array_merge(
                            [
                                'code' => $variation['code'],
                                'family' => $familyCode,
                                'variant-axes_1' => isset($variation['level_1']) ? implode(',', $variation['level_1']['axis']) : null,
                                'variant-axes_2' => isset($variation['level_2']) ? implode(',', $variation['level_2']['axis']) : null,
                                'variant-attributes_1' => isset($variation['level_1']) ? implode(',', $variation['level_1']['attributes']) : null,
                                'variant-attributes_2' => isset($variation['level_2']) ? implode(',', $variation['level_2']['attributes']) : null,
                            ],
                            ...array_map(function ($locale) use ($variation) {
                                return [
                                    'label-' . $locale => sprintf(
                                        '%s [%s] (%s)',
                                        $variation['code'],
                                        implode(', ', array_merge($variation['level_1']['axis'] ?? [], $variation['level_2']['axis'] ?? [])),
                                        $locale
                                    ),
                                ];
                            }, $locales)
                        );
                    }
                }
            })($families, $locales),
            $output
        );
    }
}