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
        $currentDate = new \DateTime();
        $maleAmount = $this->em->getRepository('AppBundle:Coworker')->getEnabledMaleCoworkersAmount();
        $femaleAmount = $this->em->getRepository('AppBundle:Coworker')->getEnabledFemaleCoworkersAmount();
        $januaryAmount = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(1);
        $februaryAmount = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(2);
        $marchAmount = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(3);
        $aprilAmount = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(4);
        $mayAmount = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(5);
        $juneAmount = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(6);
        $julyAmount = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(7);
        $augustAmount = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(8);
        $septemberAmount = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(9);
        $octoberAmount = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(10);
        $novemberAmount = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(11);
        $decemberAmount = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(12);
        $yearAmount = $januaryAmount + $februaryAmount + $marchAmount + $aprilAmount + $mayAmount + $juneAmount + $julyAmount + $augustAmount + $septemberAmount + $octoberAmount + $novemberAmount + $decemberAmount;
        $coworkersBirthday = $this->em->getRepository('AppBundle:Coworker')->getAllCoworkersBirthdayByMonth($currentDate->format('n'));

        return $this->renderResponse(
            $blockContext->getTemplate(),
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'title' => 'Coworker Indicators Block',
                'maleAmount' => round(($maleAmount / ($maleAmount + $femaleAmount)) * 100, 0),
                'femaleAmount' => round(($femaleAmount / ($maleAmount + $femaleAmount)) * 100, 0),
                'januaryAmount' => round(($januaryAmount / $yearAmount) * 100, 0),
                'februaryAmount' => round(($februaryAmount / $yearAmount) * 100, 0),
                'marchAmount' => round(($marchAmount / $yearAmount) * 100, 0),
                'coworkersBirthday' => $coworkersBirthday,
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
