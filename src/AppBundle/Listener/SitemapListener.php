<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Coworker;
use AppBundle\Entity\Post;
use AppBundle\Entity\Event;
use AppBundle\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SitemapListener
 *
 * @category Listener
 * @package  Acme\DemoBundle\EventListener
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class SitemapListener implements SitemapListenerInterface
{
    /** @var RouterInterface */
    private $router;

    /** @var EntityManager */
    private $em;

    /** @var ArrayCollection */
    private $coworkers;

    /** @var ArrayCollection */
    private $posts;

//    /** @var EventRepository */
//    private $er;

//    /** @var array */
//    private $locales;

    /**
     * SitemapListener constructor
     *
     * @param RouterInterface $router
     * @param EntityManager   $em
     * @param EventRepository $er
     * @param array           $locales
     */
    public function __construct(RouterInterface $router, EntityManager $em)
    {
        $this->router = $router;
        $this->em = $em;
        $this->coworkers = $this->em->getRepository('AppBundle:Coworker')->findAllEnabledSortedByPosition();
        $this->posts = $this->em->getRepository('AppBundle:Post')->getAllEnabledSortedByPublishedDateWithJoin();
//        $this->er = $er;
//        $this->locales = $locales;
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $section = $event->getSection();
        if (is_null($section) || $section == 'default') {
//            /** @var string $locale */
//            foreach ($this->locales as $locale) {
            // Homepage
            $event
                ->getUrlContainer()
                ->addUrl(
                    new UrlConcrete(
                        $this->router->generate('front_homepage', array(), UrlGeneratorInterface::ABSOLUTE_URL),
                        new \DateTime(),
                        UrlConcrete::CHANGEFREQ_HOURLY,
                        1
                    ),
                    'default'
                );
            // Coworker main view
            $url = $this->router->generate(
                'front_coworkers_list', array(), UrlGeneratorInterface::ABSOLUTE_URL
            );
            $event
                ->getUrlContainer()
                ->addUrl(
                    new UrlConcrete(
                        $url,
                        new \DateTime(),
                        UrlConcrete::CHANGEFREQ_HOURLY,
                        1
                    ),
                    'default'
                );
            // Coworkers detail view list
            /** @var Coworker $coworker */
            foreach ($this->coworkers as $coworker) {
                $url = $this->router->generate(
                    'front_coworker_detail',
                    array( 'slug' => $coworker->getSlug(),
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                $event
                    ->getUrlContainer()
                    ->addUrl(
                        new UrlConcrete(
                            $url,
                            new \DateTime(),
                            UrlConcrete::CHANGEFREQ_HOURLY,
                            1
                        ),
                        'default'
                    );
            }
            // Blog main view
            $url = $this->router->generate(
                'front_blog', array(), UrlGeneratorInterface::ABSOLUTE_URL
            );
            $event
                ->getUrlContainer()
                ->addUrl(
                    new UrlConcrete(
                        $url,
                        new \DateTime(),
                        UrlConcrete::CHANGEFREQ_HOURLY,
                        1
                    ),
                    'default'
                );
            // Posts detail view list
            /** @var Post $post */
            foreach ($this->posts as $post) {
                $url = $this->router->generate(
                    'front_blog_detail',
                    array(
                        'year' => $post->getPublishedAt()->format('Y'),
                        'month' => $post->getPublishedAt()->format('m'),
                        'day' => $post->getPublishedAt()->format('d'),
                        'slug' => $post->getSlug(),
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                $event
                    ->getUrlContainer()
                    ->addUrl(
                        new UrlConcrete(
                            $url,
                            new \DateTime(),
                            UrlConcrete::CHANGEFREQ_HOURLY,
                            1
                        ),
                        'default'
                    );
//                // Events view
//                $event
//                    ->getUrlContainer()
//                    ->addUrl(
//                        new UrlConcrete(
//                            $this->router->generate('front_events_list', array( '_locale' => $locale ), UrlGeneratorInterface::ABSOLUTE_URL),
//                            new \DateTime(),
//                            UrlConcrete::CHANGEFREQ_HOURLY,
//                            1
//                        ),
//                        'default'
//                    );
//                /** @var Event $event */
//                foreach ($this->er->findAllEnabledSortedByDate() as $event) {
//                    $event
//                        ->getUrlContainer()
//                        ->addUrl(
//                            new UrlConcrete(
//                                $this->router->generate('front_event_detail', array( '_locale' => $locale, 'slug' => $event->getSlug()), UrlGeneratorInterface::ABSOLUTE_URL),
//                                new \DateTime(),
//                                UrlConcrete::CHANGEFREQ_HOURLY,
//                                1
//                            ),
//                            'default'
//                        );
            }
        }
    }
}
