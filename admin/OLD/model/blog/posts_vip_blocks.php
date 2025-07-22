<?php

Class Posts_vip_blocks extends \Framework\Core\Model {

    public function List(array $data = []){
        $query = $this->db->query("SELECT * FROM tbl_posts_blocks ORDER BY sort ASC");
		return $query->rows;
	}
}

?>