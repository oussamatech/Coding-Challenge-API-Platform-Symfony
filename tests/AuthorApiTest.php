<?php
declare(strict_types=1);


use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Service\DatabasePurger;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\ErrorHandler\ErrorHandler;


ErrorHandler::register(null, false);

#[CoversClass(\App\Entity\Book::class)]
#[CoversClass(\App\Entity\Author::class)]
#[CoversClass(\App\Entity\Category::class)]
class AuthorApiTest extends ApiTestCase
{

    public function testCreateAuthor(): void
    {
        $client = static::createClient();

        // Create an Author
        $response = $client->request('POST', '/api/authors', ['json' => [
            'name' => 'test Create Author',
            'birthDate' => '1980-05-20',
            'biography' => 'test Create Author is a fictional author used for testing purposes.'
        ]]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['name' => 'test Create Author']);
    }

    public function testRetrieveAuthorById(): void
    {
        $client = static::createClient();

        // Create an Author
        $response = $client->request('POST', '/api/authors', ['json' => [
            'name' => 'test Retrieve Author By Id',
            'birthDate' => '1980-05-20',
            'biography' => 'test Retrieve Author By Id is a fictional author used for testing purposes.'
        ]]);
        self::assertResponseIsSuccessful();
        $authorIri = $response->toArray()['@id'];

        // Retrieve the Book by ID
        $client->request('GET', $authorIri);
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'name' => 'test Retrieve Author By Id',
        ]);
    }

    public function testUpdateAuthor(): void
    {
        $client = static::createClient();

        // Create an Author
        $response = $client->request('POST', '/api/authors', ['json' => [
            'name' => 'test Update Author',
            'birthDate' => '1980-05-20',
            'biography' => 'test Update Author is a fictional author used for testing purposes.'
        ]]);
        self::assertResponseIsSuccessful();
        $authorIri = $response->toArray()['@id'];

        // Update the Author
        $response = $client->request('PUT', $authorIri, ['json' => [
            'name' => 'test Update Author Updated',
            'birthDate' => '2001-05-20',
            'biography' => 'John Doe is a fictional author used for testing purposes.'
        ]]);
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'name' => 'test Update Author Updated',
        ]);
    }

    public function testDeleteAuthor(): void
    {
        $client = static::createClient();

        // Create an Author
        $response = $client->request('POST', '/api/authors', ['json' => [
            'name' => 'test Delete Author',
            'birthDate' => '1980-05-20',
            'biography' => 'test Delete Author is a fictional author used for testing purposes.'
        ]]);
        self::assertResponseIsSuccessful();
        $authorIri = $response->toArray()['@id'];

        // Delete the Book
        $client->request('DELETE', $authorIri);
        self::assertResponseStatusCodeSame(204);

        // Verify the Book was Deleted
        $client->request('GET', $authorIri);
        self::assertResponseStatusCodeSame(404);
    }

    public function testFilterAuthorsByName(): void
    {
        $client = static::createClient();

        // Create an author
        $response = $client->request('POST', '/api/authors', ['json' => [
            'name' => 'OUSSAMA BEN AKKA',
            'birthDate' => '1985-02-24',
            'biography' => 'John Doe is a fictional author used for testing purposes.'
        ]]);
        self::assertResponseIsSuccessful();

        // Filter Books by Publication Date
        $client->request('GET', '/api/authors?name=OUSSAMA BEN AKKA');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'hydra:member' => [
                ['name' => 'OUSSAMA BEN AKKA']
            ]
        ]);
    }
}

