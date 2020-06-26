<?php

namespace AppBundle\Form;

use AppBundle\Entity\Localidad;
use AppBundle\Entity\Provincia;
use AppBundle\Entity\TipoDocumento;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class PersonaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('tipoDocumento', TipoDocumento::class,
                    array(
                        'label' => 'Tipo Documento',
                        'required' => FALSE,
                        'empty_value' => '-- Seleccione --',
                        'query_builder' => function (EntityRepository $er) {        
                            return $er->createQueryBuilder('td')->where('td.fechaBaja is null')->orderBy('td.tipo', 'ASC');     
                         },
                    ))       
            ->add('numeroDocumento')
            ->add('sexo', 'choice', 
                array(
                        'choices' => array('m' => 'Masculino', 'f' => 'Femenino'), 
                        'required' => FALSE,
                        'empty_value' => '-- Seleccione --',
                    ))
            ->add('nombre')
            ->add('apellido')            
            ->add('fechaNacimiento','date', array(                        
                'constraints' => null,
                'data' => (isset($options['data']) && 
                                $options['data']->getFechaNacimiento() !== null) ? $options['data']->getFechaNacimiento() : null,
                 // render as a single text box
                 'widget' => 'single_text',
                 'format' => 'dd/MM/yyyy',
                 // do not render as type="date", to avoid HTML5 date pickers
                 'html5' => false,
                 // add a class that can be selected in JavaScript
                 'attr' => ['class' => 'js-datepicker']
                )
            )
            ->add('domicilioCalle')
            ->add('domicilioNumero')      
            
            ->add('jubilado', 'choice', 
                     array('choices' => array(1 => 'Si', 0 => 'No'),
                           'required' => true,
                           'multiple' => false,
                           'expanded' => true                           
                           )                   
                     )
            ->add('telefono')
            ->add('email')                                   
                    ->add('provincia', Provincia::class,array(
                        'empty_value' => '-- Seleccione --',
                        'query_builder' => function (EntityRepository $er) {        
                            return $er->createQueryBuilder('p')->orderBy('p.nombre', 'ASC');     
                         },
                         'required' => TRUE                
                        ))
                    ->add('localidad', Localidad::class,array(
                                'empty_value' => '-- Seleccione --',
                                'query_builder' => function (EntityRepository $er) {        
                                    return $er->createQueryBuilder('l')->orderBy('l.l_nom_dis', 'ASC');     
                                 },
                                 'required' => FALSE                 
                                ))                    
                    
                    ->add('localidadOtraProvincia','text', array(
                        'label' => 'Localidad Otra Provincia',
                        'required' => FALSE                              
                    ));
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Persona'
        ));
    }
}
