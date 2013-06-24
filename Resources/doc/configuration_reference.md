Configurações de Referência
===========================

Segue abaixo as configurações possíveis para utilização do Bundle.

``` yaml
# app/config/config.yml

### Mapeamento da herança
jhv_easy_inheritance_mapping:
    ### Classe do listener *not required
    listener: "JHV\\Bundle\\EasyInheritanceMappingBundle\\Listener\\DiscriminatorMapListener"

    ### Mapeamento dos itens
    discriminator_mapping:
        some_name:
            entity 					: "" # Super classe
            inheritance_type      	: "single_table" # Modelo de herança
            discriminator_column 	: "" # Nome da coluna de diferenciação das classes

            ### Entidades filhas
            children:
                ### Name: nome diferencial da entidade, Entity: classe da entidade filha
                - { name: "", entity: "" }
```