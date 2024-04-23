<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\controllers\Controller;
use Semeformation\Mvc\Cinema_crud\dao\FilmDAO;
use Semeformation\Mvc\Cinema_crud\views\View;
use Psr\Log\LoggerInterface;

/**
 * Description of MovieController
 *
 * @author User
 */
class MovieController extends Controller
{
    private $filmDAO;

    public function __construct(LoggerInterface $logger)
    {
        $this->filmDAO = new FilmDAO($logger);
    }

    /**
     * Route Liste des films
     */
    public function moviesList($mode = ""): void
    {
        $isUserAdmin = $this->checkAdminRights();

        // on récupère la liste des films ainsi que leurs informations
        $films = $this->filmDAO->getMoviesList();
        // liste des cinémas qui diffuse au moins un film
        $moviesUndeletable = $this->filmDAO->getOnAirMoviesId();
        $filmToBeModified = [];
        $toBeModified = null;

        // si nous sommes en mode modification
        if ($mode === "edit") {
            $sanitizedEntries = filter_input_array(
                INPUT_GET,
                ['filmID' => FILTER_SANITIZE_NUMBER_INT]
            );
            // on a besoin de récupérer les infos du film à partir de l'identifiant du film
            $filmToBeModified = $this->filmDAO->getMovieByID($sanitizedEntries['filmID']);
            $toBeModified = $filmToBeModified->getFilmId();
        }

        // On génère la vue films
        $vue = new View("MoviesList");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer(
            [
                'films'            => $films,
                'onAirFilms'       => $moviesUndeletable,
                'isUserAdmin'      => $isUserAdmin,
                'mode'             => $mode,
                'filmToBeModified' => $filmToBeModified,
                'toBeModified'     => $toBeModified,
            ]
        );
    }

    /**
     * Route Supprimer un film
     *
     * @return never
     */
    public function deleteMovie()
    {
        $this->redirectIfNotNotConnectedOrNotAdmin();

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {
            // on "sainifie" les entrées
            $sanitizedEntries = filter_input_array(
                INPUT_POST,
                ['filmID' => FILTER_SANITIZE_NUMBER_INT]
            );

            // suppression de la préférence de film
            $this->filmDAO->deleteMovie($sanitizedEntries['filmID']);
        }
        // redirection vers la liste des films
        header("Location: index.php?action=moviesList");
        exit;
    }

    /**
     * Route Ajouter / Modifier un film
     *
     * @return void
     */
    public function editMovie()
    {
        $this->redirectIfNotNotConnectedOrNotAdmin();

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {
            // on assainit les entrées
            $sanEntries = filter_input_array(
                INPUT_POST,
                [
                    'filmID'                 => FILTER_SANITIZE_NUMBER_INT,
                    'titre'                  => FILTER_DEFAULT,
                    'titreOriginal'          => FILTER_DEFAULT,
                    'modificationInProgress' => FILTER_DEFAULT,
                ]
            );

            // et que nous ne sommes pas en train de modifier un film
            if ($sanEntries['modificationInProgress'] === null) {
                // on ajoute le film
                $this->filmDAO->insertNewMovie(
                    $sanEntries['titre'],
                    $sanEntries['titreOriginal']
                );
            } else { // sinon, nous sommes dans le cas d'une modification
                // mise à jour du film
                $this->filmDAO->updateMovie(
                    $sanEntries['filmID'],
                    $sanEntries['titre'],
                    $sanEntries['titreOriginal']
                );
            }
            // on revient à la liste des films
            header('Location: index.php?action=moviesList');
            exit;
        }
    }
}
