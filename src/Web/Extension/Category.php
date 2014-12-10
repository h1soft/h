<?php
/*
 * This file is part of the HMVC package.
 *
 * (c) Allen Niu <h@h1soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * demo
 * sql script:
 * 
--
-- 表的结构 `h_blog_category`
--

CREATE TABLE IF NOT EXISTS `h_blog_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(4) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `level` tinyint(4) DEFAULT '0',
  `path` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(128) COLLATE utf8_bin NOT NULL,
  `description` varchar(256) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `path` (`path`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Resources' AUTO_INCREMENT=33 ;

--
-- 转存表中的数据 `h_blog_category`
--

INSERT INTO `h_blog_category` (`id`, `parent`, `sort_order`, `level`, `path`, `name`, `description`) VALUES
(22, 0, 1, 0, '0', 'Programing', ''),
(32, 22, 1, 1, '0-22', 'Test', ''),
(30, 22, 1, 1, '0-22', 'PHP', ''),
(26, 22, 1, 1, '0-22', 'Java', '');
 */
namespace hmvc\Web\Extension;

/**
 * 分类
 *
 * @author h@h1soft.net
 */
class Category {
    public static function query($_tbname){
        $db = \hmvc\Db\Db::getConnection();
        $_tbname = $db->tb_name($_tbname);
        
        $resources = $db->query("SELECT *,CONCAT( path,  '-', sort_order ) AS path
        FROM  `$_tbname` ORDER BY sort_order ASC,id DESC");
        $result = array();
        self::sort(0, 0, $resources, $result);   
        return $result;
    }
    
    public static function sort($level, $id, $category, &$result) {
        if ($id == "") {
            $id = 0;
        }
        $n = str_pad('', $level, '-', STR_PAD_RIGHT);
        $n = str_replace("-", "&nbsp;&nbsp;&nbsp;&nbsp;", $n);
        for ($i = 0; $i < count($category); $i++) {
            if ($category[$i]['parent'] == $id) {
                $category[$i]['Placeholder'] = $n . '|--';
                $category[$i]['level'] = $level;
                $result[] = $category[$i];
                self::sort($level + 1, $category[$i]['id'], $category, $result);
            }
        }
    }
    
}
