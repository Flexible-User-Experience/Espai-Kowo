<?php

namespace AppBundle\Command;

use AppBundle\Entity\Provider;
use AppBundle\Enum\PaymentMethodEnum;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportProviderCommand.
 *
 * @category Command
 */
class ImportProviderCommand extends BaseCommand
{
    /**
     * Configure command.
     */
    protected function configure()
    {
        $this
            ->setName('app:import:provider')
            ->setDescription('Import a provider from XLS file')
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
                'If set, the task will persist providers into database'
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
            $output->writeln('<comment>--force option enabled (this option persists providers into database)</comment>');
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
            $created = 0;
            $updated = 0;

            // providers
            /** @var Worksheet $ws */
            $ws = $spreadsheet->setActiveSheetIndexByName($input->getArgument('worksheet'));
            $output->writeln($ws->getTitle());
            /** @var Row $row */
            foreach ($ws->getRowIterator() as $row) {
                $totalItemsCounter++;
                // search provider
                $providerTic = $ws->getCellByColumnAndRow(80, $row->getRowIndex())->getValue();
                $output->write('seraching provider '.$providerTic.'... ');
                if ($providerTic) {
                    $searchedProvider = $this->em->getRepository('AppBundle:Provider')->findOneBy(array('tic' => $providerTic));
                    if ($searchedProvider) {
                        // update provider
                        $provider = $searchedProvider;
                        $updated++;
                        $output->writeln('<info>found</info>');
                    } else {
                        // new provider
                        $provider = new Provider();
                        $created++;
                        $output->writeln('<comment>not found</comment>');
                    }
                    $searchedCity = $this->em->getRepository('AppBundle:City')->findOneBy(array('postalCode' => $ws->getCellByColumnAndRow(3, $row->getRowIndex())->getValue()));
                    $provider
                        ->setTic($providerTic)
                        ->setName($ws->getCellByColumnAndRow(79, $row->getRowIndex())->getValue())
                        ->setAddress($ws->getCellByColumnAndRow(57, $row->getRowIndex())->getValue())
                        ->setCity($searchedCity)
                        ->setPaymentMethod(PaymentMethodEnum::BANK_DRAFT)
                    ;
                    if ($input->getOption('force')) {
                        if (!$searchedProvider) {
                            $this->em->persist($provider);
                        }
                        $this->em->flush();
                    }
                }
            }

            // EOF
            $dtEnd = new \DateTime();
            $output->writeln('<info>---------------------------</info>');
            $output->writeln('<info>'.$totalItemsCounter.' items parsed</info>');
            $output->writeln('<info>'.$created.' new providers added</info>');
            $output->writeln('<info>'.$updated.' providers updated</info>');
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
