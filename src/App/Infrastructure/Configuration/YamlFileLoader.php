<?php

namespace App\Infrastructure\Configuration;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class YamlFileLoader extends FileLoader
{
    public function load($resource, $type = null)
    {
        $processor = new Processor();

        return $processor->processConfiguration(
            new Configuration(),
            Yaml::parse(file_get_contents($resource))
        );
    }

    public function supports($resource, $type = null)
    {
        if (!\is_string($resource)) {
            return false;
        }

        if (null === $type && \in_array(pathinfo($resource, PATHINFO_EXTENSION), ['yaml', 'yml'], true)) {
            return true;
        }

        return \in_array($type, ['yaml', 'yml'], true);
    }
}