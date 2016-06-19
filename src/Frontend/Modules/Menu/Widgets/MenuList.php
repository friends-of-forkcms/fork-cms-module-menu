<?php

namespace Frontend\Modules\Menu\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Menu\Engine\Model as FrontendMenuModel;

/**
 * This is a widget containing a menu
 *
 * @author Jesse Dobbelaere <jesse@dobbelaere-ae.be
 */
class MenuList extends FrontendBaseWidget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        // call parent
        parent::execute();

        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {
        $this->tpl->assign('widgetMenu', FrontendMenuModel::getMenu($this->data['id']));
    }
}
