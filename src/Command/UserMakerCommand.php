<?php

namespace App\Command;

use App\Service\UserManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserMakerCommand extends Command
{
    protected static $defaultName = 'app:user-maker';
    /**
     * @var UserManagerService
     */
    private UserManagerService $userManagerService;

    public function __construct(string $name = null, UserManagerService $userManagerService)
    {
        parent::__construct($name);
        $this->userManagerService = $userManagerService;
    }

    protected function configure()
    {
        $this
            ->addArgument('username')
            ->addArgument('password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        if (!$username || !$password) {
            $io->note('Please, pass username and password');
        }

        $user = $this->userManagerService->createUser($username, $password);

        $io->success('You have a new user! ' . $user->getUsername());

        return Command::SUCCESS;
    }
}
