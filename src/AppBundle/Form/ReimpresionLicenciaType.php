<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class ReimpresionLicenciaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder         
            ->add('tipoLicencia', EntityType::class, array(
                'class' => 'AppBundle\Entity\TipoLicencia',
                'choice_label' => 'descripcion',
                'placeholder' => '-- Seleccione --',
                'empty_data' => null,
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('tl')->where('tl.isActive =1')->orderBy('tl.descripcion', 'ASC');
                }
            ))
            ->add('persona', PersonaReimpresionLicenciaType::class);
                        

            $builder->setMethod("GET");
    }
   
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
