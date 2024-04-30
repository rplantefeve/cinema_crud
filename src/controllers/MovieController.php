<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\views\View;
use Symfony\Component\HttpFoundation\Request;
use Semeformation\Mvc\Cinema_crud\models\Film;
use Silex\Application;
use Psr\Log\LoggerInterface;

/**
 * Description of MovieController
 *
 * @author User
 */
class MovieController extends Controller
{
    /**
     * Route Liste des films
     */
    public function moviesList(Request $request = null, Application $app = null, $addMode = "", $filmId = null)
    {
        $isUserAdmin = $this->checkIfUserIsConnectedAndAdmin($app);

        // on récupère la liste des films ainsi que leurs informations
        $films = $app['dao.film']->findAll();
        // liste des cinémas qui diffuse au moins un film
        $moviesUndeletable = $app['dao.film']->findAllOnAir();
        $filmToBeModified = [];
        $toBeModified = null;

        // si nous sommes en mode modification
        if ($addMode === "edit") {
            // on a besoin de récupérer les infos du film à partir de l'identifiant du film
            $filmToBeModified = $app['dao.film']->find($filmId);
            $toBeModified = $filmToBeModified->getFilmId();
        }

        // On génère la vue films
        $vue = new View("MoviesList");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer(
            $request,
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
     * @param Request $request
     * @param Application $app
     * @param string $filmId
     * @return RedirectResponse
     */
    public function editMovie(
        Request $request = null,
        Application $app = null,
        string $filmId = null
    ) {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        $this->redirectIfUserNotConnectedOrNotAdmin($request, $app);

        // si la méthode de formulaire est la méthode POST
        if ($request->isMethod('POST') === true) {
            // on assainit les entrées
            $entries = $this->extractArrayFromPostRequest(
                $request,
                [
                    'backToList',
                    'titre',
                    'titreOriginal',
                    'modificationInProgress',
                ]
            );

            $film = new Film();
            $film->setTitre($entries['titre']);
            $film->setTitreOriginal($entries['titreOriginal']);
            $film->setFilmId($filmId);
            // on sauvegarde le film
            $app['dao.film']->save($film);
            // on revient à la liste des films
            return $app->redirect($request->getBasePath() . '/movie/list');
        }
    }


    /**
     * Route Supprimer un film
     * @param string $filmId
     * @param Request $request
     * @param Application $app
     * @return RedirectResponse
     */
    public function deleteMovie(Request $request = null, Application $app = null, string $filmId)
    {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas administrateur
        $this->redirectIfUserNotConnectedOrNotAdmin($request, $app);
        // si la méthode de formulaire est la méthode POST
        if ($filmId !== null && $filmId !== "") {
            // suppression de la préférence de film
            $app['dao.film']->delete($filmId);
        }
        // redirection vers la liste des films
        return $app->redirect($request->getBasePath() . '/movie/list');
    }
}
