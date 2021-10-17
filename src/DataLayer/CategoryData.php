<?php
namespace DataLayer;

class CategoryData
{
	private \PDO $db;

	public function __construct(\PDO $db)
	{
		$this->db = $db;
	}

	public function getCategoryID($name): int
	{
		$query = $this->db->prepare('SELECT category_id FROM category WHERE `name` = "' . $name . '"');
		$query->execute();
		$result = $query->fetchAll(\PDO::FETCH_COLUMN);

		return $result[0];
	}

}
