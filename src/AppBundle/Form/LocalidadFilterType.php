<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class LocalidadFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Filters\NumberFilterType::class)
            ->add('l_distrito', Filters\TextFilterType::class)
            ->add('l_nom_dis', Filters\TextFilterType::class)
            ->add('l_departamento', Filters\TextFilterType::class)
            ->add('l_nom_dpto', Filters\TextFilterType::class)
            ->add('nodo', Filters\TextFilterType::class)
        
            ->add('provincia', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\Provincia',
                    'choice_label' => 'nombre',
            )) 
        ;
        $builder->setMethod("GET");


    }

    public function getBlockPrefix()
    {
        return null;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
