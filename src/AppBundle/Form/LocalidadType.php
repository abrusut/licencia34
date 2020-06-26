<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalidadType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('l_distrito','text',
                 array(
                    'label' => 'Id Distrito'))
            ->add('l_nom_dis','text',
                    array(
                    'label' => 'Nombre Distrito'))
            ->add('l_departamento','text',
                    array(
                    'label' => 'Departamento'))
            ->add('l_nom_dpto','text',
                    array(
                    'label' => 'Nombre Departamento'))
            ->add('nodo')
            ->add('provincia', EntityType::class, array(
                'class' => 'AppBundle\Entity\Provincia',
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione una Provincia',
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
            'data_class' => 'AppBundle\Entity\Localidad'
        ));
    }
}
