<?php

/**
 * NtoshiTable Model class
 */

defined('ROOTPATH') or exit('Access Denied!');

class UserModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getTotalCount(string $search = ''): mixed
    {
        $query = "SELECT COUNT(*) as total FROM users WHERE name LIKE ? OR email LIKE ?";
        return $this->db->fetchSingle($query, ["%$search%", "%$search%"])['total'];
    }

    public function getUsers(string $search, int $offset, int $limit, string $orderBy, string $orderDir): array
    {
        $query = "SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY $orderBy $orderDir LIMIT ?, ?";
        return $this->db->fetchAll($query, ["%$search%", "%$search%", $offset, $limit]);
    }
}
