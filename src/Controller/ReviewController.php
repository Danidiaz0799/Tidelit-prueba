<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReviewController extends AbstractController
{
    /**
     * POST /api/reviews
     * 
     * Registra una nueva reseña para un libro.
     * 
     * Request body (JSON):
     * {
     *   "book_id": 1,
     *   "rating": 5,
     *   "comment": "Excelente libro"
     * }
     * 
     * Validaciones:
     * - book_id debe existir (400 si no existe)
     * - rating debe ser entero entre 1 y 5 (400 si no cumple)
     * - comment no puede estar vacío y debe tener mínimo 5 caracteres (400 si no cumple)
     * 
     * Respuesta exitosa (201): 
     * {
     *   "id": 8,
     *   "book_id": 1,
     *   "rating": 5,
     *   "comment": "Excelente libro",
     *   "created_at": "2025-11-12T10:30:00+00:00"
     * }
     */
    #[Route('/api/reviews', name: 'api_reviews_create', methods: ['POST'])]
    public function create(
        Request $request,
        BookRepository $bookRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        // 1. Obtener y decodificar el JSON del request
        $data = json_decode($request->getContent(), true);

        // Validar que el JSON sea válido
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json([
                'error' => 'JSON inválido',
                'message' => json_last_error_msg()
            ], 400);
        }

        // 2. Validar que los campos requeridos estén presentes
        if (!isset($data['book_id'])) {
            return $this->json([
                'error' => 'Validación fallida',
                'message' => 'El campo book_id es requerido'
            ], 400);
        }

        if (!isset($data['rating'])) {
            return $this->json([
                'error' => 'Validación fallida',
                'message' => 'El campo rating es requerido'
            ], 400);
        }

        if (!isset($data['comment'])) {
            return $this->json([
                'error' => 'Validación fallida',
                'message' => 'El campo comment es requerido'
            ], 400);
        }

        // 3. Validar que el libro exista
        $book = $bookRepository->find($data['book_id']);
        if (!$book) {
            return $this->json([
                'error' => 'Libro no encontrado',
                'message' => sprintf('El libro con ID "%s" no fue encontrado.', $data['book_id'])
            ], 400);
        }

        // 4. Crear la entidad Review
        $review = new Review();
        $review->setBook($book);
        $review->setRating((int) $data['rating']);
        $review->setComment($data['comment']);

        // 5. Validar la entidad usando el Validator de Symfony
        $errors = $validator->validate($review);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorMessages[$propertyPath] = $error->getMessage();
            }

            return $this->json([
                'error' => 'Validación fallida',
                'errors' => $errorMessages
            ], 400);
        }

        // 6. Persistir la reseña
        $entityManager->persist($review);
        $entityManager->flush();

        // 7. Preparar la respuesta exitosa
        $response = $this->json([
            'id' => $review->getId(),
            'book_id' => $review->getBook()->getId(),
            'rating' => $review->getRating(),
            'comment' => $review->getComment(),
            'created_at' => $review->getCreatedAt()->format('c') // ISO 8601
        ], 201);

        // Configurar headers CORS
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        return $response;
    }
}
