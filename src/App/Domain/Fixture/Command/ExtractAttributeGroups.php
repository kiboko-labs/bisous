<?php

namespace App\Domain\Fixture\Command;

use App\Domain\Fixture\IterableToCsv;

class ExtractAttributeGroups
{
    public function __invoke(
        \SplFileObject $output,
        array $groups,
        array $locales
    ): void {
        $columns = array_merge(
            [
                'code',
                'sort_order',
            ],
            array_map(function($locale) {
                return 'label-' . $locale;
            }, $locales)
        );

        (new IterableToCsv($columns))
        (
            (function(array $groups) {
                $index = 0;
                foreach ($groups as $groupCode => $group) {
                    yield array_merge(
                        [
                            'code' => $groupCode,
                            'sort_order' => ++$index,
                        ],
                        ...array_map(function($locale, $label) {
                            return [
                                'label-' . $locale => $label,
                            ];
                        }, array_keys($group['label']), $group['label'])
                    );
                }
            })($groups),
            $output
        );
    }
}