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
            ->addArgument(
                'worksheets',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'The worksheet\'s names to read (separate multiple names with a space)'
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
            $spreadsheet = $this->ss->loadWorksheetsXlsSpreadsheetReadOnly($filename, $input->getArgument('worksheets'));
            // intialize counters
            $dtStart = new \DateTime();
            $totalItemsCounter = 0;
            $totalCustomersFound = 0;
            $created = 0;
            $updated = 0;
            $linesCreated = 0;
            $linesUpdated = 0;

            // invoices
            /** @var Worksheet $ws */
            $ws = $spreadsheet->setActiveSheetIndexByName('Facturas_Emitidas');
            $output->writeln($ws->getTitle());
            /** @var Row $row */
            foreach ($ws->getRowIterator() as $row) {
                $totalItemsCounter++;
                // search customer
                $customerId = $ws->getCellByColumnAndRow(77, $row->getRowIndex())->getValue();
                $anfixInvoiceCode = $ws->getCellByColumnAndRow(8, $row->getRowIndex())->getValue();
                $output->write('seraching customer '.$customerId.'... ');
                if ($customerId) {
                    $customer = $this->em->getRepository('AppBundle:Customer')->findOneBy(array('tic' => $customerId));
                    if ($customer) {
                        $totalCustomersFound++;
                        $output->writeln('<info>OK</info>');
                        $output->write('seraching invoice by anfix code '.$anfixInvoiceCode.'... ');
                        $searchedPreviouslyInvoice = $this->em->getRepository('AppBundle:Invoice')->findOneBy(array('anfixCode' => $anfixInvoiceCode));
                        if ($searchedPreviouslyInvoice) {
                            // update invoice
                            $output->writeln('<comment>found</comment>');
                            $updated++;
                            $importedInvoice = $searchedPreviouslyInvoice;
                        } else {
                            // new invoice
                            $output->writeln('<info>not found</info>');
                            $created++;
                            $importedInvoice = new Invoice();
                        }
                        // build imported invoice
                        $invoiceDateValue = $ws->getCellByColumnAndRow(42, $row->getRowIndex())->getValue();
                        $importedInvoice
                            ->setAnfixCode($anfixInvoiceCode)
                            ->setNumber(intval($ws->getCellByColumnAndRow(5, $row->getRowIndex())->getValue()))
                            ->setYear(intval(substr($ws->getCellByColumnAndRow(24, $row->getRowIndex())->getValue(), 1)))
                            ->setDate(\DateTime::createFromFormat('Y-m-d H:i:s.u', $invoiceDateValue))
                            ->setCustomer($customer)
                            ->setTaxPercentage(Invoice::TAX_IVA)
                            ->setIrpfPercentage(Invoice::TAX_IRPF)
                            ->setBaseAmount(floatval($ws->getCellByColumnAndRow(80, $row->getRowIndex())->getValue()))
                            ->setTotalAmount(floatval($ws->getCellByColumnAndRow(61, $row->getRowIndex())->getValue()))
                            ->setPaymentMethod($customer->getPaymentMethod())
                            ->setIsSended(true)
                            ->setIsSepaXmlGenerated(true)
                            ->setIsPayed(true)
                            ->setEnabled(true)
                        ;
                        if ($input->getOption('force')) {
                            if (!$searchedPreviouslyInvoice) {
                                $this->em->persist($importedInvoice);
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

            // invoice lines
            /** @var Worksheet $ws */
            $ws = $spreadsheet->setActiveSheetIndexByName('Lineas_Facturas_Emitidas');
            $output->writeln($ws->getTitle());
            /** @var Row $row */
            foreach ($ws->getRowIterator() as $row) {
                // search customer
                $anfixInvoiceCode = $ws->getCellByColumnAndRow(9, $row->getRowIndex())->getValue();
                $output->write('seraching previous anfix invoice '.$anfixInvoiceCode.'... ');
                $searchedPreviouslyInvoice = $this->em->getRepository('AppBundle:Invoice')->findOneBy(array('anfixCode' => $anfixInvoiceCode));
                if ($searchedPreviouslyInvoice) {
                    // related invoice found
                    $output->writeln('<comment>found</comment>');
                    $anfixInvoiceLineCode = $ws->getCellByColumnAndRow(2, $row->getRowIndex())->getValue();
                    $output->write('seraching previous anfix invoice line '.$anfixInvoiceLineCode.'... ');
                    $searchedPreviouslyInvoiceLine = $this->em->getRepository('AppBundle:InvoiceLine')->findOneBy(array('anfixCode' => $anfixInvoiceLineCode));
                    if ($searchedPreviouslyInvoiceLine) {
                        // update invoice line
                        $output->writeln('<comment>found</comment>');
                        $linesUpdated++;
                        $importedInvoiceLine = $searchedPreviouslyInvoiceLine;
                    } else {
                        // new invoice line
                        $output->writeln('<info>not found</info>');
                        $linesCreated++;
                        $importedInvoiceLine = new InvoiceLine();
                    }
                    // build imported invoice line
                    $importedInvoiceLine
                        ->setInvoice($searchedPreviouslyInvoice)
                        ->setAnfixCode($anfixInvoiceLineCode)
                        ->setDescription($ws->getCellByColumnAndRow(16, $row->getRowIndex())->getValue())
                        ->setUnits(floatval($ws->getCellByColumnAndRow(7, $row->getRowIndex())->getValue()))
                        ->setPriceUnit(floatval($ws->getCellByColumnAndRow(12, $row->getRowIndex())->getValue()))
                        ->setDiscount(0)
                        ->setTotal(floatval($ws->getCellByColumnAndRow(22, $row->getRowIndex())->getValue()))
                        ->setEnabled(true)
                    ;
                    if ($input->getOption('force')) {
                        if (!$searchedPreviouslyInvoiceLine) {
                            $this->em->persist($importedInvoiceLine);
                        }
                        $this->em->flush();
                    }
                } else {
                    // no related invoice line found
                    $output->writeln('<info>not found</info>');
                }
            }

            // EOF
            $dtEnd = new \DateTime();
            $output->writeln('<info>---------------------------</info>');
            $output->writeln('<info>'.$totalItemsCounter.' items parsed</info>');
            $output->writeln('<info>'.$created.' new invoices added</info>');
            $output->writeln('<info>'.$updated.' invoices updated</info>');
            $output->writeln('<info>'.$linesCreated.' new invoice lines added</info>');
            $output->writeln('<info>'.$linesUpdated.' invoice lines updated</info>');
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
