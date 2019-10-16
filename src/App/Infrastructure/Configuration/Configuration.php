<?php

namespace App\Infrastructure\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('catalog');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('attributes')->cannotBeEmpty()->useAttributeAsKey('code', false)
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('code')->end()
                            ->booleanNode('scoped')->end()
                            ->booleanNode('localised')->end()
                            ->arrayNode('label')
                                ->cannotBeEmpty()
                                ->scalarPrototype()->end()
                            ->end()
                            ->enumNode('type')
                                ->isRequired()
                                ->values([
                                    'identifier',
                                    'string',
                                    'text',
                                    'rich-text',
                                    'simple-select',
                                    'multi-select',
                                    'metric',
                                    'image',
                                    'status',
                                    'visibility',
                                    'datetime',
                                ])
                            ->end()
                            ->scalarNode('strategy')->isRequired()->end()
                            ->scalarNode('source')->end()
                            ->scalarNode('group')->defaultValue('default')->end()
                            ->arrayNode('metric')
                                ->children()
                                    ->scalarNode('family')->isRequired()->end()
                                    ->scalarNode('unit')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('groups')->cannotBeEmpty()->useAttributeAsKey('code', false)
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('code')->end()
                            ->arrayNode('label')
                                ->cannotBeEmpty()
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('families')->cannotBeEmpty()->useAttributeAsKey('code', false)
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('code')->end()
                            ->scalarNode('label')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('image')->isRequired()->cannotBeEmpty()->end()
                            ->arrayNode('attributes')
                                ->cannotBeEmpty()
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('variations')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('skuPattern')->end()
                                        ->scalarNode('code')->isRequired()->end()
                                        ->arrayNode('level_1')
                                            ->children()
                                                ->arrayNode('axis')
                                                    ->cannotBeEmpty()
                                                    ->scalarPrototype()->end()
                                                ->end()
                                                ->arrayNode('attributes')
                                                    ->scalarPrototype()->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('level_2')
                                            ->children()
                                                ->arrayNode('axis')
                                                    ->cannotBeEmpty()
                                                    ->scalarPrototype()->end()
                                                ->end()
                                                ->arrayNode('attributes')
                                                    ->scalarPrototype()->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('requirements')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('scope')->isRequired()->end()
                                        ->arrayNode('attributes')
                                            ->cannotBeEmpty()
                                            ->scalarPrototype()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('locales')->cannotBeEmpty()->useAttributeAsKey('code', false)
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('code')->end()
                            ->scalarNode('currency')->end()
                            ->scalarNode('store')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('scopes')->cannotBeEmpty()->useAttributeAsKey('code', false)
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('code')->end()
                            ->arrayNode('locales')->cannotBeEmpty()
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('code')->isRequired()->end()
                                        ->scalarNode('store')->isRequired()->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('store')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('codes_mapping')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('from')->end()
                            ->scalarNode('to')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}