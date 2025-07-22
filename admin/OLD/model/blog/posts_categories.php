<?php

Class posts_categories extends \Framework\Core\Model {

	
    public function countRows(){
        $query = $this->db->query("SELECT COUNT(id) as postsCatsCount FROM ". DB_PREFIX ."posts_categories");
		return $query->row["postsCatsCount"];
	}
}

?>