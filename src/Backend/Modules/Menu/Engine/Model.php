<?php

namespace Backend\Modules\Menu\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Language as BL;

/**
 * In this file we store all generic functions that we will be using in the Menu module
 *
 * @author Jesse Dobbelaere <jesse@dobbelaere-ae.be>
 */
class Model
{
    const QRY_DATAGRID_BROWSE =
        'SELECT i.id, i.hidden, i.title, UNIX_TIMESTAMP(i.created_on) AS created_on, i.sequence
         FROM menu AS i
         WHERE i.language = ?
         ORDER BY i.sequence';

    const QRY_DATAGRID_BROWSE_CATEGORIES =
        'SELECT c.id, c.title, COUNT(i.id) AS num_items, c.sequence
         FROM menu_categories AS c
         LEFT OUTER JOIN menu_alacarte AS i ON c.id = i.category_id AND i.language = c.language
         WHERE c.language = ?
         GROUP BY c.id
         ORDER BY c.sequence ASC';

    const QRY_DATAGRID_BROWSE_ALACARTE =
        'SELECT i.id, i.hidden, i.title, UNIX_TIMESTAMP(i.created_on) AS created_on, i.sequence, i.price
         FROM menu_alacarte AS i
         WHERE i.language = ? AND category_id = ?
         ORDER BY i.sequence';

    /**
     * Delete a certain item
     *
     * @param int $id
     */
    public static function delete($id)
    {
        $db = BackendModel::get('database');
        $item = self::get($id);

        if (!empty($item)) {
            $db->delete('meta', 'id = ?', array($item['meta_id']));
            $db->delete('menu', 'id = ?', array((int) $id));
            BackendModel::deleteExtraById($item['extra_id']);
            BackendModel::invalidateFrontendCache('Menu', BL::getWorkingLanguage());
        }
    }

    /**
     * Delete a certain item
     *
     * @param int $id
     */
    public static function deleteAlacarte($id)
    {
        BackendModel::get('database')->delete('menu_alacarte', 'id = ?', (int) $id);
    }

    /**
     * Delete a specific category
     *
     * @param int $id
     */
    public static function deleteCategory($id)
    {
        $db = BackendModel::get('database');
        $item = self::getCategory($id);

        if (!empty($item)) {
            $db->delete('meta', 'id = ?', array($item['meta_id']));
            $db->delete('menu_categories', 'id = ?', array((int) $id));
            $db->update('menu', array('category_id' => null), 'category_id = ?', array((int) $id));
        }
    }

