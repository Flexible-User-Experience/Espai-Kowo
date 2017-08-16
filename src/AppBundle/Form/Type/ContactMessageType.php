<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ContactMessageType.
 *
 * @category FormType
 *
 * @author   David Romaní <david@flux.cat>
 */
class ContactMessageType extends ContactHomepageType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add(
                'message',
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
            );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'contact_message';
    }
}
