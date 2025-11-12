<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Crear los 3 libros requeridos
        $book1 = new Book();
        $book1->setTitle('El Arte de Programar');
        $book1->setAuthor('Donald Knuth');
        $book1->setPublishedYear(1968);
        $manager->persist($book1);

        $book2 = new Book();
        $book2->setTitle('Clean Code');
        $book2->setAuthor('Robert C. Martin');
        $book2->setPublishedYear(2008);
        $manager->persist($book2);

        $book3 = new Book();
        $book3->setTitle('Refactoring');
        $book3->setAuthor('Martin Fowler');
        $book3->setPublishedYear(1999);
        $manager->persist($book3);

        // Crear reseñas para el Libro 1 (El Arte de Programar) - 3 reseñas
        $review1 = new Review();
        $review1->setBook($book1);
        $review1->setRating(5);
        $review1->setComment('Un libro fundamental para cualquier programador. La explicación de algoritmos es magistral.');
        $manager->persist($review1);

        $review2 = new Review();
        $review2->setBook($book1);
        $review2->setRating(4);
        $review2->setComment('Excelente contenido, aunque puede resultar denso para principiantes.');
        $manager->persist($review2);

        $review3 = new Review();
        $review3->setBook($book1);
        $review3->setRating(5);
        $review3->setComment('Una obra maestra de la informática. Imprescindible en cualquier biblioteca técnica.');
        $manager->persist($review3);

        // Crear reseñas para el Libro 2 (Clean Code) - 2 reseñas
        $review4 = new Review();
        $review4->setBook($book2);
        $review4->setRating(5);
        $review4->setComment('Cambió completamente mi forma de escribir código. Altamente recomendado.');
        $manager->persist($review4);

        $review5 = new Review();
        $review5->setBook($book2);
        $review5->setRating(3);
        $review5->setComment('Buenos principios, aunque algunos ejemplos se sienten repetitivos.');
        $manager->persist($review5);

        // Crear reseñas para el Libro 3 (Refactoring) - 2 reseñas
        $review6 = new Review();
        $review6->setBook($book3);
        $review6->setRating(4);
        $review6->setComment('Técnicas muy útiles para mejorar código legacy. Ejemplos claros y prácticos.');
        $manager->persist($review6);

        $review7 = new Review();
        $review7->setBook($book3);
        $review7->setRating(5);
        $review7->setComment('Esencial para entender cómo mantener código limpio y mantenible a largo plazo.');
        $manager->persist($review7);

        // Guardar todos los cambios en la base de datos
        $manager->flush();
    }
}
