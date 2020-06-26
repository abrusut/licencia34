<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LicenciaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechaEmitida')
            ->add('fechaDesde')
            ->add('fechaVencimiento')
            ->add('numero')
            ->add('tipoLicencia', EntityType::class, array(
                'class' => 'AppBundle\Entity\TipoLicencia',
                'choice_label' => 'descripcion',
                'placeholder' => 'Please choose',
                'empty_data' => null,
                'required' => false
 
            )) 
            ->add('persona', EntityType::class, array(
                'class' => 'AppBundle\Entity\Persona',
                'choice_label' => 'nombre',
                'placeholder' => 'Please choose',
                'empty_data' => null,
                'required' => false
 
            )) 
            ->add('comprobante', EntityType::class, array(
                'class' => 'AppBundle\Entity\Comprobante',
                'choice_label' => 'clienteSap',
                'placeholder' => 'Please choose',
                'empty_data' => null,
                'required' => false
 
            )) 
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Licencia'
        ));
    }
}
