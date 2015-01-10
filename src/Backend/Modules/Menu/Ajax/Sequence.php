<?php

namespace Backend\Modules\Menu\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\Menu\Engine\Model as BackendMenuModel;

/**
 * Alters the sequence of Menu articles
 *
 * @author Jesse Dobbelaere <jesse@dobbelaere-ae.be>
 */
class Sequence extends AjaxAction
{
    public function execute()
    {
        parent::execute();

        // get parameters
        $newIdSequence = trim(\SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

        // list id
        $ids = (array) explode(',', rtrim($newIdSequence, ','));

        // loop id's and set new sequence
        foreach ($ids as $i => $id) {
            $item['id'] = $id;
            $item['sequence'] = $i + 1;

            // update sequence
            if (BackendMenuModel::exists($id)) {
                BackendMenuModel::update($item);
            }
        }

        // success output
        $this->output(self::OK, null, 'sequence updated');
    }
}
