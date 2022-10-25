<?php

namespace App\Controller\Services;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationService
{
    static public function toArray(ConstraintViolationListInterface $validator)
    {
        $return['error'] = true;
        foreach ($validator as &$value) {
            $return[$value->getpropertyPath()] = $value->getMessage();
        }
        return $return;
    }
}
