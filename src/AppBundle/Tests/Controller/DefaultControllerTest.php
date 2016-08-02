<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $client->request('GET', '/coworkers');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        $client->request('GET', '/coworker/1');
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $client->request('GET', '/events');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $client->request('GET', '/event/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $client->request('GET', '/blog');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        $client->request('GET', '/blog/2016/04/10/my-title');
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
