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
	 * @dataProvider validRatingsProvider
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

	// DONE: Validate that the requested category exists
	/** @test */
	public function category_endpoint_returns_error_code_if_invalid_category_given()
	{
		$response = $this->client->get('/category/EpicSciFiFantasy', ['http_errors' => false]);
        $this->assertEquals(400, $response->getStatusCode());
	}

	// DONE: Add a movie to the database
	/** @test */
	public function create_movie_endpoint_loads()
	{
		$response = $this->client->request('POST', '/create', ['http_errors' => false]);

        $this->assertEquals(400, $response->getStatusCode());
	}

	/** @test */
	public function create_movie_endpoint_adds_data_to_the_application()
	{
		$data = $this->getFormData();

		// We obviously had some fun times trying to get this test to work...
		try {
			$response = $this->client->post('/create', [
				'body' => json_encode($data),
			]);
		} catch (\GuzzleHttp\Exception\ClientErrorResponseException  $e) {
			var_dump($e->getResponse()->getBody()->getContents());
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			var_dump($e->getResponse()->getBody()->getContents());
		} catch (\GuzzleHttp\Exception\ClientException  $e) {
			var_dump($e->getResponse()->getBody()->getContents());
		}

		$this->assertEquals(200, $response->getStatusCode());
		$url = '/search/' . str_replace(' ', '%20', $data['title']);

		// Make sure the movie is found
		$response = $this->client->get($url);
        $this->assertEquals(200, $response->getStatusCode());

		$receivedData = json_decode($response->getBody(), true);
		$this->assertArrayHasKey('film_id', $receivedData[0]);
		$this->assertEquals($data['title'], $receivedData[0]['title']);
	}

	/** @test */
	public function create_movie_endpoint_returns_error_if_data_malformed()
	{
		$data = $this->getFormData();

		$response = $this->client->request('POST', '/create', [
			'body' => '{"title","oops"}',
			'http_errors' => false,
		]);
		$this->assertEquals(400, $response->getStatusCode());
	}

	/**
	 * @test 
	 * @dataProvider requiredFormValidationProvider
	 **/
	public function validate_that_all_fields_are_required($field, $badValue)
	{
		$data = $this->getFormData();
		$data[$field] = $badValue;

		$response = $this->client->request('POST', '/create', [
			'body' => json_encode($data),
			'http_errors' => false,
		]);
		$this->assertEquals(422, $response->getStatusCode());
	}

	/** @test */
	public function creating_a_movie_adds_movie_to_the_correct_category()
	{
		$data = $this->getFormData();
		try {
			$response = $this->client->post('/create', [
				'body' => json_encode($data),
			]);
		} catch (\GuzzleHttp\Exception\ClientErrorResponseException  $e) {
			var_dump($e->getResponse()->getBody()->getContents());
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			var_dump($e->getResponse()->getBody()->getContents());
		} catch (\GuzzleHttp\Exception\ClientException  $e) {
			var_dump($e->getResponse()->getBody()->getContents());
		}

		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->client->get('/category/Action');

		$this->assertEquals(200, $response->getStatusCode());
		$receivedData = json_decode($response->getBody(), true);

		$lastMovie = end($receivedData);
		$this->assertArrayHasKey('film_id', $lastMovie);
		$this->assertEquals($data['title'], $lastMovie['title']);
	}



	/**
	 * @return array<string,string>
	 * The first value is the expected search term
	 * The second value is the standardized way the ratins are stored in the database
	 */
	public function validRatingsProvider()
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

	/**
	 * @return array<string,string>
	 * The first value is the form field
	 * The second value is the bad data that should fail
	 */
    public function requiredFormValidationProvider()
    {
        return [
            ['title', ''],
            ['description', ''],
            ['year', ''],
            ['language_id', ''],
            ['rental_duration', ''],
            ['rate', ''],
            ['length', ''],
            ['cost', ''],
            ['rating', ''],
            ['special_features', ''],
            ['category', ''],
        ];
    }

	/**
	 * @return array
	 * Returns an array of form data in the format of 'field' => 'value'
	 */
	private function getFormData()
	{
		$data = [
			'title' => "Adventures of a Software Engineer" . rand(1, 100000),
			'description' => "An epic story of one software engineer set free from captivity when he is hired by CCB and goes on to do great things for the company, and the world.",
			'year' => '2021',
			'language_id' => '1',
			'rental_duration' => '5',
			'rate' => '4.99',
			'length' => '90',
			'cost' => '20.99',
			'rating' => 'PG',
			'special_features' => 'Trailers,Deleted Scenes',
			'category' => '1', // This is an Adventure movie for sure. It had better not be a horror story...
		];

		return $data;
	}
}
