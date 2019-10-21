<?php

namespace App\Domain\Fixture\Command;

use Symfony\Component\Yaml\Yaml;

class ExtractLocales
{
    public function __invoke(\SplFileObject $output, array $locales): void
    {
        $output->fwrite(Yaml::dump([
            'locales' => iterator_to_array((function(array $locales) {
                foreach ($locales as $code => $config) {
                    yield $code => [
                        'currency' => $config['currency']
                    ];
                }
            })($locales))
        ], 4, 2));
    }
}