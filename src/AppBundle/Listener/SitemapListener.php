<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Coworker;
use AppBundle\Entity\Post;
use AppBundle\Entity\Event;
use DateTime;
use DateTimeInterface;
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
 */
class SitemapListener implements SitemapListenerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var array
     */
    private $locales;

    /**
     * @var ArrayCollection
     */
    private $coworkers;

    /**
     * @var ArrayCollection
     */
    private $posts;

    /**
     * @var ArrayCollection
     */
    private $events;

    /**
     * Methods
     */

    /**
     * SitemapListener constructor
     *
     * @param RouterInterface $router
     * @param EntityManager   $em
     * @param array           $locales
     */
    public function __construct(RouterInterface $router, EntityManager $em, array $locales)
    {
        $this->router = $router;
        $this->coworkers = $em->getRepository('AppBundle:Coworker')->findAllEnabledSortedBySurname();
        $this->posts = $em->getRepository('AppBundle:Post')->getAllEnabledSortedByPublishedDateWithJoin();
        $this->events = $em->getRepository('AppBundle:Event')->findAllEnabledSortedByDate();
        $this->locales = $locales;
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $section = $event->getSection();
        if (is_null($section) || $section === 'default') {
            // By locale
            /** @var string $locale */
            foreach ($this->locales as $locale) {
                // Homepage
                $url = $this->makeUrl('front_homepage', ['_locale' => $locale]);
                $event
                    ->getUrlContainer()
                    ->addUrl($this->makeUrlConcrete($url), 'default')
                ;
                // Services
                $url = $this->makeUrl('front_services', ['_locale' => $locale]);
                $event
                    ->getUrlContainer()
                    ->addUrl($this->makeUrlConcrete($url), 'default')
                ;
                // Contact
                $url = $this->makeUrl('front_contact', ['_locale' => $locale]);
                $event
                    ->getUrlContainer()
                    ->addUrl($this->makeUrlConcrete($url, 0.5), 'default')
                ;
            }
            // Coworkers detail view list
            $lastUpdatedAtDate = \DateTime::createFromFormat('d-m-Y', '01-01-2000');
            /** @var Coworker $coworker */
            foreach ($this->coworkers as $coworker) {
                $url = $this->router->generate(
                    'front_coworker_detail',
                    array('slug' => $coworker->getSlug(),
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                $event
                    ->getUrlContainer()
                    ->addUrl($this->makeUrlConcrete($url, 0.8, $coworker->getUpdatedAt()), 'default');
                if ($coworker->getUpdatedAt() > $lastUpdatedAtDate) {
                    $lastUpdatedAtDate = $coworker->getUpdatedAt();
                }
            }
            // Coworker main view
            $url = $this->makeUrl('front_coworkers_list');
            $event
                ->getUrlContainer()
                ->addUrl($this->makeUrlConcrete($url, 1, $lastUpdatedAtDate), 'default');
            // Posts detail view list
            $lastUpdatedAtDate = \DateTime::createFromFormat('d-m-Y', '01-01-2000');
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
                    ->addUrl($this->makeUrlConcrete($url, 0.8, $post->getUpdatedAt()), 'default');
                if ($post->getUpdatedAt() > $lastUpdatedAtDate) {
                    $lastUpdatedAtDate = $post->getUpdatedAt();
                }
            }
            // Blog main view
            $url = $this->makeUrl('front_blog');
            $event
                ->getUrlContainer()
                ->addUrl($this->makeUrlConcrete($url, 1, $lastUpdatedAtDate), 'default');
            // Events detail view list
            $lastUpdatedAtDate = \DateTime::createFromFormat('d-m-Y', '01-01-2000');
            /** @var Event $activity */
            foreach ($this->events as $activity) {
                $url = $this->router->generate(
                    'front_event_detail',
                    array(
                        'slug' => $activity->getSlug(),
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                $event
                    ->getUrlContainer()
                    ->addUrl($this->makeUrlConcrete($url, 0.8, $activity->getUpdatedAt()), 'default');
                if ($activity->getUpdatedAt() > $lastUpdatedAtDate) {
                    $lastUpdatedAtDate = $activity->getUpdatedAt();
                }
            }
            // Events main view
            $url = $this->makeUrl('front_events_list');
            $event
                ->getUrlContainer()
                ->addUrl($this->makeUrlConcrete($url, 1, $lastUpdatedAtDate), 'default');
            // Privacy Policy view
            $url = $this->makeUrl('front_privacy_policy');
            $event
                ->getUrlContainer()
                ->addUrl($this->makeUrlConcrete($url, 0.1), 'default');
            // Credits view
            $url = $this->makeUrl('front_credits');
            $event
                ->getUrlContainer()
                ->addUrl($this->makeUrlConcrete($url, 0.1), 'default');
        }
    }

    /**
     * @param string     $routeName
     * @param array|null $parameters
     *
     * @return string
     */
    private function makeUrl(string $routeName, ?array $parameters = null): string
    {
        return $this->router->generate(
            $routeName, $parameters ?: [], UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param string        $url
     * @param int           $priority
     * @param DateTime|null $date
     *
     * @return UrlConcrete
     */
    private function makeUrlConcrete(string $url, int $priority = 1, ?DateTimeInterface $date = null): UrlConcrete
    {
        return new UrlConcrete(
            $url,
            $date ?? new DateTime(),
            UrlConcrete::CHANGEFREQ_WEEKLY,
            $priority
        );
    }
}
