<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Category;
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

        # Création des catégories
        $politique = new Category();
        $politique->setName('Politique');
        $politique->setSlug('politique');

        $economie = new Category();
        $economie->setName('Economie');
        $economie->setSlug('economie');

        $culture = new Category();
        $culture->setName('Culture');
        $culture->setSlug('culture');

        $loisirs = new Category();
        $loisirs->setName('Loisirs');
        $loisirs->setSlug('loisirs');

        $sport = new Category();
        $sport->setName('Sport');
        $sport->setSlug('sport');

        # Sauvegarde des éléments
        $manager->persist($politique);
        $manager->persist($economie);
        $manager->persist($culture);
        $manager->persist($loisirs);
        $manager->persist($sport);

        # Création d'un tableau de catégories
        $categories = [$politique, $economie, $culture, $loisirs, $sport];

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
