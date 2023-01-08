<?php

namespace App\Exceptions;

use Exception;

class CompanyProfileExistsException extends Exception
{
    CONST MESSAGE ='You are not allowed to create more than 1 company profile';
}
