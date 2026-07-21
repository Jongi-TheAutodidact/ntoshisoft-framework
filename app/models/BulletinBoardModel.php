<?php
defined('ROOTPATH') or exit('Access Denied!');

/**
 * Bulletin Board Model
 */
class BulletinBoardModel
{
    use Model;

    protected $table = 'bulletin_board';
    protected $primaryKey = 'id';

    protected $allowedColumns = [
        'post_id',
        'title',
        'content',
        'category',
        'target_audience',
        'priority',
        'attachment',
        'is_pinned',
        'published_date',
        'expiry_date',
        'author_id',
        'author_name',
        'status',
        'created_by',
        'updated_by'
    ];

	public function validate(array $data, int|string|null $id = null): bool
    {
        $this->errors = [];

        if (empty($data['title'])) {
            $this->errors['title'] = "Title is required";
        }

        if (empty($data['content'])) {
            $this->errors['content'] = "Content is required";
        }

        if (empty($data['category'])) {
            $this->errors['category'] = "Category is required";
        }

        if (empty($data['target_audience'])) {
            $this->errors['target_audience'] = "Target Audience is required";
        }

        if (empty($data['published_date'])) {
            $this->errors['published_date'] = "Published Date is required";
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

	public function getPublishedPosts(string $audience = 'all'): array|false
    {
        $today = date('Y-m-d');
        if ($audience == 'all') {
            return $this->where(['status' => 'published']);
        }
        $query = "SELECT * FROM {$this->table} WHERE status = 'published' AND (target_audience = ? OR target_audience = 'all') AND (expiry_date IS NULL OR expiry_date >= ?) ORDER BY is_pinned DESC, published_date DESC";
        return $this->query($query, [$audience, $today]);
    }

	public function getPinnedPosts(string $audience = 'all'): array|false
    {
        if ($audience == 'all') {
            return $this->where(['status' => 'published', 'is_pinned' => 1]);
        }
        $query = "SELECT * FROM {$this->table} WHERE status = 'published' AND is_pinned = 1 AND (target_audience = ? OR target_audience = 'all')";
        return $this->query($query, [$audience]);
    }
}
