<?php

namespace Formation\CinemaCrud\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BonjourController extends Controller
{
    public function bonjourAction()
    {
        return $this->render('FormationCinemaCrudMainBundle:Bonjour:index.html.twig');
    }
}
