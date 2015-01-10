<?php

namespace Backend\Modules\Menu\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\Menu\Engine\Model as BackendMenuModel;

/**
 * This action will delete a category
 *
 * @author Jesse Dobbelaere <jesse@dobbelaere-ae.be>
 */
class DeleteCategory extends ActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id == null || !BackendMenuModel::existsCategory($this->id)) {
            $this->redirect(
                Model::createURLForAction('categories') . '&error=non-existing'
            );
        }

        // fetch the category
        $this->record = (array) BackendMenuModel::getCategory($this->id);

        // delete item
        BackendMenuModel::deleteCategory($this->id);
        Model::triggerEvent($this->getModule(), 'after_delete_category', array('item' => $this->record));

        // category was deleted, so redirect
        $this->redirect(
            Model::createURLForAction('categories') . '&report=deleted-category&var=' .
            urlencode($this->record['title'])
        );
    }
}
