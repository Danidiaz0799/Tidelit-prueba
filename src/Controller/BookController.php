<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * GET /api/books
     * 
     * Devuelve la lista de todos los libros con su promedio de rating.
     * Utiliza una consulta eficiente con JOIN y AVG() para calcular
     * el promedio en la base de datos.
     * 
     * Respuesta: Array de objetos con:
     * - title: string
     * - author: string
     * - published_year: int
     * - average_rating: float|null (null si no hay reseñas)
     */
    #[Route('/api/books', name: 'api_books_list', methods: ['GET'])]
    public function list(BookRepository $bookRepository): JsonResponse
    {
        $books = $bookRepository->findAllWithAverageRating();

        $response = $this->json($books, 200);
        
        // Configurar headers CORS manualmente para desarrollo
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        
        return $response;
    }
}
