<?php
namespace Project\Console\User;
use Project\Blog\User\User;
use Project\Repositories\User\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUser extends Command
{
    public function __construct(
    private UserRepositoryInterface $userRepository,
    ) {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
        ->setName('user:update')
        ->setDescription('Updates a user')
        ->addArgument(
        'id',
        InputArgument::REQUIRED,
        'ID of a user to update'
    )
        ->addOption(
            'first-name',
            'f',
            InputOption::VALUE_OPTIONAL,
            'First name',
        )
        ->addOption(
            'last-name',
            'l',
            InputOption::VALUE_OPTIONAL,
            'Last name',
        );
    }
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {

        $firstName = $input->getOption('first-name');
        $lastName = $input->getOption('last-name');

        if (empty($firstName) && empty($lastName)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }

        $id = $input->getArgument('id');
        $user = $this->userRepository->get($id);
        
        $updatedUser = new User(
            firstName: empty($firstName) ? $user->getFirstName() : $firstName,
            lastName: empty($lastName) ? $user->getLastName() : $lastName,
            email: $user->getEmail(),
            hashedPassword: $user->getHashedPassword(),
        );

        $this->userRepository->save($updatedUser);
        $output->writeln("User updated: $id");
        return Command::SUCCESS;
    }
}