<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Tasks;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller used to manage tasks contents in the public part of the site.
 *
 * @Route("/tasks")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class TasksController extends Controller
{
    /**
     * @Route("/", defaults={"page": 1}, name="tasks_index")
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="tasks_index_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
    public function indexAction($page)
    {
       // $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findLatest($page);

       // return $this->render('tasks/index.html.twig', array('posts' => $posts));
        return $this->render('tasks/index.html.twig');
    }

    /**
     * @Route("/posts/{slug}", name="tasks_post")
     * @Method("GET")
     *
     * NOTE: The $post controller argument is automatically injected by Symfony
     * after performing a database query looking for a Post with the 'slug'
     * value given in the route.
     * See http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html
     */
    public function postShowAction(Post $post)
    {
        return $this->render('tasks/post_show.html.twig', array('post' => $post));
    }

    /**
     * @Route("/comment/{postSlug}/new", name="comment_new")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
     *
     * NOTE: The ParamConverter mapping is required because the route parameter
     * (postSlug) doesn't match any of the Doctrine entity properties (slug).
     * See http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#doctrine-converter
     */
    public function commentNewAction(Request $request, Post $post)
    {
        $form = $this->createForm('AppBundle\Form\CommentType');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setAuthorEmail($this->getUser()->getEmail());
            $comment->setPost($post);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('tasks_post', array('slug' => $post->getSlug()));
        }

        return $this->render('tasks/comment_form_error.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * This controller is called directly via the render() function in the
     * tasks/post_show.html.twig template. That's why it's not needed to define
     * a route name for it.
     *
     * The "id" of the Post is passed in and then turned into a Post object
     * automatically by the ParamConverter.
     *
     * @param Post $post
     *
     * @return Response
     */
    public function commentFormAction(Post $post)
    {
        $form = $this->createForm('AppBundle\Form\CommentType');

        return $this->render('tasks/_comment_form.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }
}
