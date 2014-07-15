<?php



namespace Apps\Blog\Model;


class Post extends \H1Soft\H\Web\Model {
    /**
     * 更新文章分类
     * @param type $post_id
     * @param type $_categorys
     * @return type
     */
    public function updateCategory($post_id,$_categorys) {        
        if(!$post_id){
            return;
        }
        if(!empty($_categorys) && is_array($_categorys)){
            $ids = join(',', $_categorys);
            $this->db()->delete('blog_to_category',"post_id in ($ids)");
            foreach($_categorys as $id){
                $this->db()->insert('blog_to_category',array(
                    'post_id'=>$post_id,
                    'category_id'=>$id
                ));
            }
        }
    }
    
}
