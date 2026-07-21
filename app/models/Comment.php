<?php

/**
 * Comment Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class Comment
{
	use Model;

	protected $table = 'comments';
	protected $primaryKey = 'id'; 

	/* In the case of Users table */
	protected $loginUniqueColumn = 'email';


	protected $allowedColumns = [
		'post',
		'fullname',
		'email',
		'comment',
	];

	public function validate(array $data, int|string|null $id = null): bool
	{
		$this->errors = [];

		if (empty($data['fullname'])) {
			$this->errors['fullname'] = "Fullname is required";
		}

		if (empty($data['email'])) {
			$this->errors['email'] = "Email is required";
		} else
		if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			$this->errors['email'] = "Email is not valid";
		} else
		if ($this->first(['email' => $data['email']], ['id' => $id])) {
			$this->errors['email'] = "Email is already in use!";
		}

		if (empty($data['comment'])) {
			$this->errors['comment'] = "Comment is required";
		}


		if (empty($this->errors)) {
			return true;
		}

		return false;
	}

	public function commentCount(int $post): int
	{
		$sql = "SELECT COUNT(*) FROM comments WHERE post = ?";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$post]);

		$result = $stmt->fetchColumn();
		return $result;
	}

	public function commentsWithPosts(): array|false
	{
		$sql = "SELECT c.*, p.title  
				FROM comments c
				JOIN posts p ON p.post_id = c.post
		";
		$result = $this->query($sql);

		if ($result) {
			return $result;
		}
		return false;

	}

	public function specificPostComments(int $id): array
	{
		$sql = "SELECT c.*, post_id, p.title  
				FROM comments c
				JOIN posts p ON p.post_id = c.post
				WHERE c.post = ?
		";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute([$id]);
		$result = $stmt->fetchAll(PDO::FETCH_OBJ);

		if ($result) {
			return $result;
		}
		return [];

	}

	public function numComments(): int
	{
		$sql = "SELECT COUNT(*) FROM comments";
		$stmt = $this->connect()->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchColumn();

		return $result;
	}
}