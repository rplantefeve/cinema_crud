<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\controllers\Controller;
use Semeformation\Mvc\Cinema_crud\dao\CinemaDAO;
use Semeformation\Mvc\Cinema_crud\views\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Silex\Application;
use Psr\Log\LoggerInterface;

/**
 * Description of CinemaController
 *
 * @author User
 */
class CinemaController extends Controller
{
    private $cinemaDAO;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->cinemaDAO = new CinemaDAO($logger);
    }

    /**
     * Route Liste des cinémas
     * @param Request $request
     * @param Application $app
     */
    public function cinemasList(Request $request = null, Application $app = null, $addMode = "", $cinemaId = null) {
        $isUserAdmin = false;

        // si l'utilisateur est pas connecté et qu'il est amdinistrateur
        if ($app['session']->get('user') and $app['session']->get('user')['username'] ==
                'admin@adm.adm') {
            $isUserAdmin = true;
        }

        // on récupère la liste des cinémas ainsi que leurs informations
        $cinemas = $this->cinemaDAO->getCinemasList();
        // liste des cinémas qui diffuse au moins un film
        $cinemasUndeletable = $this->cinemaDAO->getOnAirCinemasId();
        $cinemaToBeModified = [];
        $toBeModified = null;

        // si nous sommes en mode modification
        if ($addMode === "edit") {
            // on a besoin de récupérer les infos du cinéma à partir de l'identifiant du cinéma
            $cinemaToBeModified = $this->cinemaDAO->getCinemaByID($cinemaId);
            $toBeModified = $cinemaToBeModified->getCinemaId();
        }

        // On génère la vue films
        $vue = new View("CinemasList");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer($request,
            [
                'cinemas'            => $cinemas,
                'onAirCinemas'       => $cinemasUndeletable,
                'isUserAdmin'        => $isUserAdmin,
                'mode'               => $addMode,
                'cinemaToBeModified' => $cinemaToBeModified,
                'toBeModified'       => $toBeModified,
            ]
        );
    }

    /**
     * Route Ajouter/Modifier un cinéma
     * @param Request $request
     * @param Application $app
     * @param string $cinemaId
     * @return type
     */
    public function editCinema(Request $request = null, Application $app = null,
            string $cinemaId = null) {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        if (!$app['session']->get('user') or $app['session']->get('user')['username'] !==
                'admin@adm.adm') {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // variable qui sert à conditionner l'affichage du formulaire
        $isItACreation = false;

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

            // on assainit les entrées
            $entries = $this->extractArrayFromPostRequest($request,
                    ['backToList',
                'adresse',
                'denomination',
                'modificationInProgress']);


            // nous ne sommes pas en train de modifier un cinéma
            if ($entries['modificationInProgress'] === null) {
                // on ajoute le cinéma
                $this->cinemaDAO->insertNewCinema($entries['denomination'],
                        $entries['adresse']);
            }
            // sinon, nous sommes dans le cas d'une modification
            else {
                // mise à jour du cinéma
                $this->cinemaDAO->updateCinema($cinemaId,
                        $entries['denomination'], $entries['adresse']);
            }
            // on revient à la liste des cinémas
            return $app->redirect($request->getBasePath() . '/cinema/list');
        }// si la page est chargée avec $_GET
        elseif (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "GET") {
            // on assainit les entrées
            $entries['cinemaID'] = $cinemaId;
            // si l'id est bien renseigné
            if ($entries && $entries['cinemaID'] !== null && $entries['cinemaID'] !==
                    '') {
                // on récupère les informations manquantes 
                $cinema = $this->cinemaDAO->getCinemaByID($entries['cinemaID']);
            }
            // sinon, c'est une création
            else {
                $isItACreation = true;
                $cinema = null;
            }
        }
        // On génère la vue films
        $vue = new View("EditCinema");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer($request,
                        [
                    'cinema'        => $cinema,
                    'isItACreation' => $isItACreation,
        ]);
    }

    /**
     * Route supprimer un cinéma
     * @param string $cinemaId
     * @param Request $request
     * @param Application $app
     * @return RedirectResponse
     */
    public function deleteCinema(Request $request = null,
            Application $app = null, string $cinemaId): RedirectResponse {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas administrateur
        if (!$app['session']->get('user') or $app['session']->get('user')['username'] !==
                'admin@adm.adm') {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

            // on assainit les entrées
            $entries['cinemaID'] = $cinemaId;

            // suppression de la préférence de film
            $this->cinemaDAO->deleteCinema($entries['cinemaID']);
        }
        // redirection vers la liste des cinémas
        return $app->redirect($request->getBasePath() . '/cinema/list');
    }
}
