<?php

namespace App\Command;

use App\Service\ProviderService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchTodoDataCommand extends Command
{
    protected static $defaultName = 'app:fetch-todo-data';

    private $providerService;

    public function __construct(ProviderService $providerService, $name = null)
    {
        parent::__construct($name);
        $this->providerService = $providerService;
    }

    protected function configure()
    {
        $this
            ->setDescription('This command fetches data from specified todo provider.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->text("Started to fetch data.");
        
        try {
            $this->providerService->fetch_data();
            $io->success('Success! Your todo list is ready!');
        } catch (\Exception $ex) {
            $io->error($ex->getMessage());
            return 1;
        }

        return 0;
    }
}
