<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Symfony\Component\HttpFoundation\Request;

/**
 * Description of Controller
 *
 * @author Seme
 */
class Controller {

    /**
     * Se charge d'extraire les données POST de la requête sous forme de tableau
     * @param Request $request Requête HTTP POST
     * @param array $variables Tableau de noms de variables à récupérer
     * @return array Tableau de variables extraites de la requête POST
     */
    protected function extractArrayFromPostRequest(Request $request,
            array $variables): array {
        $entries = array();
        // boucle de parcours des variables à extraire
        foreach ($variables as $variable) {
            $entries[$variable] = $request->request->get($variable);
        }
        return $entries;
    }

    /**
     * Se charge d'extraire les données GET de la requête sous forme de tableau
     * @param Request $request Requête HTTP GET
     * @param array $variables Tableau de noms de variables à récupérer
     * @return array Tableau de variables extraites de la requête POST
     */
    protected function extractArrayFromGetRequest(Request $request,
            array $variables): array {
        $entries = array();
        // boucle de parcours des variables à extraire
        foreach ($variables as $variable) {
            $entries[$variable] = $request->query->get($variable);
        }
        return $entries;
    }

    protected function redirectIfUserNotConnected($request, $app) {
        if ($app['session']->get('user') === null) {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }
    }

    protected function redirectIfUserNotConnectedOrNotAdmin($request, $app) {
        if ($app['session']->get('user') === null || $app['session']->get('user')['username'] !== 'admin@adm.adm') {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }
    }

    protected function checkIfUserIsConnectedAndAdmin($app) {
        if ($app['session']->get('user') && $app['session']->get('user')['username'] === 'admin@adm.adm') {
            return true;
        }
        return false;
    }
}
