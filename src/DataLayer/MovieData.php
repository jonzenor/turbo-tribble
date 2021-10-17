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
		$stmt = $this->db->prepare('select * from film');
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
		$query = $this->db->prepare('select * from film WHERE title LIKE "%' .  $searchTerm . '%"');
		$query->execute();

		return $query->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function searchRating($rating): ?array
	{
		$query = $this->db->prepare('select * from film WHERE rating = "' . $rating . '"');
		$query->execute();

		return $query->fetchAll(\PDO::FETCH_ASSOC);
	}
}
