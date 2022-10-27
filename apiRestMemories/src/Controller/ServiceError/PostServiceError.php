<?php

declare(strict_types=1);

namespace App\Controller\ServiceError;

use Symfony\Component\HttpFoundation\Request;

class PostServiceError
{
    const ERROR_EMPTY_POST = "aucune information envoyé";
    const ERROR_ANY_EMPTY_POST = "il manque des informations dans le post : ";
    public function postError(Request $request, $arrayNeed): array
    {
        if ($request->getMethod() !== 'POST') {
            $arrayErrors = [];
            $arrayErrors['error'] = self::ERROR_EMPTY_POST;
            return $arrayErrors;
        } else {
            $errors = "";
            foreach ($arrayNeed as &$value) {
                if (empty($request->get($value))) {
                    $errors .= $value . " ";
                }
            }
            if ($errors !== "") {
                $arrayErrors['error'] = self::ERROR_ANY_EMPTY_POST . $errors;
                return $arrayErrors;
            }
        }
        return [];
    }

    public function postErrorToString(Request $request, $arrayNeed)
    {
        $arrayErrors = $this->postError($request, $arrayNeed);
        if (count($arrayErrors) === 0) return  "";
        return json_encode(
            $arrayErrors
        );
    }
}
