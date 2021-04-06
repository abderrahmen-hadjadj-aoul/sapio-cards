<?php

namespace App\DataFixtures;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\UserRepository;
use App\Entity\User;

class AppFixtures extends Fixture
{
    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, UserRepository $users)
    {
        $this->kernel = $kernel;
        $this->entityManager = $em;
        $this->passwordEncoder = $encoder;
        $this->users = $users;
    }
    
    public function load(ObjectManager $manager)
    {
        $email = "unit.test@unit.test";
        $apikey = "TEST-API-KEY";
        $plainPassword = "test";

        $user = new User();
        $user->setEmail($email);
        $user->setApikey($apikey);
        $user->setIsVerified(true);
        $user->setRoles(['ROLE_USER']);

        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        $email = "not.verified@not.verified";
        $apikey = "TEST-API-KEY-NOT-VERIFIED";
        $plainPassword = "test";

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $user = new User();
        $user->setEmail($email);
        $user->setApikey($apikey);
        $user->setIsVerified(false);
        $user->setRoles(['ROLE_USER']);

        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
