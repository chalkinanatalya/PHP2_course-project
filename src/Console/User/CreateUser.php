<?php
namespace Project\Console\User;

use Project\Blog\User\User;
use Project\Exceptions\UserNotFoundException;
use Symfony\Component\Console\Command\Command;
use Project\Repositories\User\UserRepositoryInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
class CreateUser extends Command
{
    public function __construct(private UserRepositoryInterface $usersRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->setName('user:create')
        ->setDescription('Creates new user')
        ->addArgument('firstName',InputArgument::REQUIRED,'First name')
        ->addArgument('lastName', InputArgument::REQUIRED, 'Last name')
        ->addArgument('email', InputArgument::REQUIRED, 'Email')
        ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Create user command started');
        $email = $input->getArgument('email');

        if ($this->userExists($email)) {
            $output->writeln("User already exists: $email");
            return Command::FAILURE;
        }

        $user = User::createFrom(
            $input->getArgument('firstName'),
            $input->getArgument('lastName'),
            $input->getArgument('email'),
            $input->getArgument('password'),
        );
        
        $this->usersRepository->save($user);
        $output->writeln('User created: ' . $user->getEmail());

        return Command::SUCCESS;
    }

    private function userExists(string $user): bool
    {
        try {
            $this->usersRepository->getByEmail($user);
        } catch (UserNotFoundException) {
            return false;
        }

        return true;
    }
}