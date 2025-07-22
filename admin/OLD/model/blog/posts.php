<?php

Class Posts extends \Framework\Core\Model {

	public $conditions = [];
	public $where_clause;
	public $sort_clause = "ORDER BY Post_id DESC";
	public $pagination;
	
    
    public function Grid(array $data = [], int $start = 0, int $limit = config_admin_grid_rows_limit) {
    
		// Define conditions
		if (isset($data["s_post_category"]) && !empty($data["s_post_category"])) {
			$this->conditions[] = " p.id in (SELECT posts_id FROM tbl_posts_to_categories WHERE posts_categories_id=".(int)$data["s_post_category"].")";
		}
		if (isset($data["s_post_title"]) && !empty($data["s_post_title"])) {
			$this->conditions[] = " p.title LIKE '%" . $this->db->escape($data["s_post_title"]) . "%'";
		}
		if (isset($data["s_blocks"]) && !empty($data["s_blocks"])) {
			$this->conditions[] = " p.id in (SELECT posts_id FROM tbl_posts_to_blocks WHERE post_blocks_id=".(int)$data["s_blocks"].")";
		}
		if (isset($data["s_post_status"]) && !empty($data["s_post_status"])) {
			$this->conditions[] = " p.status = ".(int) $data["s_post_status"]."";
		}
		if (isset($data["s_is_archive"]) && !empty($data["s_is_archive"])) {
			$this->conditions[] = " p.is_archive = ".(int) $data["s_is_archive"]."";
		}

		// Check if we have any conditions to make where clause
		if (count($this->conditions) > 0) {
			$this->where_clause = " WHERE " . implode(" AND ", $this->conditions);
		}

		// Define and convert sort fields in database
		$sortConvert = array(
			"id" => "Post_id",
			"category" => "Post_category",
			"name" => "Post_title",
			"status" => "Post_Status"
		);

		// Check if sort exist - if exist we fill it here else use default sort
		if (isset($data["sort"]) && !empty($data["sort"])) {
			$this->sort_clause = "ORDER BY " . $this->db->escape($sortConvert[$data["sort"]]) . " " . $this->db->escape($data["sort_by"]);
		}

		// Write query and use the clauses in it
		$sql = "SELECT p.id as Post_id, p.title as Post_title, p.status as Post_Status, pc.name as Post_category, ps.name as Post_status_name 
				FROM ". DB_PREFIX ."posts p 
				LEFT JOIN ". DB_PREFIX ."posts_categories pc ON p.posts_categories_id = pc.id
				LEFT JOIN ". DB_PREFIX ."posts_status ps ON p.status = ps.id 
				" . $this->where_clause . " " . $this->sort_clause . " 
				LIMIT " . (int)$start . ", " . (int)$limit;

		// Get total products for pagination
		$sql_total = "SELECT COUNT(id) as total 
					  FROM tbl_posts p 
					  " . $this->where_clause;

		// Execute The Query
		$query = $this->db->query($sql);
		$total_query = $this->db->query($sql_total);

		return array(
			"list" => $query->rows,
			"total" => $total_query->row['total']
		);
	}

	public function getRow(int $row_id){
		$query = $this->db->query("select * from tbl_posts where id= ".(int) $row_id."");
			return $query->row;
	}
	
	public function getImage(int $row_id){
		$query = $this->db->query("select image from tbl_posts where id= ".(int) $row_id."");
			return $query->row;
	}

	public function getCategories(int $row_id){
		$list = [];
		$query = $this->db->query("SELECT posts_categories_id FROM `tbl_posts_to_categories` WHERE posts_id=".(int) $row_id."");
			foreach($query->rows as $category)
				$list[] = (int) $category["posts_categories_id"];
					return array_unique($list);
	}

	public function getVipBlocks(int $row_id){
		$list = [];
		$query = $this->db->query("SELECT post_blocks_id FROM `tbl_posts_to_blocks` WHERE posts_id=".(int) $row_id."");
			foreach($query->rows as $block)
				$list[] = (int) $block["post_blocks_id"];
					return array_unique($list);
	}

	public function Create($data) {
        $sql = "INSERT INTO ". DB_PREFIX ."posts SET 
                title = '" . $this->db->escape($data['post_title']) . "', 
                content = '" . $this->db->escape($data['post_content']) . "', 
                image = '" . $this->db->escape($data['post_image']) . "', 
                status = '" . (int)$data['post_status'] . "', 
                created_at = NOW()";

        $this->db->query($sql);
        $inserted_id =  $this->db->lastInsertId();
		
		//add vip blocks if checked
		foreach ($data["blocks"] as $block_id) {
			$this->db->query("INSERT INTO tbl_posts_to_blocks 
			SET 
                posts_id = '" . (int)$inserted_id . "', 
                post_blocks_id = '" . (int)$block_id . "'");
		}
		//add post categories
		foreach ($data["post_category2"] as $category_id) {
			$this->db->query("INSERT INTO tbl_posts_to_categories 
			SET 
                posts_id = '" . (int)$inserted_id . "', 
                posts_categories_id = '" . (int)$category_id . "'");
		}
		return $inserted_id;

    }

	public function Update(int $row_id, array $data) {
        $this->db->query("update ". DB_PREFIX ."posts SET 
                title = '" . $this->db->escape($data['post_title']) . "', 
                content = '" . $this->db->escape($data['post_content']) . "', 
                status = '" . (int)$data['post_status'] . "', 
                modify_at = NOW()
				where id = ".(int)$row_id ."");
        
		
		//edit vip blocks if checked
		$this->db->query("DELETE FROM `tbl_posts_to_blocks` WHERE posts_id=$row_id");
		foreach ($data["blocks"] as $block_id) {
			$this->db->query("INSERT INTO tbl_posts_to_blocks 
			SET 
                posts_id = '" . (int)$row_id . "', 
                post_blocks_id = '" . (int)$block_id . "'");
		}
		//edit post categories
		$this->db->query("DELETE FROM `tbl_posts_to_categories` WHERE posts_id=$row_id");
		foreach ($data["post_category2"] as $category_id) {
			$this->db->query("INSERT INTO tbl_posts_to_categories 
			SET 
                posts_id = '" . (int)$row_id . "', 
                posts_categories_id = '" . (int)$category_id . "'");
		}
    }
	
	public function Drop(int $row_id){
		
		//get item main image
		$main_image = $this->db->query("select image from tbl_posts where id= ".(int) $row_id."");
		//delete item main image
		unlink(DIR_POSTS_ITEM_IMAGE."/original/".$main_image->row["image"]);
		unlink(DIR_POSTS_ITEM_IMAGE."/vs/".$main_image->row["image"]);
		unlink(DIR_POSTS_ITEM_IMAGE."/s/".$main_image->row["image"]);
		unlink(DIR_POSTS_ITEM_IMAGE."/sg/".$main_image->row["image"]);
		unlink(DIR_POSTS_ITEM_IMAGE."/g/".$main_image->row["image"]);
		unlink(DIR_POSTS_ITEM_IMAGE."/item/".$main_image->row["image"]);
		//delete item from table
		$this->db->query("DELETE FROM tbl_posts WHERE id = ".(int) $row_id ."");
		//delete item from blocks table
		$this->db->query("DELETE FROM tbl_posts_to_blocks WHERE posts_id=".(int) $row_id ."");
		//delete item from categories table
		$this->db->query("DELETE FROM tbl_posts_to_categories WHERE posts_id=".(int) $row_id ."");
	}

	public function countRows(){
        $query = $this->db->query("SELECT COUNT(id) as postsCount FROM tbl_posts");
		return $query->row["postsCount"];
	}
	
	public function ManageImages(int $row_id, array $data, string $command =  '' ){
        if ($command == 'change_item_main_image')
		{
			$main_image = $this->db->query("select image from tbl_posts where id= ".(int) $row_id."");
			//delete item main image
			unlink(DIR_POSTS_ITEM_IMAGE."/original/".$main_image->row["image"]);
			unlink(DIR_POSTS_ITEM_IMAGE."/vs/".$main_image->row["image"]);
			unlink(DIR_POSTS_ITEM_IMAGE."/s/".$main_image->row["image"]);
			unlink(DIR_POSTS_ITEM_IMAGE."/sg/".$main_image->row["image"]);
			unlink(DIR_POSTS_ITEM_IMAGE."/g/".$main_image->row["image"]);
			unlink(DIR_POSTS_ITEM_IMAGE."/item/".$main_image->row["image"]);
			$this->db->query("UPDATE tbl_posts SET image = '" . $this->db->escape($data['new_image_uploaded']) . "' WHERE id= ".(int) $row_id."");
		}
	}
	
}

?>