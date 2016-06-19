<?php

namespace Backend\Modules\Menu\Installer;

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the Menu module
 *
 * @author Jesse Dobbelaere <jesse@dobbelaere-ae.be>
 */
class Installer extends ModuleInstaller
{
    /**
     * @var	int
     */
    private $defaultCategoryId;

    public function install()
    {
        // import the sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // install the module in the database
        $this->addModule('Menu');

        // install the locale, this is set here beceause we need the module for this
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->setModuleRights(1, 'Menu');

        $this->setActionRights(1, 'Menu', 'Index');
        $this->setActionRights(1, 'Menu', 'Add');
        $this->setActionRights(1, 'Menu', 'Edit');
        $this->setActionRights(1, 'Menu', 'Delete');
        $this->setActionRights(1, 'Menu', 'Sequence');
        $this->setActionRights(1, 'Menu', 'Categories');
        $this->setActionRights(1, 'Menu', 'AddCategory');
        $this->setActionRights(1, 'Menu', 'EditCategory');
        $this->setActionRights(1, 'Menu', 'DeleteCategory');
        $this->setActionRights(1, 'Menu', 'SequenceCategories');
        $this->setActionRights(1, 'Menu', 'Alacarte');
        $this->setActionRights(1, 'Menu', 'AddAlacarte');
        $this->setActionRights(1, 'Menu', 'EditAlacarte');
        $this->setActionRights(1, 'Menu', 'DeleteAlacarte');

        $this->insertExtra('Menu', 'widget', 'Alacarte', 'Alacarte', null, 'N', 1002);
        $this->insertExtra('Menu', 'widget', 'Menus', 'Menus', null, 'N', 1003);

        // add extra's
        $subnameID = $this->insertExtra('Menu', 'block', 'Menu', null, null, 'N', 1000);

        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationMenuId = $this->setNavigation($navigationModulesId, 'Menu');
        $this->setNavigation(
            $navigationMenuId, 'Alacarte', 'menu/alacarte',
            array('menu/add_alacarte', 'menu/edit_alacarte')
        );
        $this->setNavigation(
            $navigationMenuId, 'Menu', 'menu/index',
            array('menu/add', 'menu/edit')
        );
        $this->setNavigation(
            $navigationMenuId, 'Categories', 'menu/categories',
            array('menu/add_category', 'menu/edit_category')
        );

        // add categories
        foreach ($this->getLanguages() as $language) {
            $this->defaultCategoryId = $this->getCategory($language);

            // no category exists
            if ($this->defaultCategoryId == 0) {
                $this->defaultCategoryId = $this->addCategory($language, ucfirst($this->getLocale('Starters', 'Menu', $language, 'lbl', 'Backend')));
                $this->defaultCategoryId = $this->addCategory($language, ucfirst($this->getLocale('MainCourses', 'Menu', $language, 'lbl', 'Backend')));
                $this->defaultCategoryId = $this->addCategory($language, ucfirst($this->getLocale('Desserts', 'Menu', $language, 'lbl', 'Backend')));
            }
        }
    }

    /**
     * Fetch the id of the first category in this language we come across
     *
     * @param string $language
     * @return int
     */
    private function getCategory($language)
    {
        return (int) $this->getDB()->getVar(
            'SELECT id
             FROM menu_categories
             WHERE language = ?',
            array((string) $language)
        );
    }

    /**
     * Add a category for a language
     *
     * @param string $language
     * @param string $title
     * @return int
     */
    private function addCategory($language, $title)
    {
        // db
        $db = $this->getDB();

        // build array
        $item['meta_id'] = $this->insertMeta($title, $title, $title, urlencode($title));
        $item['language'] = (string)$language;
        $item['title'] = (string)$title;
        $item['sequence'] = 1;

        // insert category
        $item['id'] = (int) $db->insert('menu_categories', $item);

        return $item['id'];
    }
}
