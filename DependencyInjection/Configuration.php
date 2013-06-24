<?php

namespace JHV\Bundle\EasyInheritanceMappingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jhv_easy_inheritance_mapping');

        $rootNode
            ->children()
                // Ouvinte para processamento dos dados
                ->scalarNode('listener')
                    ->cannotBeEmpty()
                    ->defaultValue('JHV\\Bundle\\EasyInheritanceMappingBundle\\Listener\\DiscriminatorMapListener')
                ->end()

                ->append($this->addDiscriminatorMappingNode())
            ->end()
        ;

        return $treeBuilder;
    }


    protected function addDiscriminatorMappingNode()
    {
        $treeBuilder    = new TreeBuilder();
        $node           = $treeBuilder->root('discriminator_mapping');

        $node
            ->useAttributeAsKey('id')
            ->prototype('array')
                ->children()
                    // Entitidade principal
                    ->scalarNode('entity')
                        ->isRequired()
                    ->end()

                    // Modelo de herança
                    ->scalarNode('inheritance_type')
                        ->isRequired()
                    ->end()

                    // Coluna de discriminação
                    ->scalarNode('discriminator_column')
                        ->isRequired()
                    ->end()

                    // Verificar o mapeamento das entidades que extendem a entidade principal
                    ->arrayNode('children')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('name')->isRequired()->end()
                                ->scalarNode('entity')->isRequired()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

}
