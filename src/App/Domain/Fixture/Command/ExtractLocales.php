<?php

namespace App\Domain\Fixture\Command;

use App\Domain\Magento\Locale;
use Symfony\Component\Yaml\Yaml;

class ExtractLocales
{
    /**
     * @param \SplFileObject $output
     * @param Locale[] $locales
     */
    public function __invoke(\SplFileObject $output, array $locales): void
    {
        $output->fwrite(Yaml::dump([
            'locales' => iterator_to_array((function(array $locales) {
                /** @var Locale $locale */
                foreach ($locales as $locale) {
                    yield $locale->code() => [
                        'currency' => $locale->currency()
                    ];
                }
            })($locales))
        ], 4, 2));
    }
}