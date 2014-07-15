<?php

namespace Apps\Backend\Controller;

/**
 * 授权管理
 * @authName 授权管理
 * @authDescription 统一授权管理解决方案
 */
class Auth extends \H1Soft\H\Web\Controller {

    /**
     * 权限验证失败
     * @authName 权限验证失败
     * @authDescription 
     * @skipAuth
     */
    function invalidAction() {
        $this->render('admin/invalid');
    }

    /**
     * 资源列表
     * @authName 资源列表
     * @authDescription 资源列表
     */
    function resourcesAction() {
        $this->assign('menu_setting', 1);
        $this->isSuperAdmin();
        
        $result = \H1Soft\H\Web\Extension\Category::query('resources');
        $this->saveUrlRef();


        $this->render('admin/auth_resources', array('list' => $result));
    }

    /**
     * 删除资源
     * @authName 删除资源
     * @authDescription 
     */
    function delrsAction() {
        $this->isSuperAdmin();
        $id = intval($this->get('id'));
        $this->db()->delete('resources', "`id`=$id");
    }

    /**
     * 修改资源
     * @authName 修改资源
     * @authDescription 
     */
    function editrsAction() {
        $this->isSuperAdmin();
        $this->assign('menu_setting', 1);
        $id = intval($this->get('id'));
        if ($this->isPost()) {
            $post = array(
                'name' => $this->post('name'),
                'namespace' => $this->post('namespace'),
                'description' => $this->post('description'),
                'sort_order' => intval($this->post('sort_order')),
            );

            $this->db()->update('resources', $post, "id=$id");
            $this->redirect($this->urlRef());
        }

        $tbname = $this->db()->tb_name('resources');

        $resource = $this->db()->getRow("select * from `$tbname` where `id`=%d", array('id' => $id));

        $this->render('admin/auth_resources_modify', array('item' => $resource, 'id' => $id));
    }

    /**
     * 添加资源
     * @authName 添加资源
     * @authDescription AJAX添加
     */
    public function addrsAction() {
        $this->isSuperAdmin();
        $this->assign('menu_setting', 1);
        $namespace = $this->post('namespace');

        $name = $this->post('name');
        $category = $this->post('category');
        $description = $this->post('description');
        $tbname = $this->db()->tb_name('resources');
        $parent_row = array(
            'namespace' => $namespace,
            'name' => $name,
            'parent' => $category,
            'sort_order' => 1,
            'level' => 0,
            'description' => $description,
            'path' => 0
        );
        //删除存在的数据
        $sn = str_replace('\\', '\\\\\\\\', $namespace);
        $this->db()->exec("DELETE FROM $tbname WHERE `namespace` like '$sn%'");
        if (!empty($category)) {
            $parent_category = $this->db()->getRow("SELECT * FROM $tbname WHERE `parent`='{$category}'");
            $parent_row['path'] = $parent_category['path'] . '-' . $parent_category['id'];
            $parent_row['level'] = $parent_category['level'] + 1;
            $parent_row['parent'] = $category;
        }


        try {
            if (class_exists($namespace)) {
                
            } else if (endsWith($namespace, 'Controller')) {
                //扫描整个目录
                $controllers = scandir($namespace);
                //插入根节点
                $this->db()->insert('resources', $parent_row);
                $parent_id = $this->db()->lastId();

                foreach ($controllers as $filename) {
                    if ($filename == '.' || $filename == '..') {
                        continue;
                    }
                    $filename = str_replace('.php', '', $filename);

                    if (class_exists($namespace . '\\' . $filename)) {

                        $reflector = new \ReflectionClass($namespace . '\\' . $filename);

                        $name = \H1Soft\H\Utils\PHPDoc::findName('authName', $reflector->getDocComment());
                        $description = \H1Soft\H\Utils\PHPDoc::findName('authDescription', $reflector->getDocComment());
                        $skipAuth = \H1Soft\H\Utils\PHPDoc::bool('skipAuth', $reflector->getDocComment());
                        if ($skipAuth) {
                            continue;
                        }

                        $this->db()->insert('resources', array(
                            'namespace' => $namespace . '\\' . $filename,
                            'parent' => $parent_id,
                            'sort_order' => 1,
                            'level' => $parent_row['level'] + 1,
                            'name' => $name ? $name : $namespace . '\\' . $filename,
                            'description' => $description,
                            'path' => $parent_row['path'] . '-' . $parent_id
                        ));
                        $insert_id = $this->db()->lastId();
                        $cur_class = $this->db()->getRow("select * from `h_resources` where `id`=%d", array('id' => $insert_id));
                        $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
                        foreach ($methods as $method) {
                            if (endsWith($method->getName(), 'Action')) {
                                $skipAuth = \H1Soft\H\Utils\PHPDoc::bool('skipAuth', $method->getDocComment());
                                if ($skipAuth) {
                                    continue;
                                }
                                $name = \H1Soft\H\Utils\PHPDoc::findName('authName', $method->getDocComment());
                                $description = \H1Soft\H\Utils\PHPDoc::findName('authDescription', $method->getDocComment());
                                $this->db()->insert('resources', array(
                                    'namespace' => $namespace . '\\' . $filename . '::' . $method->getName(),
                                    'parent' => $insert_id,
                                    'sort_order' => 1,
                                    'level' => $cur_class['level'] + 1,
                                    'name' => $name,
                                    'description' => $description,
                                    'path' => $cur_class['path'] . '-' . $insert_id
                                ));
                            }
                        }
                    }
                }
            } else {
                echo '命名空间或类不存在';
            }
        } catch (Exception $exc) {
            echo '添加失败';
        }
    }

    public function groupAction() {
        $this->isSuperAdmin();
        $this->assign('menu_setting', 1);
        
        $tbname = $this->db()->tb_name('group');
        //show resources
        $groups = $this->db()->query("SELECT * FROM  `$tbname` ");

        $this->saveUrlRef();


        $this->render('admin/auth_group', array('list' => $groups));
    }

    public function addGroupAction() {
        $this->assign('menu_setting', 1);
        $this->isSuperAdmin();
        if ($this->isPost()) {
            $this->db()->insert('group', array(
                'name' => $this->post('name'),
                'description' => $this->post('description'),
            ));
            $this->redirect($this->urlRef());
        }
    }

    public function modifyGroupAction() {
        $this->isSuperAdmin();
        $this->assign('menu_setting', 1);
        $id = intval($this->get('id'));
        if ($this->isPost()) {
            $post = array(
                'name' => $this->post('name'),             
                'description' => $this->post('description'),                
            );

            $this->db()->update('group', $post, "id=$id");
            $this->redirect($this->urlRef());
        }

        $tbname = $this->db()->tb_name('group');

        $group = $this->db()->getRow("select * from `$tbname` where `id`=%d", array('id' => $id));

        $this->render('admin/auth_group_modify', array('item' => $group, 'id' => $id));
    }

    public function deleteGroupAction() {
        $this->isSuperAdmin();
        $id = intval($this->get('id'));
        $this->db()->delete('group', "id='$id'");
        $this->redirect($this->urlRef());
    }

}
