<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\controllers\Controller;
use Semeformation\Mvc\Cinema_crud\dao\FilmDAO;
use Semeformation\Mvc\Cinema_crud\views\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Silex\Application;
use Psr\Log\LoggerInterface;

/**
 * Description of MovieController
 *
 * @author User
 */
class MovieController extends Controller
{
    private $filmDAO;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->filmDAO = new FilmDAO($logger);
    }

    /**
     * Route Liste des films
     */
    function moviesList(Request $request = null, Application $app = null, $addMode = "", $filmId = null) {
        $isUserAdmin = $this->checkIfUserIsConnectedAndAdmin($app);

        // on récupère la liste des films ainsi que leurs informations
        $films = $this->filmDAO->getMoviesList();
        // liste des cinémas qui diffuse au moins un film
        $moviesUndeletable = $this->filmDAO->getOnAirMoviesId();
        $filmToBeModified = [];
        $toBeModified = null;

        // si nous sommes en mode modification
        if ($addMode === "edit") {
            // on a besoin de récupérer les infos du film à partir de l'identifiant du film
            $filmToBeModified = $this->filmDAO->getMovieByID($filmId);
            $toBeModified = $filmToBeModified->getFilmId();
        }

        // On génère la vue films
        $vue = new View("MoviesList");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer($request,
            [
                'films'            => $films,
                'onAirFilms'       => $moviesUndeletable,
                'isUserAdmin'      => $isUserAdmin,
                'mode'             => $addMode,
                'filmToBeModified' => $filmToBeModified,
                'toBeModified'     => $toBeModified,
            ]
        );
    }

    /**
     * Route Ajouter / Modifier un film
     *
     * @return never
     */
    function editMovie(Request $request = null, Application $app = null,
            string $filmId = null) {
        
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        $this->redirectIfUserNotConnectedOrNotAdmin($request, $app);

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {
            // on assainit les entrées
            $entries = $this->extractArrayFromPostRequest($request,
                    [
                'backToList',
                'titre',
                'titreOriginal',
                'modificationInProgress'
            ]);

            // et que nous ne sommes pas en train de modifier un film
            if ($entries['modificationInProgress'] === null) {
                // on ajoute le film
                $this->filmDAO->insertNewMovie($entries['titre'],
                        $entries['titreOriginal']);
            }
            // sinon, nous sommes dans le cas d'une modification
            else {
                // mise à jour du film
                $this->filmDAO->updateMovie($filmId,
                        $entries['titre'], $entries['titreOriginal']);
            }
            // on revient à la liste des films
            return $app->redirect($request->getBasePath() . '/movie/list');
        }
    }
    

    /**
     * Route Supprimer un film
     * @param string $filmId
     * @param Request $request
     * @param Application $app
     * @return type
     */
    public function deleteMovie(Request $request = null, Application $app = null, string $filmId) {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas administrateur
        $this->redirectIfUserNotConnectedOrNotAdmin($request,  $app);
        // si la méthode de formulaire est la méthode POST
        if ($filmId !== null && $filmId !== "") {
            // suppression de la préférence de film
            $this->filmDAO->deleteMovie($filmId);
        }
        // redirection vers la liste des films
        return $app->redirect($request->getBasePath() . '/movie/list');
    }

}
