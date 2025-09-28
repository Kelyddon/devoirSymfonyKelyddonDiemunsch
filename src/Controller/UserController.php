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

        $user = new User();


        $form = $this->createForm(UserRegisterType::class, $user)
            ->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );


            $entityManager->persist($user);
            $entityManager->flush();


            $this->addFlash('success', "Félicitation vous pouvez vous connecter.");


            return $this->redirectToRoute('app_login');

        }


        return $this->render('user/register.html.twig', [
            'form' => $form
        ]);
    }
}
