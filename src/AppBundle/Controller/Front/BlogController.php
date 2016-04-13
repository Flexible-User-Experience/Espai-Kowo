<?php

namespace AppBundle\Controller\Front;

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
     * @Route("/blog/{slug}", name="front_blog_detail")
     * @param         $slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postDetailAction($slug)
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
