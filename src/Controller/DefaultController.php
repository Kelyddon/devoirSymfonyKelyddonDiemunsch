<?php

namespace App\Controller;


use App\Entity\Category;
use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'default_home', methods: ['GET'])]
    public function home(Request $request, BookRepository $bookRepository): Response
    {
        $search = $request->query->get('search');
        $genre = $request->query->get('genre');
        $qb = $bookRepository->createQueryBuilder('b');
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

    # -- Routes / Pages pour mes catégories

    #[Route('/{slug:category}', name: 'default_category', methods: ['GET'])]
    public function category(Category $category): Response
    {
        return $this->render('default/category.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/books/{id}', name: 'default_book', methods: ['GET'])]
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


}
