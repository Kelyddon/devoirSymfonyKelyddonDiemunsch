<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Service\BookCoverUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/administration')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return new Response("<h1>Dashboard</h1>");
    }

    #[Route('/article/rediger.html', name: 'admin_article_rediger', methods: ['GET', 'POST'])]
    public function redigerArticle(Request $request, EntityManagerInterface $em, BookCoverUploader $coverUploader): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('coverImage')->getData();
            if ($file) {
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $book->getTitle()));
                $filename = $coverUploader->upload($file, $slug);
                $book->setCoverImage($filename);
            }
            $book->setUser($this->getUser());
            $book->setCreatedAt(new \DateTimeImmutable());
            $book->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($book);
            $em->flush();
            $this->addFlash('success', 'Livre ajouté avec succès !');
            return $this->redirectToRoute('admin_article_rediger');
        }
        return $this->render('admin/rediger_article.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
