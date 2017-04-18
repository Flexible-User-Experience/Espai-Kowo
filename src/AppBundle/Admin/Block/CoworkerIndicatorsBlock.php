<?php

namespace AppBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CoworkerIndicatorsBlock.
 *
 * @category Block
 *
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class CoworkerIndicatorsBlock extends AbstractBlockService
{
    /** @var EntityManager */
    private $em;

    /**
     * Constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     */
    public function __construct($name, EngineInterface $templating, EntityManager $em)
    {
        parent::__construct($name, $templating);
        $this->em = $em;
    }

    /**
     * Execute.
     *
     * @param BlockContextInterface $blockContext
     * @param Response              $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $maleAmount = $this->em->getRepository('AppBundle:Coworker')->getEnabledMaleCoworkersAmount();
        $femaleAmount = $this->em->getRepository('AppBundle:Coworker')->getEnabledFemaleCoworkersAmount();

        return $this->renderResponse(
            $blockContext->getTemplate(),
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'title' => 'Coworker Indicators Block',
                'maleAmount' => round(($maleAmount / ($maleAmount + $femaleAmount)) * 100, 0),
                'femaleAmount' => round(($femaleAmount / ($maleAmount + $femaleAmount)) * 100, 0),
            ),
            $response
        );
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'coworker_indicators';
    }

    /**
     * Set defaultSettings.
     *
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'title' => 'Coworker Indicators Block',
                'content' => 'Default content',
                'template' => ':Admin/Blocks:coworker_indicators.html.twig',
            )
        );
    }
}
