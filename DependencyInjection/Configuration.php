<?php

namespace KimaiPlugin\DompdfRendererBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('dompdf_renderer');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        // Set a configuration option `dompdf_renderer.renderer`, which defaults
        // to 'mpdf', so we don't break existing invoice templates.
        $rootNode
            ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('renderer')
                        ->defaultValue('mpdf')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

