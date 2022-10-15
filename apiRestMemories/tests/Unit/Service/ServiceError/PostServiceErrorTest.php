<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\ServiceError\PostServiceError;



class PostServiceErrorTest extends TestCase
{

    public function testNoError()
    {
        $request = new Request([], ["test1" => "coucou"]);
        $request->setMethod("POST");
        $PostService = new PostServiceError();
        $test1 = $PostService->postError($request, ['test1']);
        $test2 = $PostService->postErrorToString($request, ['test1']);
        $this->assertEmpty($test1);
        $this->assertSame("", $test2);
    }
    public function testNoPost()
    {
        $request = new Request();
        $PostService = new PostServiceError();
        $test1 = $PostService->postError($request, ['test1']);
        $test2 = $PostService->postErrorToString($request, ['test1']);

        $return = [
            "error" => true,
            "POST" => "aucune information envoyé"
        ];
        $returnToString = json_encode($return);

        $this->assertSame($return, $test1);
        $this->assertSame($returnToString, $test2);
    }

    public function testPostNoComplite()
    {
        $request = new Request([], ["test1" => "coucou"]);
        $request->setMethod("POST");
        $PostService = new PostServiceError();
        $test1 = $PostService->postError($request, ['test1', "test2"]);
        $test2 = $PostService->postErrorToString($request, ['test1', "test2"]);

        $return = [
            "error" => true,
            "POST" => "il manque des informations dans le post :test2"
        ];
        $returnToString = json_encode($return);

        $this->assertSame($return, $test1);
        $this->assertSame($returnToString, $test2);
    }
}
