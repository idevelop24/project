<?php

class ModelBlogPosts extends \Framework\Core\AdminBaseModel {

    public function addPost($data) {
        $this->db->query("INSERT INTO tbl_posts SET title = '" . $this->db->escape($data['title']) . "', content = '" . $this->db->escape($data['content']) . "', posts_categories_id = '" . (int)$data['posts_categories_id'] . "', image = '" . $this->db->escape($data['image']) . "'");

        $post_id = $this->db->getLastId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE tbl_posts SET image = '" . $this->db->escape($data['image']) . "' WHERE id = '" . (int)$post_id . "'");
        }

        return $post_id;
    }

    public function getPosts($data = []) {
        $sql = "SELECT p.*, c.name AS category_name FROM tbl_posts p LEFT JOIN tbl_posts_categories c ON (p.posts_categories_id = c.ID)";

        if (!empty($data['filter_name'])) {
            $sql .= " WHERE p.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sort_data = array(
            'p.title',
            'category_name',
            'p.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.title";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalPosts($data = []) {
        $sql = "SELECT COUNT(*) AS total FROM tbl_posts";

        if (!empty($data['filter_name'])) {
            $sql .= " WHERE title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
}
