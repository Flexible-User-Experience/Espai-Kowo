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
            array('/admin/contacts/message/list'),
            array('/admin/contacts/message/1/show'),
            array('/admin/contacts/message/1/answer'),
            array('/admin/contacts/message/1/delete'),
            array('/admin/blog/tag/list'),
            array('/admin/blog/tag/create'),
            array('/admin/blog/tag/1/show'),
            array('/admin/blog/tag/1/edit'),
            array('/admin/blog/tag/1/delete'),
            array('/admin/blog/post/list'),
            array('/admin/blog/post/create'),
            array('/admin/blog/post/1/edit'),
            array('/admin/blog/post/1/delete'),
            array('/admin/activities/activity/list'),
            array('/admin/activities/activity/create'),
            array('/admin/activities/activity/1/delete'),
            array('/admin/activities/activity/1/edit'),
            array('/admin/activities/category/list'),
            array('/admin/activities/category/create'),
            array('/admin/activities/category/1/delete'),
            array('/admin/activities/category/1/edit'),
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
            array('/admin/coworkers/coworker/1/edit'),
            array('/admin/configurations/province/list'),
            array('/admin/configurations/province/create'),
            array('/admin/configurations/province/1/edit'),
            array('/admin/configurations/city/list'),
            array('/admin/configurations/city/create'),
            array('/admin/configurations/city/1/edit'),
            array('/admin/sales/customer/list'),
            array('/admin/sales/customer/create'),
            array('/admin/sales/customer/1/edit'),
            array('/admin/sales/invoice/list'),
            array('/admin/sales/invoice/create'),
            array('/admin/sales/invoice/1/edit'),
            array('/admin/sales/invoice-line/list'),
            array('/admin/sales/invoice-line/create'),
            array('/admin/sales/invoice-line/1/edit'),
            array('/admin/sales/invoice-line/1/delete'),
            array('/admin/purchases/spending-category/list'),
            array('/admin/purchases/spending-category/create'),
            array('/admin/purchases/spending-category/1/edit'),
            array('/admin/purchases/spending-category/1/delete'),
            array('/admin/purchases/provider/list'),
            array('/admin/purchases/provider/create'),
            array('/admin/purchases/provider/1/edit'),
            array('/admin/configurations/user/list'),
            array('/admin/configurations/user/create'),
            array('/admin/configurations/user/1/edit'),
            array('/admin/configurations/user/1/delete'),
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
            array('/admin/blog/tag/batch'),
            array('/admin/blog/post/batch'),
            array('/admin/contacts/message/create'),
            array('/admin/contacts/message/1/edit'),
            array('/admin/contacts/message/batch'),
            array('/admin/coworkers/category/batch'),
            array('/admin/coworkers/social-network-category/batch'),
            array('/admin/coworkers/social-network/batch'),
            array('/admin/coworkers/coworker/batch'),
            array('/admin/coworkers/coworker/1/delete'),
            array('/admin/configurations/province/batch'),
            array('/admin/configurations/province/1/show'),
            array('/admin/configurations/province/1/delete'),
            array('/admin/configurations/city/batch'),
            array('/admin/configurations/city/1/show'),
            array('/admin/configurations/city/1/delete'),
            array('/admin/sales/customer/batch'),
            array('/admin/sales/customer/1/show'),
            array('/admin/sales/customer/1/delete'),
            array('/admin/sales/invoice/batch'),
            array('/admin/sales/invoice/1/show'),
            array('/admin/sales/invoice/1/delete'),
            array('/admin/sales/invoice-line/batch'),
            array('/admin/sales/invoice-line/1/show'),
            array('/admin/purchases/spending-category/batch'),
            array('/admin/purchases/spending-category/1/show'),
            array('/admin/purchases/provider/batch'),
            array('/admin/purchases/provider/1/show'),
            array('/admin/purchases/provider/1/delete'),
            array('/admin/activities/activity/batch'),
            array('/admin/activities/category/batch'),
            array('/admin/configurations/user/show'),
            array('/admin/configurations/user/batch'),
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
            array('/admin/activities/category/1/show'),
            array('/admin/blog/post/1/show'),
        );
    }
}
