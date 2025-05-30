<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée un utilisateur administrateur',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setPrenom('Admin');
        $user->setNom('Jose');
        $user->setEmail('admin.ecoride@gmail.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setCredits(1000);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'Ecoride25');
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln("✅ Utilisateur admin créé avec succès !");
        return Command::SUCCESS;
    }
}
