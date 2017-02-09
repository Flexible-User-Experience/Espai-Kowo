<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\SocialNetwork;
use AppBundle\Entity\SocialNetworkCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CoworkerDataSocialNetworkFormType.
 *
 * @category FormType
 *
 * @author   David Romaní <david@flux.cat>
 */
class CoworkerDataSocialNetworkFormType extends AbstractType
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
                EntityType::class,
                array(
                    'label' => 'frontend.forms.category',
                    'class' => SocialNetworkCategory::class,
                    'required' => true,
                )
            )
            ->add(
                'url',
                UrlType::class,
                array(
                    'label' => 'prorjpgjdlñkgj',
                )
            )
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'coworker_data_social_network_form';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => SocialNetwork::class,
            )
        );
    }
}
