<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class ComprobanteFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Filters\NumberFilterType::class)
            ->add('monto', Filters\NumberFilterType::class)
            ->add('recargoSegundoVencimiento', Filters\NumberFilterType::class)
            ->add('recargoPrimerVencimiento', Filters\NumberFilterType::class)
            ->add('clienteSap', Filters\TextFilterType::class)
            ->add('letraServicio', Filters\TextFilterType::class)
            ->add('rendicionNumero', Filters\TextFilterType::class)
            ->add('fechaCobro', Filters\DateFilterType::class)
            ->add('primerVencimiento', Filters\DateTimeFilterType::class)
            ->add('segundoVencimiento', Filters\DateTimeFilterType::class)
            ->add('numeroCodigoBarra', Filters\TextFilterType::class)
            ->add('createdAt', Filters\DateTimeFilterType::class)
        
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
