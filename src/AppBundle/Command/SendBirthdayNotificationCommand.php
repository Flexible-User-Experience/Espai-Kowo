<?php

namespace AppBundle\Command;

use AppBundle\Entity\Coworker;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendBirthdayNotificationCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    protected $em;

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
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Welcome to send a notification command</info>');

        if ($input->getOption('delivery') === true) {
            $output->writeln(
                '<comment>--delivery option enabled (the notifications will be sent by mail)</comment>'
            );
        }

        // Command Vars
        $this->em   = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $itemsFound = 0;
        $currentDate = new \DateTime();

        $coworkersBirthday = $this->em->getRepository('AppBundle:Coworker')->getAllEnabledCoworkersBirthdayByDayAndMonth($currentDate->format('j'), $currentDate->format('n'));

        /** @var Coworker $coworker */
        foreach ($coworkersBirthday as $coworker) {
            $output->writeln('Aniversari del/la coworker: ' . $coworker->getName() . ' ' . $coworker->getSurname());
        }

        $output->writeln('<comment>END OF FILE.</comment>');

        return true;
    }
}
