<?php

class Html_elementsController extends \Framework\Core\AdminBaseController 
{
	 protected $baseColors = array(
        1 => '#1f77b4', // Blue
        2 => '#ff7f0e', // Orange
        3 => '#2ca02c', // Green
        4 => '#d62728', // Red
        5 => '#9467bd', // Purple
        6 => '#8c564b', // Brown
        7 => '#e377c2', // Pink
        8 => '#7f7f7f', // Gray
        9 => '#bcbd22', // Yellow
        10 => '#17becf', // Cyan
    );
	
	public function nestedSelectBox(string $tbl_name)
	{
        $this->loadModel("inc/html_elements");
		$data["render"] = $this->model_inc_html_elements->getAllCategories($tbl_name);
		$data["render"] = $this->assignColors($data["render"]);
	   return $data ;
	}
	
    public function nestedCheckBoxInSelectBox(string $tbl_name , $selected = [])
	{
        $this->loadModel("inc/html_elements");
		$data["render"] = $this->model_inc_html_elements->getAllCategories($tbl_name);
		$data["render"] = $this->assignColors($data["render"]);
	    return $data;
	}
	
	/* public function vipBlocks(string $tbl_name , $selected = [])
	{
        $this->loadModel("inc/html_elements");
		$blocks = $this->model_inc_html_elements->getVipBlocks($tbl_name);
		return array ("blocks" => $blocks , "selected"=> $selected);
	} */
	
	protected function assignColors($categories, $level = 1) {
        foreach ($categories as &$category) {
            // Assign color based on level
            $category['color'] = $this->assignColor($level);
            // Recursively assign colors to children
            if (!empty($category['children'])) {
                $category['children'] = $this->assignColors($category['children'], $level + 1);
            }
        }
        return $categories;
    }

    protected function assignColor($level) {
        // Calculate shade based on the level
        $shade = ($level % 10) * 10; // Adjust the multiplier as needed for different shades
        $color = $this->baseColors[($level % 10) + 1];

        // Generate a shade of the base color
        list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
        $r = max(0, min(255, $r + $shade));
        $g = max(0, min(255, $g + $shade));
        $b = max(0, min(255, $b + $shade));
        $newColor = sprintf("#%02x%02x%02x", $r, $g, $b);

        return $newColor;
    }


}

?>