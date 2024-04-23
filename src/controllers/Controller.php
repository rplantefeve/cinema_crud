<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

abstract class Controller
{
    /**
     * Vérifie que l'utilisateur est connecté
     *
     * @return boolean
     */
    protected function checkUserConnected(): bool
    {
        $isUserConnected = false;

        session_start();
        // si l'utilisateur est connecté et qu'il est amdinistrateur
        if (array_key_exists("user", $_SESSION) === true) {
            $isUserConnected = true;
        }

        return $isUserConnected;
    }

    /**
     * Vérifie que l'utilisateur est connecté et qu'il est administrateur
     *
     * @return boolean
     */
    protected function checkAdminRights(): bool
    {
        $isUserAdmin = false;

        session_start();
        // si l'utilisateur est connecté et qu'il est amdinistrateur
        if (array_key_exists("user", $_SESSION) === true && $_SESSION['user'] === 'admin@adm.adm') {
            $isUserAdmin = true;
        }

        return $isUserAdmin;
    }

    /**
     * Redirige l'utilisateur vers la page d'accueil s'il n'est pas connecté ou s'il n'est pas administrateur
     *
     * @return void
     */
    protected function redirectIfNotNotConnectedOrNotAdmin(): void
    {
        session_start();
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        if (array_key_exists("user", $_SESSION) === false || $_SESSION['user'] !== 'admin@adm.adm') {
            // renvoi à la page d'accueil
            header('Location: index.php');
            exit;
        }
    }

    /**
     * Redirige l'utilisateur vers la page d'accueil s'il n'est pas connecté
     *
     * @return void
     */
    protected function redirectIfNotNotConnected(): void
    {
        session_start();
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        if (array_key_exists("user", $_SESSION) === false) {
            // renvoi à la page d'accueil
            header('Location: index.php');
            exit;
        }
    }
}
