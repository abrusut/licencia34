<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Form\PersonaType;
use AppBundle\Form\TipoLicenciaType;

class LicenciaReadOnlyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder           
            ->add('tipoLicencia','entity', 
                array(
                    'label' => 'Tipo Licencia',
                    'class' => 'AppBundle:TipoLicencia',
                    'required' => TRUE,
                    'attr' => ['readonly' => true, 'disabled'=>true] 
                ))            
                ->add('fechaEmitida','date', array(                        
                    'constraints' => null,
                    'data' => (isset($options['data']) && 
                                    $options['data']->getFechaEmitida() !== null) ? $options['data']->getFechaEmitida() : new \DateTime(),
                     // render as a single text box
                     'widget' => 'single_text',
                     'format' => 'dd/MM/yyyy',
                     // do not render as type="date", to avoid HTML5 date pickers
                     'html5' => false,
                     // add a class that can be selected in JavaScript
                     'attr' => ['class' => 'js-datepicker disabledPanel','readonly' => true, 'disabled'=>true]
                    )
                )
                ->add('fechaDesde','date', array(                        
                    'constraints' => null,
                    'required' => TRUE,
                    'data' => (isset($options['data']) && 
                                    $options['data']->getFechaDesde() !== null) ? $options['data']->getFechaDesde() : new \DateTime(),
                     // render as a single text box
                     'widget' => 'single_text',
                     'format' => 'dd/MM/yyyy',
                     // do not render as type="date", to avoid HTML5 date pickers
                     'html5' => false,
                     // add a class that can be selected in JavaScript
                     'attr' => ['class' => 'js-datepicker','readonly' => true, 'disabled'=>true]
                    )
                )
            ->add('persona',  new PersonaType(),
                array(
                        'attr' => ['readonly' => true, 'disabled' => true ]
                ));
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Licencia',
            'cascade_validation' => true,
            'attr' => ['readonly' => true]            
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mprod_licenciacypbundle_licencia_readOnly';
    }
     /**
     * @return string
     */
    public function getId()
    {
        return 'mprod_licenciacypbundle_licencia_readOnly';
    }
}
