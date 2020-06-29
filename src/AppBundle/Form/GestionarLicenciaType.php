<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Form\TipoLicenciaType;

class GestionarLicenciaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder           
            ->add('tipoLicencia',EntityType::class,
                array(
                    'label' => 'Tipo Licencia',
                    'class' => 'AppBundle:TipoLicencia',
                    'required' => TRUE,
                    'placeholder' => '-- Seleccione --'
                ))
                ->add('fechaEmitida',DateType::class, array(
                    'constraints' => null,
                    'data' => (isset($options['data']) && 
                                    $options['data']->getFechaEmitida() !== null) ? $options['data']->getFechaEmitida() : new \DateTime(),
                     // render as a single text box
                     'widget' => 'single_text',
                     //'format' => 'dd/MM/yyyy',
                     // do not render as type="date", to avoid HTML5 date pickers
                    // 'html5' => false,
                     // add a class that can be selected in JavaScript
                     //'attr' => ['class' => 'js-datepicker disabledPanel'],
                     'disabled' => TRUE,
                     //'read_only' => TRUE
                    )
                )
                ->add('fechaDesde',DateType::class, array(
                    'constraints' => null,
                    'required' => false,
                    'data' => (isset($options['data']) && 
                                    $options['data']->getFechaDesde() !== null) ? $options['data']->getFechaDesde() : new \DateTime(),
                     // render as a single text box
                     'widget' => 'single_text',
                    // 'format' => 'dd/MM/yyyy',
                     // do not render as type="date", to avoid HTML5 date pickers
                     //'html5' => false,
                     // add a class that can be selected in JavaScript
                     //'attr' => ['class' => 'js-datepicker']
                    )
                )
            ->add('persona',  PersonaLicenciaType::class)
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Licencia',
            'cascade_validation' => true            
        ));
    }

}
