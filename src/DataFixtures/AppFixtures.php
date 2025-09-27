<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        # Création d'un utilisateur
        $user = new User();
        $user->setEmail('hugo@actunews.com')
            ->setFirstName('Hugo')
            ->setLastName('Doe')
            ->setPassword('demo')
            ->setRoles(['ROLE_USER']);

        # Sauvegarde de l'utilisateur
        $manager->persist($user);

        # Création des livres
        $genres = ['Roman', 'Essai', 'Poésie', 'BD', 'Science', 'Autre'];
        for ($i = 0; $i < 50; $i++) {
            $book = new Book();
            $book->setTitle("Titre du livre n°$i")
                ->setAuthor("Auteur $i")
                ->setDescription('Description du livre exemple...')
                ->setGenre($genres[array_rand($genres)])
                ->setCoverImage('https://placehold.co/600x400')
                ->setUser($user)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($book);
        }

        # Déclenche l'enregistrement de toutes les données
        $manager->flush();
    }
}
