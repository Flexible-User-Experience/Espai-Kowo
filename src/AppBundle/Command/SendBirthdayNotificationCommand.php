<?php

namespace AppBundle\Command;

use AppBundle\Entity\Coworker;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SendBirthdayNotificationCommand
 *
 * @category Command
 */
class SendBirthdayNotificationCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Methods
     */

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
     * @return int|null|bool|void
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Router $router */
        $router = $this->getContainer()->get('router');
        $router->getContext()->setHost($this->getContainer()->getParameter('mailer_url_base'));

        $output->writeln('<info>Welcome to send a notification command</info>');

        if ($input->getOption('delivery') === true) {
            $output->writeln(
                '<comment>--delivery option enabled (the notifications will be sent by mail)</comment>'
            );
        }

        // Command Vars
        $this->em   = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $currentDate = new \DateTime();
        $messenger = $this->getContainer()->get('app.notification');

        $coworkersBirthday = $this->em->getRepository('AppBundle:Coworker')->getAllCoworkersBirthdayByDayAndMonth($currentDate->format('j'), $currentDate->format('n'));

        /** @var Coworker $coworker */
        foreach ($coworkersBirthday as $coworker) {
            $output->writeln('Aniversari del/la coworker: ' . $coworker->getName() . ' ' . $coworker->getSurname());
            if ($input->getOption('delivery') === true) {
                $messenger->sendCoworkerBirthdayNotification($coworker);
            }
        }

        $dateInterval = new \DateInterval('P1D');
        $dayBefore = $currentDate->add($dateInterval);

        $coworkersDayBeforeBirthday = $this->em->getRepository('AppBundle:Coworker')->getAllCoworkersBirthdayByDayAndMonth($dayBefore->format('j'), $dayBefore->format('n'));

        /** @var Coworker $coworker */
        foreach ($coworkersDayBeforeBirthday as $coworker) {
            $output->writeln('Demà és l\'aniversari del/la coworker: ' . $coworker->getName() . ' ' . $coworker->getSurname());
            if ($input->getOption('delivery') === true) {
                $messenger->sendAdminBirthdayNotification($coworker);
            }
        }

        $output->writeln('<comment>END OF FILE.</comment>');
    }
}
