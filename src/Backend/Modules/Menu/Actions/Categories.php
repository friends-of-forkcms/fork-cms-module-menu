<?php

namespace Backend\Modules\Menu\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\DataGridDB;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Menu\Engine\Model as BackendMenuModel;

/**
 * This is the categories-action, it will display the overview of categories
 *
 * @author Jesse Dobbelaere <jesse@dobbelaere-ae.be>
 */
class Categories extends ActionIndex
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadDataGrid();

        $this->parse();
        $this->display();
    }

    /**
     * Load the dataGrid
     */
    private function loadDataGrid()
    {
        $this->dataGrid = new DataGridDB(
            BackendMenuModel::QRY_DATAGRID_BROWSE_CATEGORIES,
            Language::getWorkingLanguage()
        );

        // check if this action is allowed
        if (Authentication::isAllowedAction('edit_category')) {
            $this->dataGrid->addColumn(
                'edit', null, Language::lbl('Edit'),
                Model::createURLForAction('edit_category') . '&amp;id=[id]',
                Language::lbl('Edit')
            );
        }

        // sequence
        $this->dataGrid->enableSequenceByDragAndDrop();
        $this->dataGrid->setAttributes(array('data-action' => 'sequence_categories'));
    }

    /**
     * Parse & display the page
     */
    protected function parse()
    {
        $this->tpl->assign('dataGrid', (string) $this->dataGrid->getContent());
    }
}
