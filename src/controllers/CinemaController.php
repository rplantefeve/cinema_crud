<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\models\Cinema;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Silex\Application;

/**
 * Contrôleur qui gère la liste des cinémas, l'ajout, la modification et la suppression d'un Cinema
 *
 * @author User
 */
class CinemaController extends Controller {

    /**
     * Route Liste des cinémas
     * @param Request $request
     * @param Application $app
     */
    public function cinemasList(Request $request = null, Application $app = null) {
        $isUserAdmin = false;

        // si l'utilisateur est pas connecté et qu'il est amdinistrateur
        if ($app['session']->get('user') && $app['session']->get('user')['username'] ==
                'admin@adm.adm') {
            $isUserAdmin = true;
        }
        // on récupère la liste des cinémas ainsi que leurs informations
        $cinemas = $app['dao.cinema']->findAll();
        // on génère la vue cinémas
        return $app['twig']->render('cinemas.html.twig',
                        [
                    'titre'       => 'Cinémas',
                    'cinemas'     => $cinemas,
                    'isUserAdmin' => $isUserAdmin]);
    }

    /**
     * Route Ajouter/Modifier un cinéma
     * @param Request $request
     * @param Application $app
     * @param string $cinemaId
     * @return string La vue générée
     */
    public function editCinema(Request $request = null, Application $app = null,
            string $cinemaId = null) {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        if (!$app['session']->get('user') || $app['session']->get('user')['username'] !==
                'admin@adm.adm') {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // init. de l'objet à null
        $cinema = null;

        // si la méthode de formulaire est la méthode POST
        if ($request->isMethod('POST')) {

            // on assainit les entrées
            $entries = $this->extractArrayFromPostRequest($request,
                    [
                'backToList',
                'adresse',
                'denomination']);

            // si l'action demandée est retour en arrière
            if ($entries['backToList'] !== null) {
                // on redirige vers la page des cinémas
                return $app->redirect($request->getBasePath() . '/cinema/list');
            }
            // sinon (l'action demandée est la sauvegarde d'un cinéma)
            else {

                // Je crée l'objet $cinema
                $cinema = new Cinema();
                // je mets à jour les infos
                $cinema->setDenomination($entries['denomination']);
                $cinema->setAdresse($entries['adresse']);
                $cinema->setCinemaId($cinemaId);
                // on sauvegarde le cinéma
                $app['dao.cinema']->save($cinema);
                // on revient à la liste des cinémas
                return $app->redirect($request->getBasePath() . '/cinema/list');
            }
        }// si la page est chargée avec $_GET
        elseif ($request->isMethod('GET')) {
            // on assainit les entrées
            $entries['cinemaID'] = $cinemaId;
            // si l'id est bien renseigné
            if ($entries && $entries['cinemaID'] !== null && $entries['cinemaID'] !==
                    '') {
                // on récupère les informations manquantes 
                $cinema = $app['dao.cinema']->find($entries['cinemaID']);
            }
        }

        $donnees = [
            'titre'  => 'Ajouter/Modifier un cinéma',
            'cinema' => $cinema];
        // On génère la vue films
        return $app['twig']->render('cinema.edit.html.twig', $donnees);
    }

    /**
     * Route supprimer un cinéma
     * @param Request $request
     * @param Application $app
     * @param string $cinemaId
     * @return RedirectResponse
     */
    public function deleteCinema(Request $request = null,
            Application $app = null, string $cinemaId): RedirectResponse {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas administrateur
        if (!$app['session']->get('user') || $app['session']->get('user')['username'] !==
                'admin@adm.adm') {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // si la méthode de formulaire est la méthode POST
        if ($request->isMethod('POST')) {

            // on assainit les entrées
            $entries['cinemaID'] = $cinemaId;

            // suppression de la préférence de film
            $app['dao.cinema']->delete($entries['cinemaID']);
        }
        // redirection vers la liste des cinémas
        return $app->redirect($request->getBasePath() . '/cinema/list');
    }

}
