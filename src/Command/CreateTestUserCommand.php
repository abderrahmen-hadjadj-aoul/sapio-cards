<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class CreateTestUserCommand extends Command
{
    protected static $defaultName = 'app:create-test-user';
    protected static $defaultDescription = 'Add a short description for your command';

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, UserRepository $users)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->passwordEncoder = $encoder;
        $this->users = $users;
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = "unit.test@unit.test";
        $apikey = "TEST-API-KEY";
        $plainPassword = "test";

        $user = new User();
        $user->setEmail($email);
        $user->setApikey($apikey);
        $user->setRoles(['ROLE_USER']);

        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success("User created");
        $io->success("email: " . $email);
        $io->success("password: " . $plainPassword);
        $io->success("apikey: " . $apikey);

        return Command::SUCCESS;
    }
}
