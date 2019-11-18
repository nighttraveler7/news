<?php
namespace nighttraveler7\News;

class News {
	public $dsn;
	public $user;
	public $password;
	public $table_name;

	public static function quoteIdent($field) {
		return "`".str_replace("`","``",$field)."`";
	}

	public function __construct($dsn, $user, $password, $table_name = 'news') {
		$this->dsn = $dsn;
		$this->user = $user;
		$this->password = $password;
		$this->table_name = $table_name;
	}

	public function get_entry($id) {
		$dsn = $this->dsn;
		$user = $this->user;
		$password = $this->password;
		$table_name = $this->table_name;

		try {
			$pdo = new \PDO($dsn, $user, $password);

			$stmt = $pdo->prepare(sprintf('SELECT * FROM %s WHERE id = :id', self::quoteIdent($table_name)));
			$stmt->bindValue(':id', $id, \PDO::PARAM_INT);

			$stmt->execute();

			return $stmt->fetch(\PDO::FETCH_ASSOC);
		}
		catch (\PDOException $e) {
			echo 'Database Routine Error: ' . $e->getMessage() . "\n";

			return false;
		}
	}

	public function delete($id) {
		$dsn = $this->dsn;
		$user = $this->user;
		$password = $this->password;
		$table_name = $this->table_name;

		try {
			$pdo = new \PDO($dsn, $user, $password);

			$stmt = $pdo->prepare(sprintf('DELETE FROM %s WHERE id = :id', self::quoteIdent($table_name)));
			$stmt->bindValue(':id', $id, \PDO::PARAM_INT);

			$stmt->execute();

			return true;
		}
		catch (\PDOException $e) {
			echo 'Database Routine Error: ' . $e->getMessage() . "\n";

			return false;
		}
	}

	public function list() {
		$dsn = $this->dsn;
		$user = $this->user;
		$password = $this->password;
		$table_name = $this->table_name;

		try {
			$pdo = new \PDO($dsn, $user, $password);

			$stmt = $pdo->prepare(sprintf('SELECT * FROM %s', self::quoteIdent($table_name)));

			$stmt->execute();

			return $stmt->fetchAll();
		}
		catch (\PDOException $e) {
			echo 'Database Routine Error: ' . $e->getMessage() . "\n";

			return false;
		}
	}

	public function post($title, $content, $posted_at = NULL, $id = NULL) {
		$dsn = $this->dsn;
		$user = $this->user;
		$password = $this->password;
		if (is_null($posted_at)) {
			$posted_at = new \Date();
		}
		$table_name = $this->table_name;

		if (is_null($id)) {
			$sql = 'INSERT INTO %s (posted_at, title, content) VALUES (:posted_at, :title, :content)';
		}
		else {
			$sql = 'UPDATE %s SET posted_at = :posted_at, title = :title, content = :content WHERE id = :id';
		}

		try {
			$pdo = new \PDO($dsn, $user, $password);

			$stmt = $pdo->prepare(sprintf($sql, self::quoteIdent($table_name)));
			$stmt->bindValue(':posted_at', $posted_at->format('Y-m-d H:i:s'), \PDO::PARAM_STR);
			$stmt->bindValue(':title', $title, \PDO::PARAM_STR);
			$stmt->bindValue(':content', $content, \PDO::PARAM_STR);
			if (!is_null($id)) {
				$stmt->bindValue(':id', $id, \PDO::PARAM_INT);
			}

			$stmt->execute();

			return true;
		}
		catch (\PDOException $e) {
			echo 'Database Routine Error: ' . $e->getMessage() . "\n";

			return false;
		}
	}
}
?>
