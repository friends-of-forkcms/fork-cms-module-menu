<?php

namespace Frontend\Modules\Menu\Engine;

use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation;

/**
 * In this file we store all generic functions that we will be using in the Menu module
 *
 * @author Jesse Dobbelaere <jesse@dobbelaere-ae.be>
 */
class Model
{
    /**
     * Fetches a certain item
     *
     * @param string $URL
     * @return array
     */
    public static function get($URL)
    {
        $item = (array) FrontendModel::get('database')->getRecord(
            'SELECT i.*,
             m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
             m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
             m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
             FROM menu AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE m.url = ?',
            array((string) $URL)
        );

        // no results?
        if (empty($item)) {
            return array();
        }

        // create full url
        $item['full_url'] = Navigation::getURLForBlock('Menu', 'detail') . '/' . $item['url'];

        return $item;
    }

    /**
     * Get all items (at least a chunk)
     *
     * @param int[optional] $limit The number of items to get.
     * @param int[optional] $offset The offset.
     * @return array
     */
    public static function getAll($limit = 10, $offset = 0)
    {
        $items = (array) FrontendModel::get('database')->getRecords(
            'SELECT i.*, m.url
             FROM menu AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.language = ?
             ORDER BY i.sequence ASC, i.id DESC LIMIT ?, ?',
            array(FRONTEND_LANGUAGE, (int) $offset, (int) $limit));

        // no results?
        if (empty($items)) {
            return array();
        }

        // get detail action url
        $detailUrl = Navigation::getURLForBlock('Menu', 'detail');

        // prepare items for search
        foreach ($items as &$item) {
            $item['full_url'] =  $detailUrl . '/' . $item['url'];
        }

        // return
        return $items;
    }

    /**
     * Get the number of items
     *
     * @return int
     */
    public static function getAllCount()
    {
        return (int) FrontendModel::get('database')->getVar(
            'SELECT COUNT(i.id) AS count
             FROM menu AS i
             WHERE i.language = ?',
            array(FRONTEND_LANGUAGE)
        );
    }

    /**
     * Get all category items (at least a chunk)
     *
     * @param int $categoryId
     * @param int[optional] $limit The number of items to get.
     * @param int[optional] $offset The offset.
     * @return array
     */
    public static function getAllByCategory($categoryId, $limit = 10, $offset = 0)
    {
        $items = (array) FrontendModel::get('database')->getRecords(
            'SELECT i.*, m.url
             FROM menu AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.category_id = ? AND i.language = ?
             ORDER BY i.sequence ASC, i.id DESC LIMIT ?, ?',
            array($categoryId, FRONTEND_LANGUAGE, (int) $offset, (int) $limit));

        // no results?
        if (empty($items)) {
            return array();
        }

        // get detail action url
        $detailUrl = Navigation::getURLForBlock('Menu', 'detail');

        // prepare items for search
        foreach ($items as &$item) {
            $item['full_url'] = $detailUrl . '/' . $item['url'];
        }

        // return
        return $items;
    }
    /**
     * Get all categories used
     *
     * @return array
     */
    public static function getAllCategories()
    {
        $return = (array) FrontendModel::get('database')->getRecords(
            'SELECT c.id, c.title AS label, COUNT(c.id) AS total
             FROM menu_categories AS c
             INNER JOIN menu_alacarte AS i ON c.id = i.category_id AND c.language = i.language
             GROUP BY c.id
             ORDER BY c.sequence ASC',
            array(), 'id'
        );

        // loop items and unserialize
        foreach ($return as &$row) {
            if (isset($row['meta_data'])) {
                $row['meta_data'] = @unserialize($row['meta_data']);
            }
        }

        return $return;
    }

    /**
     * Fetches a certain category
     *
     * @param string $URL
     * @return array
     */
    public static function getCategory($URL)
    {
        $item = (array) FrontendModel::get('database')->getRecord(
            'SELECT i.*,
             m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
             m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
             m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
             FROM menu_categories AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE m.url = ?',
            array((string) $URL)
        );

        // no results?
        if (empty($item)) {
            return array();
        }

        // create full url
        $item['full_url'] = Navigation::getURLForBlock('Menu', 'category') . '/' . $item['url'];

        return $item;
    }

    /**
     * Fetches a certain menu item
     *
     * @param int $id
     * @return array
     */
    public static function getMenu($id)
    {
        $item = (array) FrontendModel::get('database')->getRecord(
            'SELECT i.*
             FROM menu AS i
             WHERE i.id = ? AND i.language = ?',
            array((int) $id, FRONTEND_LANGUAGE)
        );

        // no results?
        if (empty($item)) {
            return array();
        }

        // create full url
        //$item['full_url'] = Navigation::getURLForBlock('Menu', 'detail') . '/' . $item['url'];

        return $item;
    }

    /**
     * Fetches à la carte items
     *
     * @return array
     */
    public static function getAllAlacarte()
    {
        $items = (array) FrontendModel::get('database')->getRecords(
            'SELECT c.id AS category_id, c.title AS category, i.id, i.title, i.description, i.price, CASE WHEN i.highlight = ? THEN 1 ELSE NULL END AS highlight
            FROM menu_categories AS c
            INNER JOIN menu_alacarte AS i ON i.category_id = c.id
            WHERE c.language = ? AND i.hidden = ?
            ORDER BY c.sequence ASC, i.sequence ASC',
            array('Y', FRONTEND_LANGUAGE, 'N')
        );

        // sort array on category name
        $arr = array();
        foreach ($items as $k => $sub_array) {
            $this_level = $sub_array['category'];
            $arr[$this_level]['title'] = $this_level;
            $arr[$this_level]['id'] = $sub_array['category_id'];
            unset($sub_array['category_id']);
            unset($sub_array['category']);
            $arr[$this_level]['items'][$k] = $sub_array;
        }

        // no results?
        if (empty($arr)) {
            return array();
        }

        return $arr;
    }

    /**
     * Fetches menus
     *
     * @return array
     */
    public static function getAllMenus()
    {
        $items = (array) FrontendModel::get('database')->getRecords(
            'SELECT i.*, m.url AS url
            FROM menu AS i
            INNER JOIN meta as m ON i.meta_id = m.id
            WHERE i.language = ? AND i.hidden = ?
            ORDER BY i.sequence ASC',
            array(FRONTEND_LANGUAGE, 'N')
        );

        // no results?
        if (empty($items)) {
            return array();
        }

        return $items;
    }

    /**
     * Get the number of items in a category
     *
     * @param int $categoryId
     * @return int
     */
    public static function getCategoryCount($categoryId)
    {
        return (int) FrontendModel::get('database')->getVar(
            'SELECT COUNT(i.id) AS count
             FROM menu AS i
             WHERE i.category_id = ?',
            array((int) $categoryId)
        );
    }
}
