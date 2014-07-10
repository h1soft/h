<?php

namespace H1Soft\H\Web;

/**
 * $page = new \H1Soft\H\Web\Page();
       $page->setPageSize(1);
       echo $page->count("select count(*) from `h_resources`");
       echo $page->getTotalPage();
 */

class Page {

    protected $_total_page = 0;
    protected $_total_rows = 0;
    protected $_cur_page = 0;
    protected $_offset = 0;
    protected $_pagesize = 20;

    public function __construct($_total_rows=0, $_cur_page = 1, $_pagesize = 20) {
        $this->_total_rows = $_total_rows;
        $this->_cur_page = $_cur_page;
        $this->_pagesize = $_pagesize;
        $this->_init();
    }

    private function _init() {
        $this->_total_page = ceil($this->_total_rows / $this->_pagesize);
        $this->_offset = ($this->_cur_page - 1) * $this->_pagesize;
    }

    public function pageHtml() {
        $this->_total_page = ceil($this->_total_rows / $this->_pagesize);
    }
    
    public function count($str,$data = false,$db = false){
        if(!$db) {
            $db = \H1Soft\H\Db\Db::getConnection();
        }        
        if($data && is_array($data)){
            $row = $db->getRow(vsprintf($str, $data),MYSQLI_NUM);            
        }else{
            $row = $db->getRow($str,MYSQLI_NUM);
        }
        if(isset($row[0])){
            $this->_total_rows = intval($row[0]);
        }
        $this->_init();
        return $this->_total_rows;
    }

    public function getPageSize() {
        return $this->_pagesize;
    }

    public function getOffset() {
        return $this->_offset;
    }

    public function getCurPage() {
        return $this->_cur_page;
    }
    
    public function getTotalPage() {
        return $this->_total_page;
    }
    
    public function getTotalRows() {
        return $this->_total_rows;
    }

    public function setCurPage($_cur_page) {
        $this->_cur_page = $_cur_page;
        return $this;
    }
    
    public function setPageSize($_size) {
        $this->_pagesize = $_size;
        return $this;
    }
}

/*
 * 
 * <ul class="pagination">
 * <li class="paginate_button previous disabled" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_previous">
 * <a href="#">Previous</a></li>
 * 
 * <li class="paginate_button active" aria-controls="dataTables-example" tabindex="0">
 * <a href="#">1</a></li>
 * <li class="paginate_button " aria-controls="dataTables-example" tabindex="0">
 * <a href="#">2</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0">
 * <a href="#">3</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0">
 * <a href="#">4</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0">
 * <a href="#">5</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0">
 * <a href="#">6</a></li><li class="paginate_button next" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_next"><a href="#">Next</a></li></ul>
 * 
 */