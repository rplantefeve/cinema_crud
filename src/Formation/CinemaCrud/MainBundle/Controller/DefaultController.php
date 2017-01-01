<?php

namespace Formation\CinemaCrud\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/hello")
 */
class DefaultController extends Controller {

    /**
     * @Method({"GET"})
     * @Route("/{name}", host="%mon_domaine%", name="hello_world")
     */
    public function indexAction($name) {
        return $this->render('FormationCinemaCrudMainBundle:Default:index.html.twig',
                        [
                    'name' => $name]);
    }

}
