<?php

/**
 * Post Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Post
{

	use Model;

	protected $table = 'posts';
	protected $primaryKey = 'post_id';

	protected $allowedColumns = [
		'image',
		'title',
		'content',
		'author',
		'category',
		'date_posted',
		'updated_by',
		'date_updated'
	];


	public function validate(array $files_data, array $post_data, int|string|null $id = null): bool
	{
		$this->errors = [];

		// Allowed File types
		$allowed_types = [
			'image/jpeg',
			'image/jpg',
			'image/png',
			'image/webp'
		];

		// Image validation - Check inside the file 
		if (empty($files_data['image']['name'])) {
			$this->errors['image'] = 'An image is required!';
		} else 
        if (!isset($files_data['image']['type']) || !in_array($files_data['image']['type'], $allowed_types)) {
			$this->errors['image'] = 'Invalid Image File Type. Only types: ' . implode(', ', $allowed_types) . ' allowed!';
		} else 
		if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
			$this->errors['image'] = 'File upload error: ' . $_FILES['image']['error'];
		}

		// Other inputs validation
		if (empty($post_data['title'])) {
			$this->errors['title'] = "Give your post a title, it's required";
		}
		if (empty($post_data['content'])) {
			$this->errors['content'] = "Post content is required";
		}
		if (empty($post_data['category'])) {
			$this->errors['category'] = "Category is required";
		}


		if (empty($this->errors)) {
			return true;
		}

		return false;
	}

	public function postsWithcats(): array|false
	{
		$sql = "SELECT p.*, c.* 
				FROM posts AS p 
				LEFT JOIN categories AS c ON c.id = p.category
			";
		$results = $this->query($sql);

		return $results;
	}

	public function singlePost(int $id): object|false
	{
		$sql = "SELECT p.*, c.* 
				FROM posts p 
				LEFT JOIN categories c ON c.id = p.category 
				WHERE post_id = ? 
				";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$id]);

		$result = $stmt->fetch(PDO::FETCH_OBJ);

		if ($result) {
			return $result;
		}
		return false;

	}

	public function recentPosts(): array|false
	{
		$sql = "SELECT * FROM posts WHERE date_posted >= CURDATE() - INTERVAL 1 MONTH LIMIT 3";
		
		$result = $this->query($sql);
		return $result;
	}

	public function search(string $filtervalue): array|false
	{
		$sql = "SELECT p.*, c.* 
				FROM posts p 
				LEFT JOIN categories c ON c.id = p.category 
				WHERE CONCAT(p.post_id, p.title, p.content, p.author, c.id, c.cat_name, p.date_posted) LIKE '%$filtervalue%'
				";

		$result = $this->query($sql);

		if ($result) {
			return $result;
		}
		return false;

	}

	public function numPosts(): int
	{
		$sql = "SELECT COUNT(*) FROM posts";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchColumn();

		return $result;
	}
}
