<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un admin ROLE_AUTHOR
        $admin = new User();
        $admin->setEmail('admin@author.com')
            ->setFirstname('Admin')
            ->setLastname('Author')
            ->setRoles(['ROLE_AUTHOR'])
            ->setPassword($this->hasher->hashPassword($admin, 'adminpass'));
        $manager->persist($admin);

        // Création d'un user ROLE_USER
        $user = new User();
        $user->setEmail('user@user.com')
            ->setFirstname('User')
            ->setLastname('Normal')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->hasher->hashPassword($user, 'userpass'));
        $manager->persist($user);

        $manager->flush();
    }
}
