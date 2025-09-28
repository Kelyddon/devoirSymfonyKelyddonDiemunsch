<?php

namespace App\Controller;


use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\BookType;
use App\Service\BookCoverUploader;
use Doctrine\ORM\EntityManagerInterface;

class DefaultController extends AbstractController
{
    private BookCoverUploader $coverUploader;

    public function __construct(BookCoverUploader $coverUploader)
    {
        $this->coverUploader = $coverUploader;
    }

    #[Route('/', name: 'default_home', methods: ['GET'])]
    public function home(Request $request, BookRepository $bookRepository): Response
    {
        $search = $request->query->get('search');
        $genre = $request->query->get('genre');
        $qb = $bookRepository->createQueryBuilder('b');
        $user = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN')) {
            // Admin : accès à tous les livres
            if ($search) {
                $qb->where('b.title LIKE :search')
                    ->setParameter('search', '%' . $search . '%');
                if ($genre) {
                    $qb->andWhere('b.genre = :genre')
                        ->setParameter('genre', $genre);
                }
            } elseif ($genre) {
                $qb->where('b.genre = :genre')
                    ->setParameter('genre', $genre);
            }
        } else {
            // Utilisateur : accès uniquement à ses livres
            $qb->where('b.user = :user')
                ->setParameter('user', $user);
            if ($search) {
                $qb->andWhere('b.title LIKE :search')
                    ->setParameter('search', '%' . $search . '%');
            }
            if ($genre) {
                $qb->andWhere('b.genre = :genre')
                    ->setParameter('genre', $genre);
            }
        }
        $qb->orderBy('b.createdAt', 'DESC');
        $books = $qb->getQuery()->getResult();
        $genres = $bookRepository->findDistinctGenres();
        return $this->render('default/home.html.twig', [
            'books' => $books,
            'genres' => $genres,
            'selected_genre' => $genre,
            'search' => $search,
        ]);
    }

    #[Route('/books/add', name: 'book_add', methods: ['GET', 'POST'])]
    public function addBook(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('coverImage')->getData();
            if ($file) {
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $book->getTitle()));
                $filename = $this->coverUploader->upload($file, $slug);
                $book->setCoverImage($filename);
            }
            $book->setUser($this->getUser());
            $book->setCreatedAt(new \DateTimeImmutable());
            $book->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($book);
            $em->flush();
            $this->addFlash('success', 'Livre ajouté avec succès !');
            return $this->redirectToRoute('default_home');
        }
        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/books/{id}', name: 'book', methods: ['GET'])]
    public function book(BookRepository $bookRepository, string $id): Response
    {
        $book = $bookRepository->findOneBy(['id' => $id]);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }
        return $this->render('default/book.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/books/{id}/edit', name: 'book_edit', methods: ['GET', 'POST'])]
    public function editBook(Request $request, BookRepository $bookRepository, EntityManagerInterface $em, string $id): Response
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }
        $user = $this->getUser();
        if ($book->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce livre.');
        }
        $oldImage = $book->getCoverImage();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('coverImage')->getData();
            if ($file) {
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $book->getTitle()));
                $filename = $this->coverUploader->upload($file, $slug);
                $book->setCoverImage($filename);
                if ($oldImage) {
                    $this->coverUploader->remove($oldImage);
                }
            }
            $book->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'Livre modifié avec succès !');
            return $this->redirectToRoute('book', ['id' => $book->getId()]);
        }
        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    #[Route('/books/{id}/delete', name: 'book_delete', methods: ['POST'])]
    public function deleteBook(Request $request, BookRepository $bookRepository, EntityManagerInterface $em, string $id): Response
    {
        $book = $bookRepository->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }
        $user = $this->getUser();
        if ($book->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce livre.');
        }
        $em->remove($book);
        $em->flush();
        $this->addFlash('success', 'Livre supprimé avec succès !');
        return $this->redirectToRoute('default_home');
    }


}
