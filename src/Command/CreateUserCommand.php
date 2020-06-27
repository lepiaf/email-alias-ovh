<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-user';
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('displayName', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->userRepository->createUserEntity($input->getArgument('username'), $input->getArgument('displayName'), null);
        $this->userRepository->saveUserEntity($user);

        return Command::SUCCESS;
    }
}
