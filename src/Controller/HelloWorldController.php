<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function ucfirst;

class HelloWorldController extends AbstractController
{
    #[Route(
        path: '/hello/{name}',
        name: 'app_hello_world',
        requirements: ['name' => '\w([-\w])*'],
        methods: ['GET'],
    )]
    public function index(string $name = 'Adrien'): Response
    {
        $name = ucfirst($name);

        return new Response(
            content: <<<"HTML"
            <body>Hello {$name} !</body>
            HTML
        );
    }
}
