<?php

namespace Frontend\Modules\Menu\Actions;

use Frontend\Core\Engine\Base\Block;
use Frontend\Core\Engine\Navigation;
use Frontend\Modules\Menu\Engine\Model as FrontendMenuModel;

/**
 * This is the category-action, it will display the overview of Menu categories
 *
 * @author Jesse Dobbelaere <jesse@dobbelaere-ae.be>
 */
class Category extends Block
{
    /**
     * @var    array
     */
    private $items;

    /**
     * @var array
     */
    private $category;

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
        if ($this->URL->getParameter(0) === null) {
            $this->redirect(Navigation::getURL(404));
        }

        // get category
        $this->category = FrontendMenuModel::getCategory($this->URL->getParameter(0));
        if (empty($this->category)) {
            $this->redirect(Navigation::getURL(404));
        }

        // requested page
        $requestedPage = $this->URL->getParameter('page', 'int', 1);

        // set URL and limit
        $this->pagination['url'] = Navigation::getURLForBlock('Menu', 'category') . '/' . $this->category['url'];

        $this->pagination['limit'] = $this->get('fork.settings')->get($this->URL->getModule(), 'overview_num_items', 10);

        // populate count fields in pagination
        $this->pagination['num_items'] = FrontendMenuModel::getCategoryCount($this->category['id']);
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

        // get items
        $this->items = FrontendMenuModel::getAllByCategory(
            $this->category['id'], $this->pagination['limit'], $this->pagination['offset']
        );
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        // add into breadcrumb
        $this->breadcrumb->addElement($this->category['meta_title']);

        // hide action title
        $this->tpl->assign('hideContentTitle', true);

        // show the title
        $this->tpl->assign('title', $this->category['title']);

        // set meta
        $this->header->setPageTitle($this->category['meta_title'], ($this->category['meta_title_overwrite'] == 'Y'));
        $this->header->addMetaDescription($this->category['meta_description'], ($this->category['meta_description_overwrite'] == 'Y'));
        $this->header->addMetaKeywords($this->category['meta_keywords'], ($this->category['meta_keywords_overwrite'] == 'Y'));

        // advanced SEO-attributes
        if (isset($this->category['meta_data']['seo_index'])) {
            $this->header->addMetaData(
                array('name' => 'robots', 'content' => $this->category['meta_data']['seo_index'])
            );
        }
        if (isset($this->category['meta_data']['seo_follow'])) {
            $this->header->addMetaData(
                array('name' => 'robots', 'content' => $this->category['meta_data']['seo_follow'])
            );
        }

        // assign items
        $this->tpl->assign('items', $this->items);

        // parse the pagination
        $this->parsePagination();
    }
}
