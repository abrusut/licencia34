<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
            ->add('id',HiddenType::class)
            ->add('nombre')
            ->add('apellido')
            ->add('fechaNacimiento', DateType::class, array( 'label'=>'Fecha de Nacimiento',
                    'required' => FALSE,
                    // render as a single text box
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    // do not render as type="date", to avoid HTML5 date pickers
                    'html5' => false,
                    // add a class that can be selected in JavaScript
                    'attr' => ['class' => 'js-datepicker'],))
            ->add('tipoDocumento', EntityType::class,
                    array(
                        'label' => 'Tipo Documento',
                        'class' => 'AppBundle:TipoDocumento',
                        'required' => FALSE,
                        'placeholder' => '-- Seleccione --',
                        'query_builder' => function (EntityRepository $er) {        
                            return $er->createQueryBuilder('td')->where('td.fechaBaja is null')->orderBy('td.tipo', 'ASC');     
                         },
                    ))
            ->add('numeroDocumento', TextType::class, array('label' => 'Número de Documento', 'attr'=>array('placeholder'=>'99999999')))
            ->add('domicilioCalle')
            ->add('domicilioNumero')
            ->add('sexo', ChoiceType::class,
                array(
                        'choices' => array('Masculino' => 'm', 'Femenino' => 'f'),
                        'required' => FALSE,
                        'placeholder' => '-- Seleccione --',
                    ))
            ->add('jubilado', ChoiceType::class,
                     array('choices' => array(1 => 'Si', 0 => 'No'),
                           'required' => true,
                           'multiple' => false,
                           'expanded' => true                           
                           )                   
                     )
            ->add('telefono', TextType::class, array('label' => 'Teléfono', 'required' => FALSE, 'attr'=>array('placeholder'=>'3420000000')))
            ->add('email', EmailType::class,
                array('required' => FALSE))
            ->add('provincia', EntityType::class,array(
                'class' => 'AppBundle:Provincia',
                'placeholder' => '-- Seleccione --',
                'query_builder' => function (EntityRepository $er) {        
                    return $er->createQueryBuilder('p')->orderBy('p.nombre', 'ASC');     
                 },
                 'required' => TRUE                
                ))
            ->add('localidad', EntityType::class,array(
                        'class' => 'AppBundle:Localidad',
                        'placeholder' => '-- Seleccione --',
                        'query_builder' => function (EntityRepository $er) {        
                            return $er->createQueryBuilder('l')->orderBy('l.l_nom_dis', 'ASC');     
                         },
                         'required' => FALSE                 
                        ))                    
            
            ->add('localidadOtraProvincia',TextType::class, array(
                'label' => 'Localidad Otra Provincia',
                'required' => FALSE                              
            ));
        
            
    }

    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Persona',
            'cascade_validation' => true,
            'error_bubbling' => true
        ));
    }
}
