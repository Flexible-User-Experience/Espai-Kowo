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
        $januaryTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(1);
        $februaryTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(2);
        $marchTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(3);
        $aprilTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(4);
        $mayTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(5);
        $juneTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(6);
        $julyTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(7);
        $augustTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(8);
        $septemberTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(9);
        $octoberTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(10);
        $novemberTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(11);
        $decemberTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(12);
        $yearTakeUp = $januaryTakeUp + $februaryTakeUp + $marchTakeUp + $aprilTakeUp + $mayTakeUp + $juneTakeUp + $julyTakeUp + $augustTakeUp + $septemberTakeUp + $octoberTakeUp + $novemberTakeUp + $decemberTakeUp;
        $januaryDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(1);
        $februaryDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(2);
        $marchDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(3);
        $aprilDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(4);
        $mayDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(5);
        $juneDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(6);
        $julyDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(7);
        $augustDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(8);
        $septemberDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(9);
        $octoberDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(10);
        $novemberDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(11);
        $decemberDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(12);
        $yearDischarge = $januaryDischarge + $februaryDischarge + $marchDischarge + $aprilDischarge + $mayDischarge + $juneDischarge + $julyDischarge + $augustDischarge + $septemberDischarge + $octoberDischarge + $novemberDischarge + $decemberDischarge;
        $coworkersBirthday = $this->em->getRepository('AppBundle:Coworker')->getAllCoworkersBirthdayByMonth($currentDate->format('n'));

        return $this->renderResponse(
            $blockContext->getTemplate(),
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'title' => 'Coworker Indicators Block',
                'maleAmount' => round(($maleAmount / ($maleAmount + $femaleAmount)) * 100, 0),
                'femaleAmount' => round(($femaleAmount / ($maleAmount + $femaleAmount)) * 100, 0),
                'januaryTakeUp' => round(($januaryTakeUp / $yearTakeUp) * 100, 0),
                'februaryTakeUp' => round(($februaryTakeUp / $yearTakeUp) * 100, 0),
                'marchTakeUp' => round(($marchTakeUp / $yearTakeUp) * 100, 0),
                'januaryDischarge' => round(($januaryDischarge / $yearDischarge) * 100, 0),
                'februaryDischarge' => round(($februaryDischarge / $yearDischarge) * 100, 0),
                'marchDischarge' => round(($marchDischarge / $yearDischarge) * 100, 0),
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
