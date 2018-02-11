<?php

namespace eCamp\Web\Controller;

use eCamp\Lib\Auth\AuthRequiredException;
use Zend\View\Model\ViewModel;

class CampController extends AbstractBaseController
{

    /**
     * @return array|ViewModel
     * @throws AuthRequiredException
     */
    public function indexAction() {
        $this->forceLogin();

        return [
            'camp' => $this->params()->fromRoute('camp'),
        ];
    }

}
