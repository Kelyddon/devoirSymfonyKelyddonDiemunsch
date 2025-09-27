<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{

    #[Route('/page/inscription', name: 'user_register', methods: ['GET','POST'])]
    public function register(Request $request,
                             EntityManagerInterface $entityManager,
                             UserPasswordHasherInterface $passwordHasher): Response
    {
        # Création d'un nouvel utilisateur
        $user = new User();

        # Création du formulaire
        $form = $this->createForm(UserRegisterType::class, $user)
            ->handleRequest($request);

        # Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            # Hachage du mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );

            # Sauvegarde dans la BDD
            $entityManager->persist($user);
            $entityManager->flush();

            # Notification Flash
            $this->addFlash('success', "Félicitation vous pouvez vous connecter.");

            # Redirection page connexion
            return $this->redirectToRoute('app_login');

        }

        # Création de la vue et passage du formulaire
        return $this->render('user/register.html.twig', [
            'form' => $form
        ]);
    }
}
