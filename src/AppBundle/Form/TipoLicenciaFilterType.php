<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class TipoLicenciaFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Filters\NumberFilterType::class)
            ->add('descripcion', Filters\TextFilterType::class)
            ->add('generoJubilado', Filters\TextFilterType::class)
            ->add('clienteSap', Filters\TextFilterType::class)
            ->add('letraServicio', Filters\TextFilterType::class)
            ->add('arancel', Filters\NumberFilterType::class)
            ->add('porcentajeRecargoPrimerVencimiento', Filters\NumberFilterType::class)
            ->add('porcentajeRecargoSegundoVencimiento', Filters\NumberFilterType::class)
            ->add('diasVigencia', Filters\NumberFilterType::class)
            ->add('diasPrimerVencimiento', Filters\NumberFilterType::class)
            ->add('diasSegundoVencimiento', Filters\NumberFilterType::class)
            ->add('fechaTope', Filters\DateTimeFilterType::class)
            ->add('isActive', Filters\BooleanFilterType::class)
            ->add('aplicaEnProvincia', Filters\TextFilterType::class)
        
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
