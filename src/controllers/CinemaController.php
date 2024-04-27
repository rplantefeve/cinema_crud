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
    public function cinemasList(Request $request = null, Application $app = null, $addMode = "", $cinemaId = null)
    {
        // si l'utilisateur est connecté et qu'il est amdinistrateur
        $isUserAdmin = $this->checkIfUserIsConnectedAndAdmin($app);

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
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {
            // on assainit les entrées
            $entries = $this->extractArrayFromPostRequest(
                $request,
                [
                    'backToList',
                    'adresse',
                    'denomination',
                    'modificationInProgress',
                ]
            );

            // nous ne sommes pas en train de modifier un cinéma
            if ($entries['modificationInProgress'] === null) {
                // on ajoute le cinéma
                $this->cinemaDAO->insertNewCinema(
                    $entries['denomination'],
                    $entries['adresse']
                );
            } else { // sinon, nous sommes dans le cas d'une modification
                // mise à jour du cinéma
                $this->cinemaDAO->updateCinema(
                    $cinemaId,
                    $entries['denomination'],
                    $entries['adresse']
                );
            }
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
            $this->cinemaDAO->deleteCinema($cinemaId);
        }
        // redirection vers la liste des cinémas
        return $app->redirect($request->getBasePath() . '/cinema/list');
    }
}
