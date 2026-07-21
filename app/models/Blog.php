<?php 
/**
 * Blog class
 */

class Blog
{
	
	use Model;

	protected $table = 'posts';
	

	protected $allowedColumns = [
        'image',
		'title',
		'content',
		'author',
		'category'
	];

	public function validate(array $files_data, array $post_data, int|string|null $id = null): bool
	{
		$this->errors = [];

        // Allowed File types
        $allowed_types = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp' 
        ];

        // Image validation - Check inside the file 
        if(empty($files_data['image']['name']))
        {
            $this->errors['image'] = 'An image is required!';
        } else 
        if(!isset($files_data['image']['type']) || !in_array($files_data['image']['type'], $allowed_types))
        {
            $this->errors['image'] = 'Invalid Image File Type. Only types: ' . implode(', ', $allowed_types) . ' allowed!';
        } else 
		if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
			$this->errors['image'] = 'File upload error: ' . $_FILES['image']['error'];
		}

		// Other inputs validation
		if(empty($post_data['title']))
        {
            $this->errors['title'] = 'A title is required!';
        }
		if(empty($post_data['content']))
        {
            $this->errors['content'] = 'Post Content is required!';
        }
		if(empty($post_data['category']))
        {
            $this->errors['category'] = 'Post Category is required!';
        }
        
		if(empty($this->errors))
		{
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

	public function singlePost(int $id): object|array
	{
		$sql = "SELECT p.*, c.* 
				FROM posts AS p 
				LEFT JOIN categories AS c ON c.id = p.category 
				WHERE p.post_id = ? 
				";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$id]);

		$result = $stmt->fetch(PDO::FETCH_OBJ);

		if ($result) {
			return $result;
		}

		return []; // Return an empty array/object if the query fails
	}

	public function recentPosts(): array|false
	{
		$sql = "SELECT * FROM posts WHERE date_posted >= CURDATE() - INTERVAL 3 MONTH";
		
		$result = $this->query($sql);
		return $result;
	}

	public function postsPerCat(int $cat_id): array
	{
		$sql = "SELECT p.*, c.* 
				FROM posts AS p 
				LEFT JOIN categories AS c ON c.id = p.category 
				WHERE p.category = ? 
				";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$cat_id]);

		$result = $stmt->fetchAll(PDO::FETCH_OBJ);

		if ($result) {
			return $result;
		}

		return []; 
	}
}


