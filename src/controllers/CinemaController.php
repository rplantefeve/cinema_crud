<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\controllers\Controller;
use Semeformation\Mvc\Cinema_crud\models\Cinema;
use Semeformation\Mvc\Cinema_crud\views\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Silex\Application;

/**
 * Description of CinemaController
 *
 * @author User
 */
class CinemaController extends Controller
{
    /**
     * Route Liste des cinémas
     * @param Request $request
     * @param Application $app
     */
    public function cinemasList(Request $request = null, Application $app = null, $addMode = "", $cinemaId = null)
    {
        // si l'utilisateur est connecté et qu'il est amdinistrateur
        $isUserAdmin = $this->checkIfUserIsConnectedAndAdmin($app);

        // on récupère la liste des cinémas ainsi que leurs informations
        $cinemas = $app['dao.cinema']->findAll();
        // liste des cinémas qui diffuse au moins un film
        $cinemasUndeletable = $app['dao.cinema']->findAllOnAir();
        $cinemaToBeModified = [];
        $toBeModified = null;

        // si nous sommes en mode modification
        if ($addMode === "edit") {
            // on a besoin de récupérer les infos du cinéma à partir de l'identifiant du cinéma
            $cinemaToBeModified = $app['dao.cinema']->find($cinemaId);
            $toBeModified = $cinemaToBeModified->getCinemaId();
        }

        // On génère la vue films
        $vue = new View("CinemasList");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer(
            $request,
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
     * @return RedirectResponse|void
     */
    public function editCinema(Request $request = null, Application $app = null, string $cinemaId = null)
    {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        $this->redirectIfUserNotConnectedOrNotAdmin($request, $app);

        // si la méthode de formulaire est la méthode POST
        if ($request->isMethod('POST')) {

            // on assainit les entrées
            $entries = $this->extractArrayFromPostRequest(
                $request,
                [
                    'backToList',
                    'adresse',
                    'denomination',
                ]
            );

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
    }

    /**
     * Route supprimer un cinéma
     * @param string $cinemaId
     * @param Request $request
     * @param Application $app
     * @return RedirectResponse
     */
    public function deleteCinema(Request $request = null, Application $app = null, string $cinemaId = null)
    {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas administrateur
        $this->redirectIfUserNotConnectedOrNotAdmin($request, $app);
        // suppression de la préférence de film
        if ($cinemaId !== null) {
            $app['dao.cinema']->delete($cinemaId);
        }
        // redirection vers la liste des cinémas
        return $app->redirect($request->getBasePath() . '/cinema/list');
    }
}
