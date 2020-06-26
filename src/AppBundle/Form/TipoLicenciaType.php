<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TipoLicenciaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // FechaTope el ultimo dia del año corriente
        $fechaTope = new \DateTime('last day of December this year');

        $builder
            ->add('descripcion')
            ->add('generoJubilado')
            ->add('clienteSap')
            ->add('letraServicio')
            ->add('arancel')
            ->add('porcentajeRecargoPrimerVencimiento')
            ->add('porcentajeRecargoSegundoVencimiento')
            ->add('diasVigencia')
            ->add('diasPrimerVencimiento')
            ->add('diasSegundoVencimiento')
            ->add('fechaTope')
            ->add('fechaTope','date', array(                        
                'constraints' => null,
                'data' => (isset($options['data']) && 
                                $options['data']->getFechaTope() !== null) ? $options['data']->getFechaTope() : $fechaTope,
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker']
                )
            )
            ->add('isActive')
            ->add('aplicaEnProvincia')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TipoLicencia'
        ));
    }
}
