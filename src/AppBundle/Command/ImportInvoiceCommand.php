<?php

namespace AppBundle\Command;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportInvoiceCommand.
 *
 * @category Command
 */
class ImportInvoiceCommand extends BaseCommand
{
    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName('app:import:invoice')
            ->setDescription('Import an invoice from XLS file')
            ->addArgument(
                'filepath',
                InputArgument::REQUIRED,
                'The XLS file path to import in database'
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will persist invoices into database'
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
            $output->writeln('<comment>--force option enabled (this option persists invoices into database)</comment>');
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
            $spreadsheet = $this->ss->loadWorksheetsXlsSpreadsheetReadOnly($filename, array('Lineas_Facturas_Emitidas', 'Facturas_Emitidas'));
            // intialize counters
            $dtStart = new \DateTime();
            $totalItemsCounter = 0;
            $totalCustomersFound = 0;
            $created = 0;
            /** @var Worksheet $ws */
            $ws = $spreadsheet->setActiveSheetIndexByName('Facturas_Emitidas');
            $output->writeln($ws->getTitle());
            /** @var Row $row */
            foreach ($ws->getRowIterator() as $row) {
                $totalItemsCounter++;
                // search customer
                $customerId = $ws->getCellByColumnAndRow(77, $row->getRowIndex());
                $output->write('seraching customer '.$customerId.'... ');
                if ($customerId) {
                    $customer = $this->em->getRepository('AppBundle:Customer')->findOneBy(array('tic' => $customerId));
                    if ($customer) {
                        $totalCustomersFound++;
                        $output->writeln('<info>OK</info>');
                        // build imported invoice

                    } else {
                        $output->writeln('<error>KO</error>');
                    }
                } else {
                    $output->writeln('<error>KO</error>');
                }
            }

//            $worksheetIterator = $spreadsheet->getWorksheetIterator();
//            /** @var Worksheet $worksheet */
//            foreach ($worksheetIterator as $worksheet) {
//                $output->writeln($worksheet->getTitle());
//                /** @var Row $row */
//                foreach ($worksheet->getRowIterator() as $row) {
//                    $output->writeln($row->getRowIndex());
//                    /** @var Cell $cell */
//                    foreach ($row->getCellIterator() as $cell) {
//                        $output->write($cell->getValue().' · ');
//                    }
//                    $output->writeln('EOL');
//                }
//            }
        } catch (ReaderException $e) {
            $output->writeln('<error>XLS Reader Excetion: '.$e->getMessage().'</error>');
        } catch (\Exception $e) {
            $output->writeln('<error>Excetion: '.$e->getMessage().'</error>');
        }

        // EOF
        $this->printEndCommand($output);
    }
}
