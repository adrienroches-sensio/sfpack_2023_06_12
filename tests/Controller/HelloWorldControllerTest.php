<?php

namespace App\Tests\Controller;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HelloWorldControllerTest extends WebTestCase
{
    public static function provideValidURLs(): Generator
    {
        yield 'default' => [
            'uri' => '/hello',
        ];

        yield 'with name "Adrien"' => [
            'uri' => '/hello/Adrien',
        ];

        yield 'with name "Adrien-Louise"' => [
            'uri' => '/hello/Adrien-Louise',
        ];
    }

    /**
     * @group type/smoke
     *
     * @dataProvider provideValidURLs
     */
    public function testItWorks(string $uri): void
    {
        $client = static::createClient();
        $client->request('GET', $uri);

        $this->assertResponseIsSuccessful();
    }
}
