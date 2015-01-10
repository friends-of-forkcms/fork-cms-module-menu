<?php

namespace Backend\Modules\Menu\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Meta;
use Backend\Core\Engine\Model;
use Backend\Modules\Menu\Engine\Model as BackendMenuModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;
use Backend\Core\Engine\Language as BL;

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Jesse Dobbelaere <jesse@dobbelaere-ae.be>
 */
class Add extends ActionAdd
{
    /**
     * Execute the actions
     */
    public function execute()
    {
        parent::execute();

        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        $this->frm = new Form('add');

        $this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
        $this->frm->addEditor('description');
        $this->frm->addEditor('price');

        // build array with options for the hidden Radiobutton
        $RadiobuttonHiddenValues[] = array('label' => Language::lbl('Hidden'), 'value' => 'Y');
        $RadiobuttonHiddenValues[] = array('label' => Language::lbl('Published'), 'value' => 'N');
        $this->frm->addRadioButton('hidden', $RadiobuttonHiddenValues, 'N');

        // meta
        $this->meta = new Meta($this->frm, null, 'title', true);
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();

        // get url
        $url = Model::getURLForBlock($this->URL->getModule(), 'detail');
        $url404 = Model::getURL(404);

        // parse additional variables
        if ($url404 != $url) {
            $this->tpl->assign('detailURL', SITE_URL . $url);
        }
        $this->record['url'] = $this->meta->getURL();
    }

    /**
     * Validate the form
     */
    protected function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validation
            $fields = $this->frm->getFields();

            $fields['title']->isFilled(Language::err('FieldIsRequired'));
            $fields['description']->isFilled(Language::err('FieldIsRequired'));
            //$fields['price']->isFilled(Language::err('InvalidInteger'));

            // validate meta
            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                // build the item
                $item['language'] = Language::getWorkingLanguage();
                $item['title'] = $fields['title']->getValue();
                $item['description'] = $fields['description']->getValue();
                $item['price'] = $fields['price']->getValue();
                $item['hidden'] = $fields['hidden']->getValue();
                $item['sequence'] = BackendMenuModel::getMaximumSequence() + 1;

                $item['meta_id'] = $this->meta->save();

                // insert it
                $item['id'] = BackendMenuModel::insert($item);

                Model::triggerEvent(
                    $this->getModule(), 'after_add', $item
                );
                $this->redirect(
                    Model::createURLForAction('index') . '&report=added&highlight=row-' . $item['id']
                );
            }
        }
    }
}
