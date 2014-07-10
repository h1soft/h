<?php

namespace Apps\Backend\Controller;

class Auth extends \H1Soft\H\Web\Controller {

    function indexAction() {
//        echo $this->isAllowed(__METHOD__);
        $this->render('admin/index');
    }

    function invalidAction() {
        $this->render('admin/invalid');
    }

    /**
     * 资源管理
     */
    function resourcesAction() {
        $this->isSuperAdmin();

        //show resources
        $resources = $this->db()->query("SELECT *,CONCAT( path,  '-', sort_order ) AS path
 FROM  `h_resources` 
ORDER BY sort_order ASC,id DESC");
        $result = array();
        $this->category(0, 0, $resources, $result);



        $this->render('admin/auth_resources', array('list' => $result));
    }

    private function category($m, $id = 0, $category, &$result) {
        if ($id == "") {
            $id = 0;
        }
        $n = str_pad('', $m, '-', STR_PAD_RIGHT);
        $n = str_replace("-", "&nbsp;&nbsp;&nbsp;&nbsp;", $n);
        for ($i = 0; $i < count($category); $i++) {
            if ($category[$i]['parent'] == $id) {
                $category[$i]['Placeholder'] = $n . '|--';
                $result[] = $category[$i];
                $this->category($m + 1, $category[$i]['id'], $category, $result);
            }
        }
    }

    function editrsAction() {
        $this->isAdmin();
        $id = intval($this->get('id'));

        $resource = $this->db()->getRow("select * from `h_resources` where `id`=%d", array('id' => $id));

        $this->render('admin/auth_resources_modify', array('item' => $resource));
    }

    public function addrsAction() {
        $this->isAdmin();
        $namespace = $this->post('namespace');
        $category = $this->post('category');
        $description = $this->post('description');
        $tbname = $this->db()->tb_name('resources');
        if (empty($category)) {
            $category = 0;
            $parent_row = Array('id' => 0, 'parent' => 0, 'sort_order' => 1,
                'level' => 0, 'path' => '0', 'namespace' => '',
                'description' => '');
        } else {
            $parent_row = $this->db()->getRow("SELECT * FROM $tbname WHERE `id`='{$category}'");
        }

        try {
            if (class_exists($namespace)) {
//                $reflector = new \ReflectionClass($namespace);
//                $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
//                foreach ($methods as $method) {
//                    if (endsWith($method->getName(), 'Action')) {
//                        echo $method->getName();
//                    }
//                }
            } else if (endsWith($namespace, 'Controller')) {
                //扫描整个目录
                $namespace = stripcslashes($namespace);
                $controllers = scandir($namespace);                
                foreach ($controllers as $filename) {
                    if ($filename == '.' || $filename == '..') {
                        continue;
                    }
                    $filename = str_replace('.php', '', $filename);

                    if (class_exists($namespace . '\\' . $filename)) {

                        $reflector = new \ReflectionClass($namespace . '\\' . $filename);

                        $this->db()->insert('resources', array(
                            'namespace' => addslashes($namespace . '\\' . $filename),
                            'parent' => $category,
                            'sort_order' => 1,
                            'level' => $parent_row['level'],
                            'description' => $description,
                            'path' => $category ? $parent_row['path'] . '-' . $category : 0
                        ));
                        $insert_id = $this->db()->lastId();
                        $this->db()->update('resources', array('sort_order' => $insert_id), "id=$insert_id");
                        $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
                        foreach ($methods as $method) {
                            if (endsWith($method->getName(), 'Action')) {

                                $this->db()->insert('resources', array(
                                    'namespace' => addslashes($namespace . '\\' . $filename . '::' . $method->getName()),
                                    'parent' => $insert_id,
                                    'sort_order' => $insert_id,
                                    'level' => $parent_row['level'] + 1,
                                    'description' => $description,
                                    'path' => $parent_row['path'] . '-' . $insert_id
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

}

/*
 * $reflector = new \ReflectionClass('Apps\Backend\Controller\Auth');
        $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if(endsWith($method->getName(), 'Action') ){
               echo $method->getName();
            }            
        }
//        var_dump($reflector->getDocComment());

 */


