<?php

namespace Apps\Backend\Controller;

class Blog extends AdminController {

    public function indexAction() {
        $this->assign('menu_blog', 1);
        $this->saveUrlRef();
        $tbname = $result = $this->db()->tb_name('blog_posts');
        $params = array();
        $params['page'] = $this->get('page', 0);
        $result = $this->db()->query("select * from $tbname WHERE 1 ");
        $page = new \H1Soft\H\Web\Page();
        $page->setCurPage($this->get('page', 0))->setUrl('blog/index')->setParams($params);
        $page->count("select count(*) from `$tbname`");
        $this->assign('page', $page);
        $this->assign('list', $result);

        $this->render('admin/blog_index');
    }

    /**
     * 写文章
     */
    public function writeAction() {
        $this->assign('menu_blog', 1);
        $id = $this->get('p', 0);
        
        if ($id) {//修改
            $post = $this->db()->getOne('blog_posts', "`id`=$id");
            $post_category_result = $this->db()->getAll("blog_to_category", "post_id=$id");
            if ($post_category_result) {
                $post_category = array();
                foreach ($post_category_result as $pc) {
                    $post_category[] = $pc['category_id'];
                }
                $this->assign('post_category', $post_category);
            }
            $this->assign('post', $post);
        }
        if ($this->isPost()) {
            $category = $this->post('category');
            $content = $this->post('content');
            $title = $this->post('title');
            $post_date = $this->post('post_date');
            if (\H1Soft\H\Utils\Date::isDate($post_date)) {
                $post_date = strtotime($post_date);
            }
            $post_status = $this->post('post_status');
            $comment_status = $this->post('comment_status');
            if (!$post) {
                $post_id = $this->db()->insert('blog_posts', array(
                    'author' => \H1Soft\H\Web\Auth::getInstance()->getId(),
                    'title' => $title,
                    'post_name' => $title,
                    'content' => $content,
                    'post_status' => $post_status,
                    'comment_status' => $comment_status,
                    'post_date' => time(),
                    'post_modifyed' => time(),
                ));
                $post = \Apps\Blog\Model\Post::getInstance();
                $post->updateCategory($post_id, $category);
                $this->showFlashMessage("添加成功", H_SUCCESS);
            } else {
                $post['title'] = $title;
                $post['post_name'] = $title;
                $post['content'] = $content;
                $post['post_status'] = $post_status;
                $post['comment_status'] = $comment_status;
                $post['post_modifyed'] = time();
                $this->db()->update('blog_posts', $post, "`id`=$id");
                $post = \Apps\Blog\Model\Post::getInstance();
                $post->updateCategory($id, $category);
                $this->showFlashMessage("修改成功", H_SUCCESS);
            }
        }
        $this->assign('category', \H1Soft\H\Web\Extension\Category::query('blog_category'));
        $this->assign('editor', \Apps\UEditor\Helper\UEditor::create('content'));
       
        $this->render('admin/blog_write');
    }

    public function categoryAction() {
        $this->assign('menu_blog', 1);
        $result = \H1Soft\H\Web\Extension\Category::query('blog_category');

        $this->saveUrlRef();

        $this->render('admin/blog_category', array('list' => $result));
    }

    public function addcategoryAction() {
        $name = $this->post('name');
        $category = $this->post('category');
        $description = $this->post('description');
        $tbname = $this->db()->tb_name('blog_category');
        $parent_row = array(
            'name' => $name,
            'parent' => $category,
            'sort_order' => 1,
            'level' => 0,
            'description' => $description,
            'path' => 0
        );
        if (!empty($category)) {
            $parent_category = $this->db()->getRow("SELECT * FROM $tbname WHERE `id`='{$category}'");
            $parent_row['path'] = $parent_category['path'] . '-' . $parent_category['id'];
            $parent_row['level'] = $parent_category['level'] + 1;
            $parent_row['parent'] = $category;
        }
        $this->db()->insert('blog_category', $parent_row);
    }

    public function editcategoryAction() {
        $this->assign('menu_blog', 1);
        $id = intval($this->get('id'));
        $category = $this->db()->getOne("blog_category", " `id`=$id ");
        if ($this->isPost()) {
            $post = array(
                'name' => $this->post('name'),
                'description' => $this->post('description'),
                'sort_order' => intval($this->post('sort_order')),
            );
            $select_category = $this->post('category');
            if ($category['parent'] != $select_category && $id != $select_category) {
                $post['parent'] = $select_category;
                //更新level
                $parent_category = $this->db()->getOne("blog_category", " `id`=$select_category ");
                $post['level'] = $select_category == 0 ? 0 : $parent_category['level'] + 1;
                $post['path'] = $select_category == 0 ? 0 : $parent_category['path'] . '-' . $select_category;
                unset($parent_category);
            }

            $this->db()->update('blog_category', $post, "id=$id");
            $this->redirect($this->urlRef());
        }

        $result = \H1Soft\H\Web\Extension\Category::query('blog_category');


        $this->render('admin/blog_category_modify', array('item' => $category, 'id' => $id, 'list' => $result));
    }

    function rmcategoryAction() {
        $this->isSuperAdmin();
        $id = intval($this->get('id'));
        if (!$id) {
            $this->showFlashMessage("类别不存在", H_ERROR, $this->urlRef());
        }
        $check = $this->db()->getOne('blog_category', "parent=$id");
        if ($check) {
            $this->showFlashMessage("请先删除子类别", H_ERROR, $this->urlRef());
        }
        $this->db()->delete('blog_category', "`id`=$id");
        $this->showFlashMessage("删除成功", H_SUCCESS, $this->urlRef());
    }

}
