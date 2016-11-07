<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Form;

/**
 * Class FormsSubmitTest
 *
 * @category Test
 * @package  AppBundle\Tests\Controller
 * @author   David Romaní <david@flux.cat>
 */
class FormsSubmitTest extends WebTestCase
{
    /**
     * Test Forms Submit
     */
    public function testFormsSubmit()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $sendButton = $crawler->selectButton('Enviar');
        /** @var Form $form */
        $form = $sendButton->form();
        $contactHomepage = $form->get('contact_homepage');

        $this->assertEquals(count($contactHomepage), 5);
        $this->assertTrue(isset($contactHomepage['name']));
        $this->assertTrue(isset($contactHomepage['phone']));
        $this->assertTrue(isset($contactHomepage['email']));
        $this->assertTrue(isset($contactHomepage['send']));

        $form->setValues(array(
            'contact_homepage[name]' => 'myName',
            'contact_homepage[phone]' => 'myPhone',
            'contact_homepage[email]' => 'my@slkddfj.sfd',
        ));
        $crawler = $client->submit($form);
        $this->assertEquals($crawler->filter('html:contains("Aquest valor no és una adreça d\'email vàlida.")')->count(), 1);

        $crawler = $client->request('GET', '/');
        $sendButton = $crawler->selectButton('Enviar');
        /** @var Form $form */
        $form = $sendButton->form();
        $form->setValues(array(
            'contact_homepage[name]' => '',
            'contact_homepage[phone]' => '',
            'contact_homepage[email]' => '',
        ));
        $crawler = $client->submit($form);
        $this->assertEquals($crawler->filter('html:contains("Aquest valor no hauria d\'estar buit.")')->count(), 1);

        $crawler = $client->request('GET', '/');
        $sendButton = $crawler->selectButton('Enviar');
        /** @var Form $form */
        $form = $sendButton->form();
        $form->setValues(array(
            'contact_homepage[name]' => 'myName',
            'contact_homepage[phone]' => 'myPhone',
            'contact_homepage[email]' => $this->getContainer()->getParameter('mailer_destination'),
        ));
        $crawler = $client->submit($form);
        var_dump($crawler);
        $this->assertEquals($crawler->filter('i.fa-check')->count(), 1);
    }
}
