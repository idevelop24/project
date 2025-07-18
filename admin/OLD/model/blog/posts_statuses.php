<?php

Class Posts_statuses extends \Framework\Core\Model {

	
    public function List(array $data = []){
        $query = $this->db->query("SELECT * FROM tbl_posts_status ORDER BY SORT ASC");
		return $query->rows;
	}
	
	public function upperIt($string){
        return strtoupper($string);
	}
}

?>