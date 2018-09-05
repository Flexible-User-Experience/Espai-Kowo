<?php

namespace AppBundle\Command;

use AppBundle\Entity\Coworker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SendBirthdayNotificationCommand
 *
 * @category Command
 */
class SendBirthdayNotificationCommand extends BaseCommand
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('app:coworker:birthday:notification')
            ->setDescription('Send a notification to all the birthday coworkers in current date')
            ->addOption(
                'delivery',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will deliver the email'
            );
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|bool|void
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setUp();
        $this->rs->getContext()->setHost($this->getContainer()->getParameter('mailer_url_base'));
        $currentDate = new \DateTime();

        $this->printBeginCommand($output);
        if ($input->getOption('delivery') === true) {
            $output->writeln(
                '<comment>--delivery option enabled (the notifications will be sent by mail)</comment>'
            );
        }

        // Coworker notification to send current day
        $coworkersBirthday = $this->em->getRepository('AppBundle:Coworker')->getAllCoworkersBirthdayByDayAndMonth($currentDate->format('j'), $currentDate->format('n'));
        /** @var Coworker $coworker */
        foreach ($coworkersBirthday as $coworker) {
            $output->writeln('Aniversari del/la coworker: '.$coworker->getName().' '.$coworker->getSurname());
            if ($input->getOption('delivery') === true) {
                $this->ns->sendCoworkerBirthdayNotification($coworker);
            }
        }

        // Admin notification to send the day before
        $dateInterval = new \DateInterval('P1D');
        $dayBefore = $currentDate->add($dateInterval);
        $coworkersDayBeforeBirthday = $this->em->getRepository('AppBundle:Coworker')->getAllCoworkersBirthdayByDayAndMonth($dayBefore->format('j'), $dayBefore->format('n'));
        /** @var Coworker $coworker */
        foreach ($coworkersDayBeforeBirthday as $coworker) {
            $output->writeln('Demà és l\'aniversari del/la coworker: '.$coworker->getName().' '.$coworker->getSurname());
            if ($input->getOption('delivery') === true) {
                $this->ns->sendAdminBirthdayNotification($coworker);
            }
        }

        $this->printEndCommand($output);
    }
}
