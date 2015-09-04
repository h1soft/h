<?php

/*
 * Copyright (C) 2014 Allen Niu <h@h1soft.net>

 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.



 * This file is part of the H1Cart package.
 * (w) http://www.h1cart.com
 * (c) Allen Niu <h@h1soft.net>

 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.


 */

namespace hmvc\Collections;

/**
 * Description of ArrayList
 *
 * @author allen <i@w4u.cn>
 */
class ArrayList {

    /**
     * 集合元素
     * @var array
     * @access protected
     */
    protected $elementData = array();

    /**
     * 架构函数
     * @access public
     * @param string $elements  初始化数组元素
     */
    public function __construct($elements = array()) {
        if (!empty($elements)) {
            $this->elementData = $elements;
        }
    }

    /**
     * 若要获得迭代因子，通过getIterator方法实现
     * @access public
     * @return ArrayObject
     */
    public function getIterator() {
        return new ArrayObject($this->elementData);
    }

    /**
     * 增加元素
     * @access public
     * @param mixed $element  要添加的元素
     * @return boolean
     */
    public function add($element) {
        return (array_push($this->elementData, $element)) ? true : false;
    }

    //
    public function unshift($element) {
        return (array_unshift($this->elementData, $element)) ? true : false;
    }

    //
    public function pop() {
        return array_pop($this->elementData);
    }

    /**
     * 增加元素列表
     * @access public
     * @param ArrayList $list  元素列表
     * @return boolean
     */
    public function addAll($list) {
        $before = $this->size();
        foreach ($list as $element) {
            $this->add($element);
        }
        $after = $this->size();
        return ($before < $after);
    }

    /**
     * 清除所有元素
     * @access public
     */
    public function clear() {
        $this->elementData = array();
    }

    /**
     * 是否包含某个元素
     * @access public
     * @param mixed $element  查找元素
     * @return string
     */
    public function contains($element) {
        return (array_search($element, $this->elementData) !== false );
    }

    /**
     * 根据索引取得元素
     * @access public
     * @param integer $index 索引
     * @return mixed
     */
    public function get($index) {
        return $this->elementData[$index];
    }

    /**
     * 查找匹配元素，并返回第一个元素所在位置
     * 注意 可能存在0的索引位置 因此要用===False来判断查找失败
     * @access public
     * @param mixed $element  查找元素
     * @return integer
     */
    public function indexOf($element) {
        return array_search($element, $this->elementData);
    }

    /**
     * 判断元素是否为空
     * @access public
     * @return boolean
     */
    public function isEmpty() {
        return empty($this->elementData);
    }

    /**
     * 最后一个匹配的元素位置
     * @access public
     * @param mixed $element  查找元素
     * @return integer
     */
    public function lastIndexOf($element) {
        for ($i = (count($this->elementData) - 1); $i > 0; $i--) {
            if ($element == $this->get($i)) {
                return $i;
            }
        }
    }

    public function toJson() {
        return json_encode($this->elementData);
    }

    /**
     * 根据索引移除元素
     * 返回被移除的元素
     * @access public
     * @param integer $index 索引
     * @return mixed
     */
    public function remove($index) {
        $element = $this->get($index);
        if (!is_null($element)) {
            array_splice($this->elementData, $index, 1);
        }
        return $element;
    }

    /**
     * 移出一定范围的数组列表
     * @access public
     * @param integer $offset  开始移除位置
     * @param integer $length  移除长度
     */
    public function removeRange($offset, $length) {
        array_splice($this->elementData, $offset, $length);
    }

    /**
     * 移出重复的值
     * @access public
     */
    public function unique() {
        $this->elementData = array_unique($this->elementData);
    }

    /**
     * 取出一定范围的数组列表
     * @access public
     * @param integer $offset  开始位置
     * @param integer $length  长度
     */
    public function range($offset, $length = null) {
        return array_slice($this->elementData, $offset, $length);
    }

    /**
     * 设置列表元素
     * 返回修改之前的值
     * @access public
     * @param integer $index 索引
     * @param mixed $element  元素
     * @return mixed
     */
    public function set($index, $element) {
        $previous = $this->get($index);
        $this->elementData[$index] = $element;
        return $previous;
    }

    /**
     * 获取列表长度
     * @access public
     * @return integer
     */
    public function size() {
        return count($this->elementData);
    }

    /**
     * 转换成数组
     * @access public
     * @return array
     */
    public function toArray() {
        return $this->elementData;
    }

    // 列表排序    
    public function ksort() {
        ksort($this->elementData);
    }

    // 列表排序
    public function asort() {
        asort($this->elementData);
    }

    // 逆向排序
    public function rsort() {
        rsort($this->elementData);
    }

    // 自然排序
    public function natsort() {
        natsort($this->elementData);
    }

}
