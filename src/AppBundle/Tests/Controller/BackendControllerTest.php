<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\AbstractBaseTest;

/**
 * Class BackendControllerTest
 *
 * @category Test
 * @package  AppBundle\Tests\Controller
 * @author   David Romaní <david@flux.cat>
 */
class BackendControllerTest extends AbstractBaseTest
{
    /**
     * Test admin login request is successful
     */
    public function testAdminLoginPageIsSuccessful()
    {
        $client = $this->createClient();           // anonymous user
        $client->request('GET', '/admin/login');

        $this->assertStatusCode(200, $client);
    }

    /**
     * Test HTTP request is successful
     *
     * @dataProvider provideSuccessfulUrls
     * @param string $url
     */
    public function testAdminPagesAreSuccessful($url)
    {
        $client = $this->makeClient(true);         // authenticated user
        $client->request('GET', $url);

        $this->assertStatusCode(200, $client);
    }

    /**
     * Successful Urls provider
     *
     * @return array
     */
    public function provideSuccessfulUrls()
    {
        return array(
            array('/admin/dashboard'),
            array('/admin/contact/message/list'),
            array('/admin/contact/message/1/show'),
            array('/admin/contact/message/1/answer'),
            array('/admin/contact/message/1/delete'),
            array('/admin/web/tag/list'),
            array('/admin/web/tag/create'),
            array('/admin/web/tag/1/show'),
            array('/admin/web/tag/1/edit'),
            array('/admin/web/tag/1/delete'),
            array('/admin/web/post/list'),
            array('/admin/web/post/create'),
            array('/admin/web/post/1/edit'),
            array('/admin/web/post/1/delete'),
            array('/admin/coworkers/category/list'),
            array('/admin/coworkers/category/create'),
            array('/admin/coworkers/category/1/delete'),
            array('/admin/coworkers/category/1/edit'),
            array('/admin/coworkers/social-network-category/list'),
            array('/admin/coworkers/social-network-category/create'),
            array('/admin/coworkers/social-network-category/1/delete'),
            array('/admin/coworkers/social-network-category/1/edit'),
            array('/admin/coworkers/social-network/list'),
            array('/admin/coworkers/social-network/create'),
            array('/admin/coworkers/social-network/1/delete'),
            array('/admin/coworkers/social-network/1/edit'),
            array('/admin/coworkers/coworker/list'),
            array('/admin/coworkers/coworker/create'),
            array('/admin/coworkers/coworker/1/delete'),
            array('/admin/coworkers/coworker/1/edit'),
            array('/admin/users/list'),
            array('/admin/users/create'),
            array('/admin/users/1/edit'),
            array('/admin/users/1/delete'),
        );
    }

    /**
     * Test HTTP request is not found
     *
     * @dataProvider provideNotFoundUrls
     * @param string $url
     */
    public function testAdminPagesAreNotFound($url)
    {
        $client = $this->makeClient(true);         // authenticated user
        $client->request('GET', $url);

        $this->assertStatusCode(404, $client);
    }

    /**
     * Not found Urls provider
     *
     * @return array
     */
    public function provideNotFoundUrls()
    {
        return array(
            array('/admin/contact/message/create'),
            array('/admin/contact/message/1/edit'),
            array('/admin/contact/message/batch'),
            array('/admin/coworkers/category/batch'),
            array('/admin/coworkers/social-network-category/batch'),
            array('/admin/coworkers/social-network/batch'),
            array('/admin/coworkers/coworker/batch'),
            array('/admin/users/show'),
            array('/admin/users/batch'),
        );
    }

    /**
     * Test HTTP request is redirection
     *
     * @dataProvider provideRedirectionUrls
     * @param string $url
     */
    public function testAdminPagesAreRedirection($url)
    {
        $client = $this->makeClient(true);         // authenticated user
        $client->request('GET', $url);

        $this->assertStatusCode(302, $client);
    }

    /**
     * Not found Urls provider
     *
     * @return array
     */
    public function provideRedirectionUrls()
    {
        return array(
            array('/admin/coworkers/coworker/1/show'),
            array('/admin/activitats/activitat/1/show'),
            array('/admin/web/post/1/show'),
        );
    }
}
