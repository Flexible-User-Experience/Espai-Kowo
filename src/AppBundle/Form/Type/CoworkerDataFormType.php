<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Coworker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
                    'label' => 'frontend.forms.category',
                    'required' => false,
                )
            )
            ->add(
                'description',
                TextareaType::class,
                array(
                    'label' => 'frontend.forms.description',
                    'required' => false,
                    'attr' => array(
                        'rows' => 5,
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                )
            )
            ->add(
                'birthday',
                BirthdayType::class,
                array(
                    'label' => 'frontend.forms.birthdate',
                )
            )
            ->add(
                'printerCode',
                null,
                array(
                    'label' => 'frontend.forms.printercode',
                )
            )
            ->add(
                'socialNetworks',
                CollectionType::class,
                array(
                    'label' => ' ',
                    'entry_type' => CoworkerDataSocialNetworkFormType::class,
                    'allow_add' => true,
                    'by_reference' => false,
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
                'data_class' => Coworker::class,
            )
        );
    }
}
