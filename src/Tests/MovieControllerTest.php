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
		// Since we are expecting the page to fail we have to tell Guzzle to not fail on http_errors.
		$response = $this->client->get('/movie/10000', ['http_errors' => false]);
		
        $this->assertEquals(404, $response->getStatusCode());
	}

	/** @test */
	public function return_an_error_if_invalid_data_type_is_given(): void
	{
		// Since we are expecting the page to fail we have to tell Guzzle to not fail on http_errors.
		$response = $this->client->get('/movie/AllYourBase', ['http_errors' => false]);
		
		// If we were not typecasting the input to an integer, then we would test for status 400 instead
		$this->assertEquals(404, $response->getStatusCode());
	}

	// DONE: Search for movies by title
	/** @test */
	public function search_endpoint_loads()
	{
		$response = $this->client->get('/search/CROOKED%20FROGMEN');

        $this->assertEquals(200, $response->getStatusCode());
	}

	/** @test */
	public function search_endpoint_returns_some_movies()
	{
		$response = $this->client->get('/search/CROOKED%20FROGMEN');
		$data = json_decode($response->getBody(), true);
		$this->assertArrayHasKey('film_id', $data[0]);
	}

	/** @test */
	public function search_endpoint_returns_the_correct_movie()
	{
		$response = $this->client->get('/search/CROOKED%20FROGMEN');
		$data = json_decode($response->getBody(), true);
		$this->assertEquals('CROOKED FROGMEN', $data[0]['title']);
	}
	
	// DONE: Filter movies by rating
	/** @test */
	public function rating_endpoint_loads()
	{
		$response = $this->client->get('/rated/G');

        $this->assertEquals(200, $response->getStatusCode());
	}

	/** @test */
	public function rating_endpoint_returns_movies()
	{
		$response = $this->client->get('/rated/G');
		$data = json_decode($response->getBody(), true);
		$this->assertArrayHasKey('film_id', $data[0]);
	}

	/** 
	 * @test 
	 * @dataProvider validRatings
	 */
	public function rating_endpoint_returns_only_movies_of_the_requested_rating($searchRating, $dbRating)
	{
		$response = $this->client->get('/rated/' . $searchRating);
		$data = json_decode($response->getBody(), true);
		$this->assertEquals($dbRating, $data[0]['rating']);
	}

	/** @test */
	public function rating_endpoint_returns_error_for_invalid_rating_format()
	{
		// Since we are expecting the page to fail we have to tell Guzzle to not fail on http_errors.
		$response = $this->client->get('/rated/Emc2', ['http_errors' => false]);

		$this->assertEquals(400, $response->getStatusCode());
	}

	// DONE: Filter movies by category
	/** @test */
	public function category_endpoint_loads()
	{
		$response = $this->client->get('/category/Action');

		$this->assertEquals(200, $response->getStatusCode());
	}

	/** @test */
	public function category_endpoint_returns_movies()
	{
		$response = $this->client->get('/category/Action');

		$data = json_decode($response->getBody(), true);
		$this->assertArrayHasKey('film_id', $data[0]);
	}

	/** @test */
	public function category_endpoint_returns_movies_from_the_correct_category()
	{
		$response = $this->client->get('/category/Action');
		
		// If I were doing this type of test in a full application then I would be
		// making a mock database and filling it in with factory created test data
		// and testing against that. However, as this is a small test project I am
		// just going to test against the data and values that I know that are in
		// the provided database.
		$data = json_decode($response->getBody(), true);

		// The first result from search for action movies should be id
		$this->assertEquals(19, $data[0]['film_id']);
	}	

	// TODO: Validate that the requested category exists

	// TODO: Add a movie to the database


	/**
	 * @returns array<string, string>
	 * The first value is the expected search term
	 * The second value is the standardized way the ratins are stored in the database
	 */
	public function validRatings()
    {
		// This returns all of the valid Ratings that can be searched for
        return [
            ['G', 'G'],
            ['PG', 'PG'],
            ['PG13', 'PG-13'],
            ['R', 'R'],
            ['NC17', 'NC-17'],
        ];
    }
}
