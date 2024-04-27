<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\dao\UtilisateurDAO;
use Semeformation\Mvc\Cinema_crud\views\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Silex\Application;
use Psr\Log\LoggerInterface;
use Exception;

/**
 * Description of HomeController
 *
 * @author User
 */
class HomeController extends Controller
{
    /**
     * L'utilisateur de l'application
     */
    private $utilisateurDAO;

    /**
     * Constructeur de la classe
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->utilisateurDAO = new UtilisateurDAO($logger);
    }

    /**
     * Route Accueil
     */
    public function home(Request $request = null, Application $app = null)
    {
        // le user est-il authentifié à ce niveau ?
        $loginSuccess = $this->checkIfUserIsConnected($app);

        // variables de contrôle du formulaire
        $areCredentialsOK = true;

        // si l'utilisateur n'est pas authentifié
        if ($loginSuccess === false) {
            // si la méthode POST a été employée
            if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {
                $entries = $this->extractArrayFromPostRequest(
                    $request,
                    [
                        'email',
                        'password',
                    ]
                );

                return $this->login($entries, $areCredentialsOK, $app, $request);
            }
        }

        // On génère la vue Accueil
        $vue = new View("Home");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer(
            $request,
            [
                'areCredentialsOK' => $areCredentialsOK,
                'loginSuccess'     => $loginSuccess,
            ]
        );
    }

    /**
     * Vérifie si l'utilisateur existe et que son mot de passe est bon
     * @param type $sanitizedEntries
     * @param boolean $areCredentialsOK
     * @param Application $app
     * @param Request $request
     * @return RedirectResponse
     */
    private function login(
        $sanitizedEntries,
        &$areCredentialsOK,
        Application $app,
        Request $request
    ): RedirectResponse {
        try {
            // On vérifie l'existence de l'utilisateur
            $this->utilisateurDAO->verifyUserCredentials(
                $sanitizedEntries['email'],
                $sanitizedEntries['password']
            );

            // on enregistre l'utilisateur en session
            $username = $sanitizedEntries['email'];
            $userId   = $this->utilisateurDAO->getUserIDByEmailAddress($username);
            $app['session']->set(
                'user',
                [
                    'username' => $username,
                    'userId'   => $userId,
                ]
            );
            // redirection vers la liste des préférences de films
            return $app->redirect($request->getBasePath() . '/favorite/list');
        } catch (Exception $ex) {
            $areCredentialsOK = false;
            // FIXME ne fonctionne pas car pas de logger injecté dans les contrôleurs
            // $this->utilisateurDAO->getLogger()->error($ex->getMessage());
            // redirection vers la liste des préférences de films
            return $app->redirect($request->getBasePath() . '/home');
        }
    }

    /**
     * Route Création d'un nouvel utilisateur
     * @param Request $request
     * @param Application $app
     * @return type
     */
    public function createNewUser(
        Request $request = null,
        Application $app = null
    ) {
        // variables de contrôles du formulaire de création
        $isFirstNameEmpty            = false;
        $isLastNameEmpty             = false;
        $isEmailAddressEmpty         = false;
        $isUserUnique                = true;
        $isPasswordEmpty             = false;
        $isPasswordConfirmationEmpty = false;
        $isPasswordValid             = true;

        // si la méthode POST est utilisée, cela signifie que le formulaire a été envoyé
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {
            // on assainit les entrées
            $entries = $this->extractArrayFromPostRequest(
                $request,
                [
                    'firstName',
                    'lastName',
                    'email',
                    'password',
                    'passwordConfirmation',
                ]
            );

            // si le prénom n'a pas été renseigné
            if ($entries['firstName'] === "") {
                $isFirstNameEmpty = true;
            }

            // si le nom n'a pas été renseigné
            if ($entries['lastName'] === "") {
                $isLastNameEmpty = true;
            }

            // si l'adresse email n'a pas été renseignée
            if ($entries['email'] === "") {
                $isEmailAddressEmpty = true;
            } else {
                // On vérifie l'existence de l'utilisateur
                $userID = $this->utilisateurDAO->getUserIDByEmailAddress($entries['email']);
                // si on a un résultat, cela signifie que cette adresse email existe déjà
                if ($userID !== null) {
                    $isUserUnique = false;
                }
            }
            // si le password n'a pas été renseigné
            if ($entries['password'] === "") {
                $isPasswordEmpty = true;
            }
            // si la confirmation du password n'a pas été renseigné
            if ($entries['passwordConfirmation'] === "") {
                $isPasswordConfirmationEmpty = true;
            }

            // si le mot de passe et sa confirmation sont différents
            if ($entries['password'] !== $entries['passwordConfirmation']) {
                $isPasswordValid = false;
            }

            // si les champs nécessaires ne sont pas vides, que l'utilisateur est unique et que le mot de passe est valide
            if ($isFirstNameEmpty === false && $isLastNameEmpty === false && $isEmailAddressEmpty === false && $isUserUnique === true && $isPasswordEmpty === false && $isPasswordValid === true) {
                // hash du mot de passe
                $password = password_hash($entries['password'], PASSWORD_DEFAULT);
                // créer l'utilisateur
                $this->utilisateurDAO->createUser(
                    $entries['firstName'],
                    $entries['lastName'],
                    $entries['email'],
                    $password
                );

                $username = $entries['email'];
                $userId   = $this->utilisateurDAO->getUserIDByEmailAddress($username);
                $app['session']->set(
                    'user',
                    [
                        'username' => $username,
                        'userId'   => $userId,
                    ]
                );
                // redirection vers la liste des préférences de films
                return $app->redirect($request->getBasePath() . '/favorite/list');
            }
        } else { // sinon (le formulaire n'a pas été envoyé)
            // initialisation des variables du formulaire
            $entries['firstName'] = '';
            $entries['lastName']  = '';
            $entries['email']     = '';
        }

        $donnees = [
            'sanitizedEntries'            => $entries,
            'isFirstNameEmpty'            => $isFirstNameEmpty,
            'isLastNameEmpty'             => $isLastNameEmpty,
            'isEmailAddressEmpty'         => $isEmailAddressEmpty,
            'isUserUnique'                => $isUserUnique,
            'isPasswordEmpty'             => $isPasswordEmpty,
            'isPasswordConfirmationEmpty' => $isPasswordConfirmationEmpty,
            'isPasswordValid'             => $isPasswordValid,
        ];
        // On génère la vue Création d'un utilisateur
        $vue     = new View("CreateUser");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer($request, $donnees);
    }

    /**
     * Détruit la session d'authentification
     * @param Request $request
     * @param Application $app
     * @return RedirectResponse
     */
    public function logout(Request $request, Application $app): RedirectResponse
    {
        // démarrage de la session
        $app['session']->start();
        // destruction de la sessions
        $app['session']->invalidate();
        return $app->redirect($request->getBasePath() . '/home');
    }

    public function error($e): string
    {
        $this->utilisateurDAO->getLogger()->error('Exception : ' . $e->getMessage() . ', File : ' . $e->getFile() . ', Line : ' . $e->getLine() . ', Stack trace : ' . $e->getTraceAsString());
        $vue = new View("Error");
        return $vue->generer(['messageErreur' => $e->getMessage()]);
    }
}
