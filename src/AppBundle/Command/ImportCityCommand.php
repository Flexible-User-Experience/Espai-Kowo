<?php

namespace AppBundle\Command;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\InvoiceLine;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportCityCommand.
 *
 * @category Command
 */
class ImportCityCommand extends BaseCommand
{
    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName('app:import:city')
            ->setDescription('Import a city from XLS file')
            ->addArgument(
                'filepath',
                InputArgument::REQUIRED,
                'The XLS file path to import in database'
            )
            ->addArgument(
                'worksheet',
                InputArgument::REQUIRED,
                'The worksheet\'s names to read'
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will persist cities into database'
            )
        ;
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Welcome
        $this->setUp();
        $this->printBeginCommand($output);
        if ($input->getOption('force')) {
            $output->writeln('<comment>--force option enabled (this option persists cities into database)</comment>');
        }

        // Validate arguments
        $filename = $input->getArgument('filepath');
        if (!$this->fs->exists($filename)) {
            throw new \InvalidArgumentException('The file '.$filename.' does not exist');
        }

        // Main loop
        $output->writeln('Loading data, please wait...');
        try {
            // load file
            $spreadsheet = $this->ss->loadWorksheetsXlsSpreadsheetReadOnly($filename, $input->getArgument('worksheet'));
            // intialize counters
            $dtStart = new \DateTime();
            $totalItemsCounter = 0;
            $totalCitiesFound = 0;
            $created = 0;
            $updated = 0;

            // cities
            /** @var Worksheet $ws */
            $ws = $spreadsheet->setActiveSheetIndexByName($input->getArgument('worksheet'));
            $output->writeln($ws->getTitle());
            /** @var Row $row */
            foreach ($ws->getRowIterator() as $row) {
                $totalItemsCounter++;
                // search city
                $cityPostaCode = $ws->getCellByColumnAndRow(3, $row->getRowIndex())->getValue();
                $output->write('seraching city '.$cityPostaCode.'... ');
                if ($cityPostaCode) {
                    $city = $this->em->getRepository('AppBundle:City')->findOneBy(array('postalCode' => $cityPostaCode));
                    if ($city) {
                        // update city
                        $updated++;
                        $output->writeln('<info>found</info>');
                    } else {
                        // new city
                        $created++;
                        $output->writeln('<comment>not found</comment>');
                    }
                }
            }

            // EOF
            $dtEnd = new \DateTime();
            $output->writeln('<info>---------------------------</info>');
            $output->writeln('<info>'.$totalItemsCounter.' items parsed</info>');
            $output->writeln('<info>'.$created.' new cities added</info>');
            $output->writeln('<info>'.$updated.' cities updated</info>');
            $output->writeln('<info>---------------------------</info>');
            $output->writeln('Total ellapsed time: '.$dtStart->diff($dtEnd)->format('%H:%I:%S'));
            $this->printEndCommand($output);

        } catch (ReaderException $e) {
            $output->writeln('<error>XLS Reader Excetion: '.$e->getMessage().'</error>');
        } catch (\Exception $e) {
            $output->writeln('<error>Excetion: '.$e->getMessage().'</error>');
        }
    }
}
