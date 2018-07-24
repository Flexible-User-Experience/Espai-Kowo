<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\ContactMessage;
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
    public function testHomepageFormSubmit()
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

        $form->setValues(array(
            'contact_homepage[name]'  => 'myName',
            'contact_homepage[phone]' => '',
            'contact_homepage[email]' => '',
        ));
        $crawler = $client->submit($form);
        $this->assertEquals($crawler->filter('html:contains("Aquest valor no hauria d\'estar buit.")')->count(), 1);

        $form->setValues(array(
            'contact_homepage[name]'  => 'myName',
            'contact_homepage[phone]' => 'myPhone',
            'contact_homepage[email]' => $this->getContainer()->getParameter('mailer_destination'),
        ));
        $crawler = $client->submit($form);
        $this->assertEquals($crawler->filter('i.fa-check')->count(), 1);
    }

    /**
     * Test Contact Form Submit
     */
    public function testContactFormSubmit()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/contacte');
        $sendButton = $crawler->selectButton('Enviar');
        /** @var Form $form */
        $form = $sendButton->form();
        $contactHomepage = $form->get('contact_message');

        $this->assertEquals(count($contactHomepage), 6);
        $this->assertTrue(isset($contactHomepage['name']));
        $this->assertTrue(isset($contactHomepage['phone']));
        $this->assertTrue(isset($contactHomepage['email']));
        $this->assertTrue(isset($contactHomepage['message']));
        $this->assertTrue(isset($contactHomepage['send']));

        $form->setValues(array(
            'contact_message[name]' => 'myName',
            'contact_message[phone]' => 'myPhone',
            'contact_message[email]' => 'my@slkddfj.sfd',
            'contact_message[message]' => 'myMessage',
        ));
        $crawler = $client->submit($form);
        $this->assertEquals($crawler->filter('html:contains("Aquest valor no és una adreça d\'email vàlida.")')->count(), 1);

        $form->setValues(array(
            'contact_message[name]'  => 'myName',
            'contact_message[phone]' => 'myPhone',
            'contact_message[message]' => 'myMessage',
            'contact_message[email]' => $this->getContainer()->getParameter('mailer_destination'),
        ));
        $crawler = $client->submit($form);
        $this->assertEquals($crawler->filter('i.fa-check')->count(), 1);

        $cmr = $this->getContainer()->get('doctrine')->getRepository('AppBundle:ContactMessage');
        /** @var ContactMessage $contact */
        $contact = $cmr->findOneBy(array(
            'email' => $this->getContainer()->getParameter('mailer_destination')
        ));
        $this->assertEquals('myName', $contact->getName());
        $this->assertEquals('myPhone', $contact->getPhone());
        $this->assertEquals('myMessage', $contact->getMessage());
    }

    /**
     * Test Forms Submit
     *
     * (hard to test with Travis due to Mailchimp API private keys)
     */
//    public function testNewsletterFormSubmit()
//    {
//        $client = $this->createClient();
//        $crawler = $client->request('GET', '/activitats');
//        $sendButton = $crawler->selectButton('Subscriu-me al newsletter');
//        /** @var Form $form */
//        $form = $sendButton->form();
//        $contactHomepage = $form->get('contact_newsletter');
//
//        $this->assertEquals(count($contactHomepage), 4);
//        $this->assertTrue(isset($contactHomepage['name']));
//        $this->assertFalse(isset($contactHomepage['phone']));
//        $this->assertTrue(isset($contactHomepage['email']));
//        $this->assertFalse(isset($contactHomepage['message']));
//        $this->assertTrue(isset($contactHomepage['send']));
//
//        $form->setValues(array(
//            'contact_newsletter[name]' => 'myName',
//            'contact_newsletter[email]' => $this->getContainer()->getParameter('mailer_destination'),
//        ));
//        $crawler = $client->submit($form);
//        $this->assertEquals($crawler->filter('div.alert')->count(), 1);
//    }

    /**
     * Test Forms Submit
     *
     * (hard to test with Travis due to Mailchimp API private keys)
     */
//    public function testBlogNewsletterFormSubmit()
//    {
//        $client = $this->createClient();
//        $crawler = $client->request('GET', '/blog');
//        $sendButton = $crawler->selectButton('Subscriu-me al newsletter');
//        /** @var Form $form */
//        $form = $sendButton->form();
//        $contactHomepage = $form->get('contact_newsletter');
//
//        $this->assertEquals(count($contactHomepage), 4);
//        $this->assertTrue(isset($contactHomepage['name']));
//        $this->assertFalse(isset($contactHomepage['phone']));
//        $this->assertTrue(isset($contactHomepage['email']));
//        $this->assertFalse(isset($contactHomepage['message']));
//        $this->assertTrue(isset($contactHomepage['send']));
//
//        $form->setValues(array(
//            'contact_newsletter[name]' => 'myName',
//            'contact_newsletter[email]' => $this->getContainer()->getParameter('mailer_destination'),
//        ));
//        $crawler = $client->submit($form);
//        $this->assertEquals($crawler->filter('div.alert')->count(), 1);
//    }
}
