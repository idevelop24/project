<?php

namespace Framework\Core;

class CatalogBaseController extends Controller
{
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->efg = $registry->get("efg");
    }

    public function loadModel(){
        echo "Test Model Load Successfully <br />";
        $this->abc->show();
        $this->efg->write();
    }
}