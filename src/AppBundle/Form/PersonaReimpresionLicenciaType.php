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

class PersonaReimpresionLicenciaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('tipoDocumento', 'entity', 
                    array(
                        'label' => 'Tipo Documento',
                        'class' => 'AppBundle\Entity\TipoDocumento',
                        'required' => true,
                        'empty_value' => '-- Seleccione --',
                        'query_builder' => function (EntityRepository $er) {        
                            return $er->createQueryBuilder('td')->where('td.fechaBaja is null')->orderBy('td.tipo', 'ASC');     
                         },
                    ))            
            ->add('numeroDocumento', 'text', 
                    array('label' => 'Número de Documento','required' => true, 
                            'attr'=>array('placeholder'=>'9999999', 'minlength'=>'6')))          
            ->add('sexo', 'choice', 
                array(
                        'choices' => array('m' => 'Masculino', 'f' => 'Femenino'), 
                        'required' => true,
                        'empty_value' => '-- Seleccione --',
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
