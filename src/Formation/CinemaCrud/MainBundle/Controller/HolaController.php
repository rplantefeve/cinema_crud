<?php

namespace Formation\CinemaCrud\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HolaController extends Controller
{
    public function holaAction()
    {
        return $this->render('FormationCinemaCrudMainBundle:Hola:index.html.twig');
    }
}
