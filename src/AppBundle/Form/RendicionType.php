<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RendicionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numeroRendicion')
            ->add('nis')            
            ->add('fechaCobro','date', array(                        
                'constraints' => null,
                'data' => (isset($options['data']) && 
                                $options['data']->getFechaCobro() !== null) ? $options['data']->getFechaCobro() : new \DateTime(),
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker'],
                 'label' => 'Fecha Cobro'                 
                )
            )
            ->add('codigoBarraCliente')
            ->add('importeCobrado')                        
            ->add('createdAt','date', array(                        
                'constraints' => null,
                'data' => (isset($options['data']) && 
                                $options['data']->getCreatedAt() !== null) ? $options['data']->getCreatedAt() : new \DateTime(),
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker'],
                 'label' => 'Fecha Creacion',
                 'read_only' => TRUE,
                 'disabled' => TRUE,
                 'required' => false
                )
            )
            ->add('updatedAt','date', array(                        
                'constraints' => null,
                'data' => (isset($options['data']) && 
                                $options['data']->getUpdatedAt() !== null) ? $options['data']->getUpdatedAt() : null,
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker'],
                 'label' => 'Fecha Actualizacion',
                 'read_only' => TRUE,
                 'disabled' => TRUE,
                 'required' => false
                )
            )   
            ->add('liquidacion', EntityType::class, array(
                'class' => 'AppBundle\Entity\Liquidacion',
                'choice_label' => 'nis',
                'placeholder' => '--Seleccione--',
                'empty_data' => null,
                'required' => false
 
            )) 
            ->add('fileRendicionLiquidacion', EntityType::class, array(
                'class' => 'AppBundle\Entity\FileRendicionLiquidacion',
                'choice_label' => 'nombreOriginalArchivo',
                'placeholder' => '--Seleccione--',
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
            'data_class' => 'AppBundle\Entity\Rendicion'
        ));
    }
}
