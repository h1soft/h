<?php

namespace Apps\Blog\Model;

/**
 * 博客模块
 */
class Blog extends \H1Soft\H\Web\Model {
    
    public function init() {
        
    }
    
    /**
     * 获取一条POST
     * @param int $post_id
     * @return Post
     */
    public function Post($post_id) {
        return $this->db()->getOne('blog_posts', "`id`=$post_id");
    }
    
    public function Posts($params = NULL) {        
        if($params){
            parse_str($params);
            $where = '';            
            if(isset($category)){
                $where .= " category_id=$category ";
                $this->db()->query("SELECT * FROM ");
            }else{
                
            }
            
        }else{
            return $this->db()->getAll('blog_posts', NULL, "post_date desc");
        }
        
    }
    
    public function post_link($post_id) {        
        return sprintf("%s/post/%d.html", \H1Soft\H\Web\Application::basePath(),$post_id);
    }
    public function homeUrl() {        
        return sprintf("%s/index.html", \H1Soft\H\Web\Application::basePath());
    }
}
