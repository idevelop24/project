<?php

Class Html_elements extends \Framework\Core\Model {

    
	public function getAllCategories(string $tbl_name) {
        $query = "SELECT * FROM {$tbl_name} where ID!=1 ORDER BY sort";
        $categories = $this->db->query($query)->rows;
        return $this->buildTree($categories, 1);
    }

    private function buildTree(array $categories, $parentId) {
        $tree = [];
        foreach ($categories as $category) {
            if ($category['parent'] == $parentId) {
                $category['children'] = $this->buildTree($categories, $category['ID']);
                $tree[] = $category;
            }
        }
        return $tree;
    }
}

?>