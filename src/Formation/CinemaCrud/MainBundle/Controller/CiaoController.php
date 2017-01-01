<?php

namespace Formation\CinemaCrud\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CiaoController extends Controller
{
    public function ciaoAction()
    {
        return $this->render('FormationCinemaCrudMainBundle:Ciao:index.html.twig');
    }
}
