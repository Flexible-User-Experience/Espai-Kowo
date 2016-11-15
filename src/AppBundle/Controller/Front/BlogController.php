<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    /**
     * @Route("/blog/{pagina}", name="front_blog")
     *
     * @param int $pagina
     *
     * @return Response
     */
    public function indexAction($pagina = 1)
    {
        $tags = $this->getDoctrine()->getRepository('AppBundle:Tag')->getAllEnabledSortedByTitle();
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->getAllEnabledSortedByPublishedDateWithJoinUntilNow();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($posts, $pagina);

        return $this->render(':Frontend:Blog/list.html.twig',
            [
                'pagination' => $pagination,
                'tags'       => $tags,
            ]
        );
    }

    /**
     * @Route("/blog/{year}/{month}/{day}/{slug}", name="front_blog_detail")
     * @param $year
     * @param $month
     * @param $day
     * @param $slug
     *
     * @return Response
     * @throws EntityNotFoundException
     */
    public function postDetailAction($year, $month, $day, $slug)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $year . '-' . $month . '-' . $day);

        /** @var Post $post */
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneBy(
            array(
                'publishedAt' => $date,
                'slug'        => $slug,
            )
        );

        if (!$post) {
            throw new EntityNotFoundException();
        }

        return $this->render('Frontend/Blog/detail.html.twig',
            [ 'post' => $post ]
        );
    }

    /**
     * @Route("/blog/categoria/{slug}", name="front_blog_tag_detail")
     * @param string $slug
     *
     * @return Response
     * @throws EntityNotFoundException
     */
    public function tagDetailAction($slug)
    {
        $tags = $this->getDoctrine()->getRepository('AppBundle:Tag')->getAllEnabledSortedByTitle();
        /** @var Tag $tag */
        $tag = $this->getDoctrine()->getRepository('AppBundle:Tag')->findOneBy(
            array(
                'slug' => $slug,
            )
        );

        if (!$tag) {
            throw new EntityNotFoundException();
        }

        return $this->render(':Frontend/Blog:tag_detail.html.twig', [
            'tags' => $tags,
            'tag'  => $tag,
        ]);
    }
}
