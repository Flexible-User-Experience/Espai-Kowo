<?php

namespace AppBundle\Command;

use AppBundle\Entity\Spending;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportSpendingCommand.
 *
 * @category Command
 */
class ImportSpendingCommand extends BaseCommand
{
    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName('app:import:spending')
            ->setDescription('Import a spending from XLS file')
            ->addArgument(
                'filepath',
                InputArgument::REQUIRED,
                'The XLS file path to import in database'
            )
            ->addArgument(
                'worksheets',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'The worksheet\'s names to read (separate multiple names with a space)'
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will persist spendings into database'
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
            $output->writeln('<comment>--force option enabled (this option persists spendings into database)</comment>');
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
            $spreadsheet = $this->ss->loadWorksheetsXlsSpreadsheetReadOnly($filename, $input->getArgument('worksheets'));
            // intialize counters
            $dtStart = new \DateTime();
            $totalItemsCounter = 0;
            $totalProvidersFound = 0;
            $created = 0;
            $updated = 0;

            // spendings
            /** @var Worksheet $ws */
            $ws = $spreadsheet->setActiveSheetIndexByName('Facturas_Recibidas');
            $output->writeln($ws->getTitle());
            /** @var Row $row */
            foreach ($ws->getRowIterator() as $row) {
                $totalItemsCounter++;
                // search provider
                $providerId = $ws->getCellByColumnAndRow(8, $row->getRowIndex())->getValue();
                $anfixInvoiceCode = $ws->getCellByColumnAndRow(63, $row->getRowIndex())->getValue();
                $output->write('seraching provider '.$providerId.'... ');
                if ($providerId) {
                    $provider = $this->em->getRepository('AppBundle:Provider')->findOneBy(array('tic' => $providerId));
                    if ($provider) {
                        $totalProvidersFound++;
                        $output->writeln('<info>OK</info>');
                        $output->write('seraching spending by anfix code '.$anfixInvoiceCode.'... ');
                        $searchedPreviouslySpending = $this->em->getRepository('AppBundle:Spending')->findOneBy(array('anfixCode' => $anfixInvoiceCode));
                        if ($searchedPreviouslySpending) {
                            // update spending
                            $output->writeln('<comment>found</comment>');
                            $updated++;
                            $importedSpending = $searchedPreviouslySpending;
                        } else {
                            // new spending
                            $output->writeln('<info>not found</info>');
                            $created++;
                            $importedSpending = new Spending();
                        }
                        // build imported spending
                        $spendingDateValue = $ws->getCellByColumnAndRow(14, $row->getRowIndex())->getValue();
                        $importedSpending
                            ->setAnfixCode($anfixInvoiceCode)
                            ->setDate(\DateTime::createFromFormat('Y-m-d H:i:s.u', $spendingDateValue))
                            ->setProvider($provider)
                            ->setBaseAmount(floatval($ws->getCellByColumnAndRow(80, $row->getRowIndex())->getValue()))
                            ->setTotalAmount(floatval($ws->getCellByColumnAndRow(61, $row->getRowIndex())->getValue()))
                            ->setPaymentMethod($provider->getPaymentMethod())
                            ->setIsPayed(true)
                            ->setEnabled(true)
                        ;
                        if ($input->getOption('force')) {
                            if (!$searchedPreviouslySpending) {
                                $this->em->persist($importedSpending);
                            }
                            $this->em->flush();
                        }
                    } else {
                        $output->writeln('<error>KO</error>');
                    }
                } else {
                    $output->writeln('<error>KO</error>');
                }
            }

            // EOF
            $dtEnd = new \DateTime();
            $output->writeln('<info>---------------------------</info>');
            $output->writeln('<info>'.$totalItemsCounter.' items parsed</info>');
            $output->writeln('<info>'.$created.' new spendings added</info>');
            $output->writeln('<info>'.$updated.' spendings updated</info>');
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
