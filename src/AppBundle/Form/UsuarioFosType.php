<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UsuarioFosType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rolesArray', null,
            [
                'choice_label' => function ($rol) {
                    return $rol->getNombre();
                },
                'label' => 'Roles',
                'multiple' => true,
                'expanded' => true
                ]
            );
        if ($options['submit'])
            $builder
                ->add('submit', SubmitType::class,
                    ['label' => 'Enviar']
                );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Usuario',
            'submit' => false,
            'password' => true
        ));
    }
  

}
