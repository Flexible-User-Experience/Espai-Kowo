<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    /**
     * @Route("/blog", name="front_blog")
     *
     * @return Response
     */
    public function indexAction()
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findAll();

        return $this->render(':Frontend:Blog/list.html.twig',
            [ 'posts' => $posts]
            );
    }

    /**
     * @Route("/blog/{year}/{month}/{day}/{slug}", name="front_blog_detail")
     * @param         $year
     * @param         $month
     * @param         $day
     * @param         $slug
     *
     * @return Response
     */
    public function postDetailAction($year, $month, $day, $slug)
    {
        /** @var Post $post */
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneBy(
            array(
                'slug' => $slug
            )
        );

        return $this->render('Frontend/Blog/detail.html.twig',
            [ 'post' => $post ]
        );
    }
}
