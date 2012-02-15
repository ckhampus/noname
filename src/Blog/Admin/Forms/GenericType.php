<?php

namespace Blog\Admin\Forms;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

use Doctrine\ORM\Mapping\ClassMetadata;

class GenericType extends AbstractType
{
    private $fields;

    function __construct(ClassMetadata $metadata) {
        $this->fields = array();

        foreach ($metadata->fieldMappings as $field) {
            switch ($field['type']) {
                case 'string':
                    $type = 'text';
                    break;
                
                default:
                    $type = $field['type'];
                    break;
            }

            if (!$metadata->isIdentifier($field['fieldName'])) {
                $this->fields[$field['fieldName']] = $type;
            }
        }
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        foreach ($this->fields as $name => $type) {
            $builder = $builder->add($name, $type);
        }
    }

    public function getName()
    {
        return 'generic_type';
    }
}
