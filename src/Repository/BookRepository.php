<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Encuentra todos los libros con su promedio de rating calculado de forma eficiente.
     * 
     * Utiliza una sola consulta SQL con LEFT JOIN y AVG() para calcular el promedio
     * de ratings sin realizar múltiples consultas. Los libros sin reseñas tendrán
     * average_rating = null.
     * 
     * @return array Array de arrays asociativos con los campos:
     *               - title: string
     *               - author: string
     *               - published_year: int
     *               - average_rating: float|null
     */
    public function findAllWithAverageRating(): array
    {
        $qb = $this->createQueryBuilder('b')
            ->select(
                'b.title',
                'b.author',
                'b.publishedYear as published_year',
                'AVG(r.rating) as average_rating'
            )
            ->leftJoin('b.reviews', 'r')
            ->groupBy('b.id')
            ->orderBy('b.title', 'ASC');

        $result = $qb->getQuery()->getResult();

        // Convertir average_rating de string a float redondeado o null
        return array_map(function ($book) {
            $book['average_rating'] = $book['average_rating'] !== null 
                ? round((float) $book['average_rating'], 2)
                : null;
            return $book;
        }, $result);
    }
}
