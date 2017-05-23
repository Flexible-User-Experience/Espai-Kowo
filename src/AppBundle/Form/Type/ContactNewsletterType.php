<?php

namespace AppBundle\Form\Type;

use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaType;
use Beelab\Recaptcha2Bundle\Validator\Constraints\Recaptcha2;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ContactNewsletterType
 *
 * @category FormType
 * @package  AppBundle\Form\Type
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class ContactNewsletterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                array(
                    'label'       => false,
                    'required'    => true,
                    'attr'        => array(
                        'placeholder' => 'frontend.forms.name',
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                )
            )
            ->add(
                'email',
                EmailType::class,
                array(
                    'label'       => false,
                    'required'    => true,
                    'attr'        => array(
                        'placeholder' => 'frontend.forms.email',
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Email(array(
                            'strict'    => true,
                            'checkMX'   => true,
                            'checkHost' => true,
                        )),
                    ),
                )
            )
            ->add(
                'captcha',
                RecaptchaType::class,
                array(
                    'mapped' => false,
                    'label' => false,
                    'constraints' => array(
                        new Recaptcha2(),
                    ),
                    'attr' => array(
                        'data-size' => 'compact'
                    )
                )
            )
            ->add(
                'send',
                SubmitType::class,
                array(
                    'label' => 'frontend.forms.news',
                )
            );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'contact_newsletter';
    }
}
