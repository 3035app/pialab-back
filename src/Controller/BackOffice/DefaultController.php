<?php

/*
 * Copyright (C) 2015-2018 Libre Informatique
 *
 * This file is licensed under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace PiaApi\Controller\BackOffice;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/home", name="backend_home")
     *
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $roles = $this->getUser()->getRoles();

        if (in_array('ROLE_SHARED_DPO', $roles)) {
            return  $this->redirectToRoute('manage_portfolios');
        }
    }
}
