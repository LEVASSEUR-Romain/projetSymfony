<?php

declare(strict_types=1);

namespace App\Controller\ServiceError;

use Symfony\Component\HttpFoundation\Request;

class PostServiceError
{

    public function postError(Request $request, $arrayNeed): array
    {
        if ($request->getMethod() !== 'POST') {
            $arrayErrors = [];
            $arrayErrors['error'] = "aucune information envoyé";
            return $arrayErrors;
        } else {
            $errors = "";
            foreach ($arrayNeed as &$value) {
                if (empty($request->get($value))) {
                    $errors .= $value;
                }
            }
            if ($errors !== "") {
                $arrayErrors['error'] = "il manque des informations dans le post :" . $errors;
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
