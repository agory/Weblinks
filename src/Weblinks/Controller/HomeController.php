<?php

namespace Weblinks\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Weblinks\Domain\Link;
use Weblinks\Form\Type\LinkType;


class HomeController {

    /**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app) {
        $links = $app['dao.link']->findAll();
        return $app['twig']->render('index.html.twig', array('links' => $links));
    }

    /**
     * Link details controller.
     *
     * @param integer $id Link id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function AddLink(Request $request, Application $app) {
        $link = new Link();
        $user = $app['security']->getToken()->getUser();
        if ($app['security']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $link->setUser($user);
            $linkForm = $app['form.factory']->create(new LinkType(), $link);
            $linkForm->handleRequest($request);
            if ($linkForm->isValid()) {
                $app['dao.link']->save($link);
                $app['session']->getFlashBag()->add('success', 'Your link was succesfully added.');
            }
            $linkFormView = $linkForm->createView();
        }
        return $app['twig']->render('link.html.twig', array(
            'linkForm' => $linkFormView));
    }

    /**
     * User login controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function loginAction(Request $request, Application $app) {
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
            ));
    }
}