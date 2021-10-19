<?php
namespace Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \DataLayer\{
	MovieData,
	CategoryData,
};

class MovieController
{
	private MovieData $movieData;
	private CategoryData $categoryData;

	public function __construct(ContainerInterface $container)
	{
		$this->movieData = $container->get('movieData');
		$this->categoryData = $container->get('categoryData');
	}

	public function listAll(Request $request, Response $response, array $args)
	{
		$movies = $this->movieData->getAllMovies();
		return $response->withJson($movies);
	}

	public function listMovie(Request $request, Response $response, array $args)
	{
		// The id should always be an integer, so let's force it
		// This also prevents bad actors from injecting invalid data
		// into our database search
		$movie_id = (int)$args['id'];

		// We have already typecast the id into an integer so this is not needed
		// However, this was my first attempt at input validation so I left it.
		// Ultimately I decided on typecasting because is_numeric would still
		// allow decimals, which would be an invalid id. That is why I added the
		// above solution.
		if (!is_numeric($movie_id)) {
			return $response->withStatus(400);
		}

		$movie = $this->movieData->getMovie($movie_id);

		// Throw a response code if the movie was not found
		if (!$movie) {
			throw new \Slim\Exception\NotFoundException($request, $response);
		}

		return $response->withJson($movie);
	}

	public function searchTitle(Request $request, Response $response, array $args)
	{
		// Sanitize user input
		$searchTerm = htmlentities($args['term'], ENT_QUOTES, 'UTF-8');
		
		$movies = $this->movieData->searchTitle($searchTerm);

		return $response->withJson($movies);
	}

	public function searchRating(Request $request, Response $response, array $args)
	{
		$rating = $args['rating'];

		if (!$this->validateRating($rating)) {
			return $response->withStatus(400);
		}

		$rating = $this->standardizeRatings($rating);

		$movies = $this->movieData->searchRating($rating);

		return $response->withJson($movies);
	}

	public function searchCategory(Request $request, Response $response, array $args)
	{
		$category_name = $args['name'];

		$category_id = $this->categoryData->getCategoryID($category_name);

		if (!$category_id) {
			return $response->withStatus(400);
		}

		$movies = $this->movieData->searchCategory($category_id);

		return $response->withJson($movies);
	}

	public function createMovie(Request $request, Response $response, array $args)
	{
		$data = json_decode($request->getBody(), true);

		if (!is_array($data)) {
			return $response->withStatus(400);
		}

		if (!$this->validateFormData($data)) {
			return $response->withStatus(422);
		}

		$movie = $this->movieData->addMovie($data);
	}



	/**
	 * @param string $rating
	 * @return string $standardizedRating
	 */
	private function standardizeRatings($rating)
	{
		switch ($rating) {
			case "PG13":
				return "PG-13";
				break;
			case "NC17":
				return "NC-17";
				break;
			default:
				return $rating;
		}
	}

	/**
	 * @param string $rating
	 * @return bool
	 */
	private function validateRating($rating)
	{
		$validRatings = ['G', 'PG', 'PG13', 'PG-13', 'R', 'NC17', 'NC-17'];

		return in_array($rating, $validRatings);
	}

	/**
	 * @return array
	 */
	private function requiredFormFields()
	{
		$requiredFields = ['title', 'description', 'year', 'language_id',
			'rental_duration', 'rate', 'length', 'cost', 'rating',
			'special_features', 'category'];

		// Realistically we should be doing way more detailed form validation.
		// Validating that certain fields are integers or decimal, for instance.
		// If I were okay with this taking another day to complete then I would add that,
		// however my current job requires me so I will just end it with this note.
		return $requiredFields;
	}

	private function validateFormData($formData)
	{
		foreach ($this->requiredFormFields() as $requiredField) {
			if (!isset($formData[$requiredField])) {
				return false;
			}

			if ($formData[$requiredField] == '') {
				return false;
			}
		}

		return true;
	}
}
