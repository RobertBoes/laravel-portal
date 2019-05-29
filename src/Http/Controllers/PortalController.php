<?php

namespace RobertBoes\LaravelPortal\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use RobertBoes\LaravelPortal\Exceptions\PortalActionNotDefined;

class PortalController extends BaseController
{
    public function fallback()
    {
        throw new PortalActionNotDefined('Guest or auth portal action was not defined.');
    }
}
