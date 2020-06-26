<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Persona;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PersonaLicenciaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id','hidden')
            ->add('nombre')
            ->add('apellido')
            ->add('fechaNacimiento', 'date', array( 'label'=>'Fecha de Nacimiento',
                    'required' => FALSE,
                    // render as a single text box
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    // do not render as type="date", to avoid HTML5 date pickers
                    'html5' => false,
                    // add a class that can be selected in JavaScript
                    'attr' => ['class' => 'js-datepicker'],))
            ->add('tipoDocumento', 'entity', 
                    array(
                        'label' => 'Tipo Documento',
                        'class' => 'AppBundle:TipoDocumento',
                        'required' => FALSE,
                        'empty_value' => '-- Seleccione --',
                        'query_builder' => function (EntityRepository $er) {        
                            return $er->createQueryBuilder('td')->where('td.fechaBaja is null')->orderBy('td.tipo', 'ASC');     
                         },
                    ))            
            ->add('numeroDocumento', 'text', array('label' => 'Número de Documento', 'attr'=>array('placeholder'=>'99999999')))
            ->add('domicilioCalle')
            ->add('domicilioNumero')
            ->add('sexo', 'choice', 
                array(
                        'choices' => array('m' => 'Masculino', 'f' => 'Femenino'), 
                        'required' => FALSE,
                        'empty_value' => '-- Seleccione --',
                    ))
            ->add('jubilado', 'choice', 
                     array('choices' => array(1 => 'Si', 0 => 'No'),
                           'required' => true,
                           'multiple' => false,
                           'expanded' => true                           
                           )                   
                     )
            ->add('telefono', 'text', array('label' => 'Teléfono', 'required' => FALSE, 'attr'=>array('placeholder'=>'3420000000')))
            ->add('email', EmailType::class,
                array('required' => FALSE))
            ->add('provincia', EntityType::class,array(
                'class' => 'AppBundle:Provincia',
                'empty_value' => '-- Seleccione --',
                'query_builder' => function (EntityRepository $er) {        
                    return $er->createQueryBuilder('p')->orderBy('p.nombre', 'ASC');     
                 },
                 'required' => TRUE                
                ))
            ->add('localidad', EntityType::class,array(
                        'class' => 'AppBundle:Localidad',
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
        
            
    }

    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Persona',
            'cascade_validation' => true,
            'error_bubbling' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mprod_licenciacypbundle_persona';
    }
}
