<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CoworkerDataFormType.
 *
 * @category FormType
 *
 * @author   David Romaní <david@flux.cat>
 */
class CoworkerDataFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'category',
                null,
                array(
                    'label' => false,
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'frontend.forms.category',
                    ),
                )
            )
            ->add(
                'description',
                TextareaType::class,
                array(
                    'label' => false,
                    'required' => true,
                    'attr' => array(
                        'rows' => 5,
                        'placeholder' => 'frontend.forms.message',
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                )
            )
            ->add(
                'birthday',
                null,
                array(
                )
            )
            ->add(
                'printerCode',
                null,
                array(
                )
            )
            ->add(
                'send',
                SubmitType::class,
                array(
                    'label' => 'frontend.forms.send',
                    'attr' => array(
                        'class' => 'btn-kowo',
                    ),
                )
            )
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'coworker_data_form';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Coworker',
            )
        );
    }
}
