<?php

namespace App\Controller\Services;


class methodDataBase
{
    public static function push($doctrine, $entity)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();
    }
    public static function delete($doctrine, $entity)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($entity);
        $entityManager->flush();
    }
}
