<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AtributoConfiguracionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('clave')
            ->add('valor',TextareaType::class,array(
                'label' => 'Valor'
            ))           
            ->add('createdAt','date', array(                        
                'constraints' => null,
                'required' => FALSE,
                'data' => (isset($options['data']) && 
                                $options['data']->getCreatedAt() !== null) ? $options['data']->getCreatedAt() : new \DateTime(),
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker'],
                 'disabled'=>true,
                 'read_only'=>true
                )
            )            
            ->add('updatedAt','date', array(                        
                'constraints' => null,
                'required' => FALSE,
                'data' => (isset($options['data']) && 
                                $options['data']->getUpdatedAt() !== null) ? $options['data']->getUpdatedAt() : null,
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker'],
                 'disabled'=>true,
                 'read_only'=>true
                )
            )            
            ->add('fechaBaja','date', array(                        
                'constraints' => null,
                'required' => FALSE,
                'data' => (isset($options['data']) && 
                                $options['data']->getFechaBaja() !== null) ? $options['data']->getFechaBaja() : null,
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker']
                )
            )
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AtributoConfiguracion'
        ));
    }
}
