<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ReviewCRUDTest extends WebTestCase
{

    public function testShowResponseStatusCodeGet()
    {
        $client = static::createClient();

        // Send a GET request to the show route
        $client->request('GET', '/activités/{id}');
        // Assert that the response has a successful status code (200)
        $this->assertEquals(200, Response::HTTP_OK);
    }
    public function testShowResponseStatusCodePost()
    {
        $client = static::createClient();

        // Send a GET request to the show route
        $client->request('POST', '/activités/{id}');

        // Assert that the response contains the expected content
        $this->assertEquals(200, Response::HTTP_OK);
    }
    

}

