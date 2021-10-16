<?php
namespace Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \DataLayer\MovieData;

class MovieController
{
	private MovieData $movieData;

	public function __construct(ContainerInterface $container)
	{
		$this->movieData = $container->get('movieData');
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

		if (!$movie) {
			throw new \Slim\Exception\NotFoundException($request, $response);
		}

		return $response->withJson($movie);
	}
}
