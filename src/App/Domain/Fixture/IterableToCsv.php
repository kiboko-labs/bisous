<?php

namespace App\Domain\Fixture;

class IterableToCsv
{
    /** @var array */
    private $columns;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    public function __invoke(iterable $data, \SplFileObject $output): self
    {
        $output->fputcsv($this->columns, ';');
        foreach ($data as $item) {
            $output->fputcsv($this->orderColumns($this->columns, $item), ';');
        }

        return $this;
    }

    private function orderColumns(array $headers, array $line)
    {
        $result = [];
        foreach ($headers as $cell) {
            $result[$cell] = $line[$cell] ?? null;
        }
        return $result;
    }
}