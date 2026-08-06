<?php

namespace App\Controllers\Sec;

use App\Controllers\PublicController;
use App\Utilities\Security;

class Logout extends PublicController
{
    protected function execute(): void
    {
        Security::logout();
        $this->redirect("Sec_Login");
    }
}