    /**
     * Checks if a certain item exists
     *
     * @param int $id
     * @return bool
     */
    public static function exists($id)
    {
        return (bool) BackendModel::get('database')->getVar(
            'SELECT 1
             FROM menu AS i
             WHERE i.id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    /**
     * Checks if a certain alacarte item exists
     *
     * @param int $id
     * @return bool
     */
    public static function existsAlacarte($id)
    {
        return (bool) BackendModel::get('database')->getVar(
            'SELECT 1
             FROM menu_alacarte AS i
             WHERE i.id = ?
             LIMIT 1',
            array((int) $id)
        );
    }

    /**
     * Does the category exist?
     *
     * @param int $id
     * @return bool
     */
    public static function existsCategory($id)
    {
        return (bool) BackendModel::get('database')->getVar(
            'SELECT 1
             FROM menu_categories AS i
             WHERE i.id = ? AND i.language = ?
             LIMIT 1',
            array((int) $id, Language::getWorkingLanguage()));
    }

    /**
     * Fetches a certain item
     *
     * @param int $id
     * @return array
     */
    public static function get($id)
    {
        return (array) BackendModel::get('database')->getRecord(
            'SELECT i.*
             FROM menu AS i
             WHERE i.id = ?',
            array((int) $id)
        );
    }

    /**
     * Fetches a certain item
     *
     * @param int $id
     * @return array
     */
    public static function getAlacarte($id)
    {
        return (array) BackendModel::get('database')->getRecord(
            'SELECT i.*
             FROM menu_alacarte AS i
             WHERE i.id = ?',
            array((int) $id)
        );
    }

    /**
     * Get all the categories
     *
     * @param bool[optional] $includeCount
     * @return array
     */
    public static function getCategories($includeCount = false)
    {
        $db = BackendModel::get('database');

        if ($includeCount) {
            return (array) $db->getPairs(
                'SELECT i.id, CONCAT(i.title, " (",  COUNT(p.category_id) ,")") AS title
                 FROM menu_categories AS i
                 LEFT OUTER JOIN menu AS p ON i.id = p.category_id AND i.language = p.language
                 WHERE i.language = ?
                 GROUP BY i.id',
                 array(Language::getWorkingLanguage()));
        }

        return (array) $db->getPairs(
            'SELECT i.id, i.title
             FROM menu_categories AS i
             WHERE i.language = ?',
             array(Language::getWorkingLanguage()));
    }

    /**
     * Fetch a category
     *
     * @param int $id
     * @return array
     */
    public static function getCategory($id)
    {
        return (array) BackendModel::get('database')->getRecord(
            'SELECT i.*
             FROM menu_categories AS i
             WHERE i.id = ? AND i.language = ?',
             array((int) $id, Language::getWorkingLanguage()));
    }

    /**
     * Get the maximum sequence for a category
     *
     * @return int
     */
    public static function getMaximumCategorySequence()
    {
        return (int) BackendModel::get('database')->getVar(
            'SELECT MAX(i.sequence)
             FROM menu_categories AS i
             WHERE i.language = ?',
             array(Language::getWorkingLanguage()));
    }

    /**
     * Get the maximum Menu sequence.
     *
     * @return int
     */
    public static function getMaximumSequence()
    {
        return (int) BackendModel::get('database')->getVar(
            'SELECT MAX(i.sequence)
             FROM menu AS i'
        );
    }

    /**
     * Get the maximum Menu sequence.
     *
     * @return int
     */
    public static function getMaximumAlacarteSequence()
    {
        return (int) BackendModel::get('database')->getVar(
            'SELECT MAX(i.sequence)
             FROM menu_alacarte AS i'
        );
    }

    /**
     * Retrieve the unique URL for an item
     *
     * @param string $url
     * @param int[optional] $id    The id of the item to ignore.
     * @return string
     */
    public static function getURL($url, $id = null)
    {
        $url = \SpoonFilter::urlise((string) $url);
        $db = BackendModel::get('database');

        // new item
        if ($id === null) {
            // already exists
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM menu AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ?
                 LIMIT 1',
                array(Language::getWorkingLanguage(), $url))) {
                $url = BackendModel::addNumber($url);
                return self::getURL($url);
            }
        } else {
            // current item should be excluded
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM menu AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ? AND i.id != ?
                 LIMIT 1',
                array(Language::getWorkingLanguage(), $url, $id))) {
                $url = BackendModel::addNumber($url);
                return self::getURL($url, $id);
            }
        }

        return $url;
    }

    /**
     * Retrieve the unique URL for a category
     *
     * @param string $url
     * @param int[optional] $id The id of the category to ignore.
     * @return string
     */
    public static function getURLForCategory($url, $id = null)
    {
        $url = \SpoonFilter::urlise((string) $url);
        $db = BackendModel::get('database');

        // new category
        if ($id === null) {
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM menu_categories AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ?
                 LIMIT 1',
                array(Language::getWorkingLanguage(), $url))) {
                $url = BackendModel::addNumber($url);
                return self::getURLForCategory($url);
            }
        }
        // current category should be excluded
        else {
            if ((bool) $db->getVar(
                'SELECT 1
                 FROM menu_categories AS i
                 INNER JOIN meta AS m ON i.meta_id = m.id
                 WHERE i.language = ? AND m.url = ? AND i.id != ?
                 LIMIT 1',
                array(Language::getWorkingLanguage(), $url, $id))) {
                $url = BackendModel::addNumber($url);
                return self::getURLForCategory($url, $id);
            }
        }

        return $url;
    }

    /**
     * Insert an item in the database
     *
     * @param array $item
     * @return int
     */
    public static function insert(array $item)
    {
        $item['created_on'] = BackendModel::getUTCDate();
        $item['edited_on'] = BackendModel::getUTCDate();
        $db = BackendModel::get('database');

        // insert extra
        $item['extra_id'] = BackendModel::insertExtra(
            'widget',
            'Menu',
            'MenuList'
        );

        $item['id'] = $db->insert('menu', $item);

        // update extra (item id is now known)
        BackendModel::updateExtra(
            $item['extra_id'],
            'data',
            array(
                'id' => $item['id'],
                'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Menu', 'Menu')) . ': ' . $item['title'],
                'language' => $item['language'],
                'edit_url' => BackendModel::createURLForAction(
                        'Edit',
                        'Menu',
                        $item['language']
                    ) . '&id=' . $item['id']
            )
        );

        BackendModel::invalidateFrontendCache('Menu', BL::getWorkingLanguage());

        return $item['id'];
    }

    /**
     * Insert an item in the database
     *
     * @param array $item
     * @return int
     */
    public static function insertAlacarte(array $item)
    {
        $item['created_on'] = BackendModel::getUTCDate();
        $item['edited_on'] = BackendModel::getUTCDate();

        return (int)BackendModel::get('database')->insert('menu_alacarte', $item);
    }


        /**
     * Insert a category in the database
     *
     * @param array $item
     * @return int
     */
    public static function insertCategory(array $item)
    {
        $item['created_on'] = BackendModel::getUTCDate();
        $item['edited_on'] = BackendModel::getUTCDate();

        return BackendModel::get('database')->insert('menu_categories', $item);
    }

    /**
     * Updates an item
     *
     * @param array $item
     */
    public static function update(array $item)
    {
        $item['edited_on'] = BackendModel::getUTCDate();

        BackendModel::get('database')->update(
            'menu', $item, 'id = ?', (int) $item['id']
        );

        // update extra
        BackendModel::updateExtra(
            $item['extra_id'],
            'data',
            array(
                'id' => $item['id'],
                'extra_label' => \SpoonFilter::ucfirst(BL::lbl('Menu', 'Menu')) . ': ' . $item['title'],
                'language' => $item['language'],
                'edit_url' => BackendModel::createURLForAction('Edit') . '&id=' . $item['id']
            )
        );

        // invalidate menu
        BackendModel::invalidateFrontendCache('Menu', BL::getWorkingLanguage());
    }

    /**
     * Updates an item
     *
     * @param array $item
     */
    public static function updateAlacarte(array $item)
    {
        $item['edited_on'] = BackendModel::getUTCDate();

        BackendModel::get('database')->update(
            'menu_alacarte', $item, 'id = ?', (int) $item['id']
        );
    }

    /**
     * Update a certain category
     *
     * @param array $item
     */
    public static function updateCategory(array $item)
    {
        $item['edited_on'] = BackendModel::getUTCDate();

        BackendModel::get('database')->update(
            'menu_categories', $item, 'id = ?', array($item['id'])
        );
    }
}
