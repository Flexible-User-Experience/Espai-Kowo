<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class FrontendControllerTest
 *
 * @category Test
 * @package  AppBundle\Tests\Controller
 * @author   David Romaní <david@flux.cat>
 */
class FrontendControllerTest extends WebTestCase
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
        $client->request('GET', '/activitats');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $client->request('GET', '/activitat/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $client->request('GET', '/blog');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        $client->request('GET', '/blog/2016/04/10/my-title');
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
