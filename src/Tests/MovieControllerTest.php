<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

final class MovieControllerTest extends TestCase
{
	protected $client;

    protected function setUp(): void
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8080'
        ]);
    }

	public function testThings(): void
	{
		$this->assertEquals(true, true);
	}

	// DONE: Verify that the movies page works
	/** @test */
    public function test_that_the_movies_api_loads(): void
    {
        $response = $this->client->get('/movies');
        $this->assertEquals(200, $response->getStatusCode());
    }

	/** @test */
	public function the_movies_api_loads_json_data(): void
	{
		$response = $this->client->get('/movies');
		$this->assertJson($response->getBody());

		$data = json_decode($response->getBody(), true);
		$this->assertArrayHasKey('film_id', $data[0]);
	}

	// DONE: Return a single movie
	/** @test */
	public function the_movie_api_loads(): void
	{
		$response = $this->client->get('/movie/1');

        $this->assertEquals(200, $response->getStatusCode());
	}

	/** @test */
	public function verify_the_movie_api_loads_expected_data(): void
	{
		$response = $this->client->get('/movie/1');

		$data = json_decode($response->getBody(), true);
		$this->assertArrayHasKey('film_id', $data);
		$this->assertEquals('ACADEMY DINOSAUR', $data['title']);
	}

	// DONE: Verify the movie exists and validate user input
	/** @test */
	public function return_404_error_if_movie_does_not_exist(): void
	{
		$response = $this->client->get('/movie/10000', ['http_errors' => false]);
		
        $this->assertEquals(404, $response->getStatusCode());
	}

	/** @test */
	public function return_an_error_if_invalid_data_type_is_given(): void
	{
		$response = $this->client->get('/movie/AllYourBase', ['http_errors' => false]);
		// If we were not typecasting the input to an integer, then we would test for status 400 instead
		$this->assertEquals(404, $response->getStatusCode());
	}

	// TODO: Search for movies by title

	// TODO: Validate user input for searches

	// TODO: Filter movies by rating

	// TODO: Validate rating input

	// TODO: Filter movies by category

	// TODO: Validate category input

	// TODO: Add a movie to the database
}
