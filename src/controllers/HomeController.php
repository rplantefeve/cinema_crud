<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use \Semeformation\Mvc\Cinema_crud\models\Utilisateur;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Silex\Application;

/**
 * Le contrôleur qui gère l'accueil du visiteur, son authentification et son logout
 *
 * @author User
 */
class HomeController extends Controller
{
    /**
     * Route Accueil
     *
     * @param Request|null $request
     * @param Application|null $app
     * @return RedirectResponse|string
     */
    public function home(Request $request = null, Application $app = null)
    {
        // le user est-il authentifié à ce niveau ?
        $loginSuccess = $this->checkIfUserIsConnected($app);

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

                return $this->login($entries, $app, $request);
            }
        }

        $error = null;
        // si une erreur est présente dans la requête GET
        if ($request->getMethod() === 'GET'){
            $entries = $this->extractArrayFromGetRequest($request, ['error']);
            if ($entries['error'] !== null) {
                $error = $entries['error'];
            }
        }

        // on retourne la vue générée
        return $app['twig']->render('index.html.twig',
                        [
                    'titre'        => 'Accueil',
                    'errorMessage' => false,
                    'email'        => '',
                    'loginSuccess' => $loginSuccess]);
    }

    /**
     * Vérifie si l'utilisateur existe et que son mot de passe est bon
     * @param array $sanitizedEntries
     * @param Application $app
     * @param Request $request
     * @return string La vue générée
     */
    private function login(
        $sanitizedEntries,
        Application $app,
        Request $request
    ) {
        try {
            $errorMessage = false;
            // On vérifie l'existence de l'utilisateur
            $utilisateur = $app['dao.utilisateur']->findOneByCourrielAndPassword(
                $sanitizedEntries['email'],
                $sanitizedEntries['password']
            );

            // on enregistre l'utilisateur en session
            $username = $sanitizedEntries['email'];
            $userId = $utilisateur->getUserId();
            $app['session']->set(
                'user',
                [
                    'username' => $username,
                    'userId'   => $userId,
                ]
            );
            // redirection vers la liste des préférences de films
            return $app->redirect($request->getBasePath() . '/favorite/list');
        } catch (\Exception $ex) {
            $loginSuccess = false;
            $errorMessage = $ex->getMessage();
            return $app['twig']->render('index.html.twig',
                            [
                        'titre'        => 'Accueil',
                        'email'        => $sanitizedEntries['email'],
                        'loginSuccess' => $loginSuccess,
                        'errorMessage' => $errorMessage]);
        }
    }

    /**
     * Route Création d'un nouvel utilisateur
     * @param Request $request
     * @param Application $app
     * @return string La vue générée
     */
    public function createNewUser(
        Request $request = null,
        Application $app = null
    ) {
        // variables de contrôles du formulaire de création
        $isFirstNameEmpty = false;
        $isLastNameEmpty = false;
        $isEmailAddressEmpty = false;
        $isUserUnique = true;
        $isPasswordEmpty = false;
        $isPasswordConfirmationEmpty = false;
        $isPasswordValid = true;

        // si la méthode POST est utilisée, cela signifie que le formulaire a été envoyé
        if ($request->isMethod('POST')) {
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
                $user = $app['dao.utilisateur']->findOneByCourriel($entries['email']);
                // si on a un résultat, cela signifie que cette adresse email existe déjà
                if ($user->getUserId() !== null) {
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
                $password    = password_hash($entries['password'],
                        PASSWORD_DEFAULT);
                $utilisateur = new Utilisateur();
                $utilisateur->setAdresseCourriel($entries['email']);
                $utilisateur->setNom($entries['lastName']);
                $utilisateur->setPrenom($entries['firstName']);
                $utilisateur->setPassword($password);
                // créer l'utilisateur
                $app['dao.utilisateur']->save($utilisateur);

                $username = $entries['email'];
                $userId = $utilisateur->getUserId();
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
            $entries['lastName'] = '';
            $entries['email'] = '';
        }
        $utilisateur = new Utilisateur();
        $utilisateur->setNom($entries['lastName']);
        $utilisateur->setPrenom($entries['firstName']);
        $utilisateur->setAdresseCourriel($entries['email']);

        $donnees = [
            'titre'                       => 'Création d\'un nouvel utilisateur',
            'utilisateur'                 => $utilisateur,
            'isFirstNameEmpty'            => $isFirstNameEmpty,
            'isLastNameEmpty'             => $isLastNameEmpty,
            'isEmailAddressEmpty'         => $isEmailAddressEmpty,
            'isUserUnique'                => $isUserUnique,
            'isPasswordEmpty'             => $isPasswordEmpty,
            'isPasswordConfirmationEmpty' => $isPasswordConfirmationEmpty,
            'isPasswordValid'             => $isPasswordValid,
        ];
        // On génère la vue Création d'un utilisateur
        return $app['twig']->render('user.create.html.twig', $donnees);
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

}
