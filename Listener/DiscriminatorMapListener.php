<?php

namespace JHV\Bundle\EasyInheritanceMappingBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * DiscriminatorMapListener.
 * 
 * @author Jorge Vahldick <jvahldick@gmail.com>
 * @license Please view /Resources/meta/LICENSE
 * @copyright (c) 2013
 */
class DiscriminatorMapListener implements EventSubscriber
{

    protected $mapping;

    /**
     * Construtor.
     * Recebimento do mapeamento quanto ao banco de dados.
     *
     * @param array $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function getSubscribedEvents()
    {
        return array(Events::loadClassMetadata);
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();

        foreach ($this->mapping as $mapping) {
            $subclasses = array();

            // Verificar passagem do mapeamento
            if ($classMetadata->getName() == $mapping['entity']) {
                foreach ($mapping['children'] as $chilren) {
                    $subclasses[$chilren['name']] = $chilren['entity'];
                }

                // Definir as classes que estendem a principal
                $classMetadata->setSubclasses($subclasses);

                // Verificar modelo de herança
                $inheritanceType = $mapping['inheritance_type'];
                switch ($inheritanceType) {
                    case 'single_table':
                        $classMetadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE);
                        break;

                    case 'table_per_class':
                        $classMetadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_TABLE_PER_CLASS);
                        break;

                    default:
                        $classMetadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_JOINED);
                        break;
                }

                // Definição da coluna de discriminação
                $classMetadata->setDiscriminatorColumn(array(
                    'name'      => $mapping['discriminator_column'],
                    'type'      => 'string',
                    'length'    => 100
                ));
            }

            // Adicionar entidade principal a uma listagem de classes
            $classes    = $subclasses;
            $classes[]  = $mapping['entity'];

            // Verificar se a classe está definida na configuração
            if (true === in_array($classMetadata->name, $classes)) {
                $classMetadata->discriminatorMap    = $classes;
                $classMetadata->discriminatorValue  = array_search($classMetadata->name, $subclasses);
            }
        }
    }

}