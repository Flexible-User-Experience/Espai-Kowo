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
        $januaryTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(01);
        $februaryTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(02);
        $marchTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(03);
        $aprilTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(04);
        $mayTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(05);
        $juneTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(06);
        $julyTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(07);
        $augustTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(8);
        $septemberTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(9);
        $octoberTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(10);
        $novemberTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(11);
        $decemberTakeUp = $this->em->getRepository('AppBundle:Coworker')->getNewCoworkerAmountByMonth(12);
        $yearTakeUp = $januaryTakeUp + $februaryTakeUp + $marchTakeUp + $aprilTakeUp + $mayTakeUp + $juneTakeUp + $julyTakeUp + $augustTakeUp + $septemberTakeUp + $octoberTakeUp + $novemberTakeUp + $decemberTakeUp;
        $januaryDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(01);
        $februaryDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(02);
        $marchDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(03);
        $aprilDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(04);
        $mayDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(05);
        $juneDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(06);
        $julyDischarge = $this->em->getRepository('AppBundle:Coworker')->getDischargeCoworkerAmountByMonth(07);
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
                'aprilTakeUp' => round(($aprilTakeUp / $yearTakeUp) * 100, 0),
                'mayTakeUp' => round(($mayTakeUp / $yearTakeUp) * 100, 0),
                'juneTakeUp' => round(($juneTakeUp / $yearTakeUp) * 100, 0),
                'julyTakeUp' => round(($julyTakeUp / $yearTakeUp) * 100, 0),
                'augustTakeUp' => round(($augustTakeUp / $yearTakeUp) * 100, 0),
                'septemberTakeUp' => round(($septemberTakeUp / $yearTakeUp) * 100, 0),
                'octoberTakeUp' => round(($octoberTakeUp / $yearTakeUp) * 100, 0),
                'novemberTakeUp' => round(($novemberTakeUp / $yearTakeUp) * 100, 0),
                'decemberTakeUp' => round(($decemberTakeUp / $yearTakeUp) * 100, 0),
                'januaryDischarge' => round(($januaryDischarge / $yearDischarge) * 100, 0),
                'februaryDischarge' => round(($februaryDischarge / $yearDischarge) * 100, 0),
                'marchDischarge' => round(($marchDischarge / $yearDischarge) * 100, 0),
                'aprilDischarge' => round(($aprilDischarge / $yearDischarge) * 100, 0),
                'mayDischarge' => round(($mayDischarge / $yearDischarge) * 100, 0),
                'juneDischarge' => round(($juneDischarge / $yearDischarge) * 100, 0),
                'julyDischarge' => round(($julyDischarge / $yearDischarge) * 100, 0),
                'augustDischarge' => round(($augustDischarge / $yearDischarge) * 100, 0),
                'septemberDischarge' => round(($septemberDischarge / $yearDischarge) * 100, 0),
                'octoberDischarge' => round(($octoberDischarge / $yearDischarge) * 100, 0),
                'novemberDischarge' => round(($novemberDischarge / $yearDischarge) * 100, 0),
                'decemberDischarge' => round(($decemberDischarge / $yearDischarge) * 100, 0),
                'coworkersBirthday' => $coworkersBirthday,
                'currentDate' => $currentDate->format('F'),
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
