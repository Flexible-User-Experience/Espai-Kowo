<?php

namespace AppBundle\Command;

use AppBundle\Service\NotificationService;
use AppBundle\Service\SpreadsheetService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Router;

/**
 * Class BaseCommand.
 */
abstract class BaseCommand extends ContainerAwareCommand
{
    const CSV_DELIMITER = ';';
    const CSV_ENCLOSURE = '"';
    const CSV_ESCAPE = '\\';
    const IMPORT_BATCH_WINDOW = 100; // Doctrine flush & clear iteration trigger

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Router
     */
    protected $rs;

    /**
     * @var NotificationService
     */
    protected $ns;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var SpreadsheetService
     */
    protected $ss;

    /**
     * Methods.
     */

    /**
     * Set up
     */
    public function setUp()
    {
        $this->em = $this->getEntityManager();
        $this->rs = $this->getRouterService();
        $this->ns = $this->getNotificationService();
        $this->fs = $this->getFilesystemService();
        $this->ss = $this->getSpreadsheetService();
    }

    /**
     * Get current timestamp string with format Y/m/d H:i:s.
     *
     * @return string
     */
    public function getCurrentTimestampString()
    {
        $cm = new \DateTime();

        return $cm->format('Y/m/d H:i:s');
    }

    /**
     * @param OutputInterface $output
     */
    public function printBeginCommand(OutputInterface $output)
    {
        $output->writeln('<info>Welcome to '.$this->getName().' command</info>');
    }

    /**
     * @param OutputInterface $output
     */
    public function printEndCommand(OutputInterface $output)
    {
        $output->writeln('<info>EOF</info>');
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    /**
     * @return Router
     */
    private function getRouterService()
    {
        return $this->getContainer()->get('router');
    }

    /**
     * @return NotificationService
     */
    private function getNotificationService()
    {
        return $this->getContainer()->get('app.notification');
    }

    /**
     * @return Filesystem
     */
    private function getFilesystemService()
    {
        return $this->getContainer()->get('filesystem');
    }

    /**
     * @return SpreadsheetService
     */
    private function getSpreadsheetService()
    {
        return $this->getContainer()->get('app.spreadsheet_factory');
    }
}
