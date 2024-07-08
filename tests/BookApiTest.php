<?php
declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Service\DatabasePurger;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\ErrorHandler\ErrorHandler;
use Symfony\Contracts\Service\Attribute\Required;


ErrorHandler::register(null, false);

#[CoversClass(\App\Entity\Book::class)]
#[CoversClass(\App\Entity\Author::class)]
#[CoversClass(\App\Entity\Category::class)]
class BookApiTest extends ApiTestCase
{

    public function testCreateBook(): void
    {
        $client = static::createClient();

        // Create an Author
        $response = $client->request('POST', '/api/authors', ['json' => [
            'name' => 'test Create Book',
            'birthDate' => '1980-05-20',
            'biography' => 'test Create Book is a fictional author used for testing purposes.'
        ]]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['name' => 'test Create Book']);
        $authorIri = $response->toArray()['@id'];

        // Create a Category
        $response = $client->request('POST', '/api/categories', ['json' => [
            'name' => 'Science Fiction'
        ]]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['name' => 'Science Fiction']);
        $categoryIri = $response->toArray()['@id'];

        // Create a Book
        $response = $client->request('POST', '/api/books', ['json' => [
            'title' => 'New Book Title',
            'description' => 'A description of the new book.',
            'publicationDate' => '2023-01-01',
            'author' => $authorIri,
            'categories' => [$categoryIri]
        ]]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['title' => 'New Book Title']);
    }

    public function testRetrieveBookById(): void
    {
        $client = static::createClient();

        // Create a Book
        $response = $client->request('POST', '/api/books', ['json' => [
            'title' => 'Book to Retrieve',
            'description' => 'Description for book to retrieve.',
            'publicationDate' => '2023-01-01',
            'author' => '/api/authors/1',
            'categories' => ['/api/categories/1']
        ]]);
        self::assertResponseIsSuccessful();
        $bookIri = $response->toArray()['@id'];

        // Retrieve the Book by ID
        $client->request('GET', $bookIri);
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'title' => 'Book to Retrieve',
        ]);
    }

    public function testUpdateBook(): void
    {
        $client = static::createClient();

        // Create a Book
        $response = $client->request('POST', '/api/books', ['json' => [
            'title' => 'Book to Update',
            'description' => 'Description for book to update.',
            'publicationDate' => '2023-01-01',
            'author' => '/api/authors/1',
            'categories' => ['/api/categories/1']
        ]]);
        self::assertResponseIsSuccessful();
        $bookIri = $response->toArray()['@id'];

        // Update the Book
        $response = $client->request('PUT', $bookIri, ['json' => [
            'title' => 'Updated Book Title',
            'description' => 'Updated description.',
            'publicationDate' => '2023-02-01',
            'author' => '/api/authors/1',
            'categories' => ['/api/categories/1']
        ]]);
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'title' => 'Updated Book Title',
            'description' => 'Updated description.',
        ]);
    }

    public function testDeleteBook(): void
    {
        $client = static::createClient();

        // Create a Book
        $response = $client->request('POST', '/api/books', ['json' => [
            'title' => 'Book to Delete',
            'description' => 'Description for book to delete.',
            'publicationDate' => '2023-01-01',
            'author' => '/api/authors/1',
            'categories' => ['/api/categories/1']
        ]]);
        self::assertResponseIsSuccessful();
        $bookIri = $response->toArray()['@id'];

        // Delete the Book
        $client->request('DELETE', $bookIri);
        self::assertResponseStatusCodeSame(204);

        // Verify the Book was Deleted
        $client->request('GET', $bookIri);
        self::assertResponseStatusCodeSame(404);
    }

    public function testFilterBooksByPublicationDate(): void
    {
        $client = static::createClient();

        // Create a Book
        $response = $client->request('POST', '/api/books', ['json' => [
            'title' => 'Book to Filter',
            'description' => 'Description for book to filter.',
            'publicationDate' => '2024-01-01',
            'author' => '/api/authors/1',
            'categories' => ['/api/categories/1']
        ]]);
        self::assertResponseIsSuccessful();

        // Filter Books by Publication Date
        $client->request('GET', '/api/books?publicationDate[after]=2024-01-01');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'hydra:member' => [
                ['title' => 'Book to Filter']
            ]
        ]);
    }
}

