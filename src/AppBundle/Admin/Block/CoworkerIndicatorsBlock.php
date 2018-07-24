<?php

namespace AppBundle\Admin\Block;

use AppBundle\Enum\MonthEnum;
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
                'maleAmount' => $this->solveAverage($maleAmount, $maleAmount + $femaleAmount),
                'femaleAmount' => $this->solveAverage($femaleAmount, $maleAmount + $femaleAmount),
                'januaryTakeUp' => $this->solveAverage($januaryTakeUp, $januaryTakeUp + $januaryDischarge),
                'februaryTakeUp' => $this->solveAverage($februaryTakeUp, $februaryTakeUp + $februaryDischarge),
                'marchTakeUp' => $this->solveAverage($marchTakeUp, $marchTakeUp + $marchDischarge),
                'aprilTakeUp' => $this->solveAverage($aprilTakeUp, $aprilTakeUp + $aprilDischarge),
                'mayTakeUp' => $this->solveAverage($mayTakeUp, $mayTakeUp + $mayDischarge),
                'juneTakeUp' => $this->solveAverage($juneTakeUp, $juneTakeUp + $juneDischarge),
                'julyTakeUp' => $this->solveAverage($julyTakeUp, $julyTakeUp + $julyDischarge),
                'augustTakeUp' => $this->solveAverage($augustTakeUp, $augustTakeUp + $augustDischarge),
                'septemberTakeUp' => $this->solveAverage($septemberTakeUp, $septemberTakeUp + $septemberDischarge),
                'octoberTakeUp' => $this->solveAverage($octoberTakeUp, $octoberTakeUp + $octoberDischarge),
                'novemberTakeUp' => $this->solveAverage($novemberTakeUp, $novemberTakeUp + $novemberDischarge),
                'decemberTakeUp' => $this->solveAverage($decemberTakeUp, $decemberTakeUp + $decemberDischarge),
                'januaryDischarge' => $this->solveAverage($januaryDischarge, $januaryTakeUp + $januaryDischarge),
                'februaryDischarge' => $this->solveAverage($februaryDischarge, $februaryTakeUp + $februaryDischarge),
                'marchDischarge' => $this->solveAverage($marchDischarge, $marchTakeUp + $marchDischarge),
                'aprilDischarge' => $this->solveAverage($aprilDischarge, $aprilTakeUp + $aprilDischarge),
                'mayDischarge' => $this->solveAverage($mayDischarge, $mayTakeUp + $mayDischarge),
                'juneDischarge' => $this->solveAverage($juneDischarge, $juneTakeUp + $juneDischarge),
                'julyDischarge' => $this->solveAverage($julyDischarge, $julyTakeUp + $julyDischarge),
                'augustDischarge' => $this->solveAverage($augustDischarge, $augustTakeUp + $augustDischarge),
                'septemberDischarge' => $this->solveAverage($septemberDischarge, $septemberTakeUp + $septemberDischarge),
                'octoberDischarge' => $this->solveAverage($octoberDischarge, $octoberTakeUp + $octoberDischarge),
                'novemberDischarge' => $this->solveAverage($novemberDischarge, $novemberTakeUp + $novemberDischarge),
                'decemberDischarge' => $this->solveAverage($decemberDischarge, $decemberTakeUp + $decemberDischarge),
                'coworkersBirthday' => $coworkersBirthday,
                'currentDate' => MonthEnum::getTranslatedMonthEnumArray()[intval($currentDate->format('n'))],
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

    /**
     * @param float|int $dividend
     * @param float|int $divider
     *
     * @return float|int
     */
    private function solveAverage($dividend, $divider)
    {
        if ($divider == 0) {
            return 0;
        }

        return round(($dividend / $divider) * 100, 0);
    }
}
