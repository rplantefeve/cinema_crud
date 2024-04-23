<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\controllers\Controller;
use Semeformation\Mvc\Cinema_crud\dao\CinemaDAO;
use Semeformation\Mvc\Cinema_crud\views\View;
use Psr\Log\LoggerInterface;

/**
 * Description of CinemaController
 *
 * @author User
 */
class CinemaController extends Controller
{
    private $cinemaDAO;

    public function __construct(LoggerInterface $logger)
    {
        $this->cinemaDAO = new CinemaDAO($logger);
    }

    /**
     * Route Liste des cinémas
     */
    public function cinemasList($mode = ""): void
    {
        // Vérifie que l'utilisateur est connecté et qu'il est administrateur
        $isUserAdmin = $this->checkAdminRights();

        // on récupère la liste des cinémas ainsi que leurs informations
        $cinemas = $this->cinemaDAO->getCinemasList();
        // liste des cinémas qui diffuse au moins un film
        $cinemasUndeletable = $this->cinemaDAO->getOnAirCinemasId();
        $cinemaToBeModified = [];
        $toBeModified = null;

        // si nous sommes en mode modification
        if ($mode === "edit") {
            $sanitizedEntries = filter_input_array(
                INPUT_GET,
                ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]
            );
            // on a besoin de récupérer les infos du cinéma à partir de l'identifiant du cinéma
            $cinemaToBeModified = $this->cinemaDAO->getCinemaByID($sanitizedEntries['cinemaID']);
            $toBeModified = $cinemaToBeModified->getCinemaId();
        }

        // On génère la vue films
        $vue = new View("CinemasList");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer(
            [
                'cinemas'            => $cinemas,
                'onAirCinemas'       => $cinemasUndeletable,
                'isUserAdmin'        => $isUserAdmin,
                'mode'               => $mode,
                'cinemaToBeModified' => $cinemaToBeModified,
                'toBeModified'       => $toBeModified,
            ]
        );
    }

    /**
     * Route Ajouter/Modifier un cinéma
     *
     * @return void
     */
    public function editCinema()
    {
        // Redirige l'utilisateur vers la page d'accueil s'il n'est pas connecté ou s'il n'est pas administrateur
        $this->redirectIfNotNotConnectedOrNotAdmin();

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {
            // on "sainifie" les entrées
            $sanEntries = filter_input_array(
                INPUT_POST,
                [
                    'cinemaID'               => FILTER_SANITIZE_NUMBER_INT,
                    'adresse'                => FILTER_DEFAULT,
                    'denomination'           => FILTER_DEFAULT,
                    'modificationInProgress' => FILTER_DEFAULT,
                ]
            );

            // et que nous ne sommes pas en train de modifier un cinéma
            if ($sanEntries['modificationInProgress'] === null) {
                // on ajoute le cinéma
                $this->cinemaDAO->insertNewCinema(
                    $sanEntries['denomination'],
                    $sanEntries['adresse']
                );
            } else { // sinon, nous sommes dans le cas d'une modification
                // mise à jour du cinéma
                $this->cinemaDAO->updateCinema(
                    $sanEntries['cinemaID'],
                    $sanEntries['denomination'],
                    $sanEntries['adresse']
                );
            }
            // on revient à la liste des cinémas
            header('Location: index.php?action=cinemasList');
            exit;
        }
    }

    /**
     * Route supprimer un cinéma
     *
     * @return never
     */
    public function deleteCinema()
    {
        $this->redirectIfNotNotConnectedOrNotAdmin();

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {
            // on "sainifie" les entrées
            $sanitizedEntries = filter_input_array(
                INPUT_POST,
                ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]
            );

            // suppression de la préférence de film
            $this->cinemaDAO->deleteCinema($sanitizedEntries['cinemaID']);
        }
        // redirection vers la liste des cinémas
        header("Location: index.php?action=cinemasList");
        exit;
    }
}
