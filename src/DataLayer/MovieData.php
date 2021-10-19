<?php
namespace DataLayer;

class MovieData
{
	private \PDO $db;

	public function __construct(\PDO $db)
	{
		$this->db = $db;
	}

	public function getAllMovies(): array
	{
		$stmt = $this->db->prepare('SELECT * FROM film');
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getMovie($id): ?array
	{
		$query = $this->db->prepare('SELECT * FROM film WHERE film_id = ' . $id);
		$query->execute();

		$result = $query->fetch(\PDO::FETCH_ASSOC);

		if ($result) {
			return $result;
		}

		// If a movie was not found, then just return null
		return null;
	}

	public function searchTitle($searchTerm): ?array
	{
		$query = $this->db->prepare('SELECT * FROM film WHERE title LIKE "%' .  $searchTerm . '%"');
		$query->execute();

		return $query->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function searchRating($rating): ?array
	{
		$query = $this->db->prepare('SELECT * FROM film WHERE rating = "' . $rating . '"');
		$query->execute();

		return $query->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function searchCategory($category): ?array
	{
		$query = $this->db->prepare('SELECT * FROM film LEFT JOIN `film_category` ON film.film_id = film_category.film_id WHERE `category_id` = ' . $category);
		$query->execute();

		return $query->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function addMovie(array $data): ?int
	{
		$query = $this->db->prepare('INSERT INTO film 
			(title, description, release_year, language_id, original_language_id, rental_duration, rental_rate, length, replacement_cost, rating, special_features, last_update)
			VALUES (
			"' . $data['title'] . '",
			"' . $data['description'] . '",
			"' . $data['year'] . '",
			"' . $data['language_id'] . '",
			"1",
			"' . $data['rental_duration'] . '",
			"' . $data['rate'] . '",
			"' . $data['length'] . '",
			"' . $data['cost'] . '",
			"' . $data['rating'] . '",
			"' . $data['special_features'] . '",
			"' . date('Y-m-d H:i:s') . '"
		)');

		$query->execute();
		$movieId = $this->db->lastInsertId();

		$query2 = $this->db->prepare('INSERT INTO film_category (film_id, category_id, last_update) 
			VALUES ("' .  $movieId . '", "' . $data['category'] . '", "' . date('Y-m-d H:i:s') . '")'
		);
		$query2->execute();

		return $movieId;
	}
}
