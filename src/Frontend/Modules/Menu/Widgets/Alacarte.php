<?php

namespace Frontend\Modules\Menu\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget;
use Frontend\Core\Engine\Navigation;
use Frontend\Modules\Menu\Engine\Model as FrontendMenuModel;

/**
 * This is a widget with the A la carte menu items
 *
 * @author Jesse Dobbelaere <jesse@dobbelaere-ae.be>
 */
class Alacarte extends Widget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {
        // get categories
        $alacarte = FrontendMenuModel::getAllAlacarte();

        foreach ($alacarte as $kc =>$category) {
            foreach ($category['items'] as $ki => $items) {
                if ((double)$items['price'] == 0) {
                    $alacarte[$kc]['items'][$ki]['price'] = false;
                }
            }
        }

        // assign comments
        $this->tpl->assign('widgetMenuAlacarte', $alacarte);
    }
}
