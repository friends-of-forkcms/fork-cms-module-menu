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
 * This is the delete-action, it deletes an item
 *
 * @author Jesse Dobbelaere <jesse@dobbelaere-ae.be>
 */
class DeleteAlacarte extends ActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendMenuModel::existsAlacarte($this->id)) {
            parent::execute();
            $this->record = (array) BackendMenuModel::getAlacarte($this->id);

            BackendMenuModel::deleteAlacarte($this->id);

            Model::triggerEvent(
                $this->getModule(), 'after_delete',
                array('id' => $this->id)
            );

            $this->redirect(
                Model::createURLForAction('alacarte') . '&report=deleted&var=' .
                urlencode($this->record['title'])
            );
        } else {
            $this->redirect(Model::createURLForAction('index') . '&error=non-existing');
        }
    }
}
