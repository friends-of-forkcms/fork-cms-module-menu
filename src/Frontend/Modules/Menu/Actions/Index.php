<?php

namespace Frontend\Modules\Menu\Actions;

use Frontend\Core\Engine\Base\Block;
use Frontend\Core\Engine\Navigation;
use Frontend\Modules\Menu\Engine\Model as FrontendMenuModel;

/**
 * This is the index-action (default), it will display the overview of Menu posts
 *
 * @author Jesse Dobbelaere <jesse@dobbelaere-ae.be>
 */
class Index extends Block
{
    /**
     * The items
     *
     * @var    array
     */
    private $items;

    /**
     * The pagination array
     * It will hold all needed parameters, some of them need initialization.
     *
     * @var    array
     */
    protected $pagination = array(
        'limit' => 10,
        'offset' => 0,
        'requested_page' => 1,
        'num_items' => null,
        'num_pages' => null
    );

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->getData();
        $this->parse();
    }

    /**
     * Load the data, don't forget to validate the incoming data
     */
    private function getData()
    {
        // requested page
        $requestedPage = $this->URL->getParameter('page', 'int', 1);

        // set URL and limit
        $this->pagination['url'] = Navigation::getURLForBlock('Menu');
        $this->pagination['limit'] = $this->get('fork.settings')->get($this->URL->getModule(), 'overview_num_items', 10);

        // populate count fields in pagination
        $this->pagination['num_items'] = FrontendMenuModel::getAllCount();
        $this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

        // num pages is always equal to at least 1
        if ($this->pagination['num_pages'] == 0) {
            $this->pagination['num_pages'] = 1;
        }

        // redirect if the request page doesn't exist
        if ($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) {
            $this->redirect(Navigation::getURL(404));
        }

        // populate calculated fields in pagination
        $this->pagination['requested_page'] = $requestedPage;
        $this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

        // get articles
        $this->items = FrontendMenuModel::getAll($this->pagination['limit'], $this->pagination['offset']);
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        // assign items
        $this->tpl->assign('items', $this->items);

        // parse the pagination
        $this->parsePagination();
    }
}
