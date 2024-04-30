<?php

namespace Semeformation\Mvc\Cinema_crud\views;

use Exception;

/**
 * Description of View
 *
 * @author User
 */
class View
{
    // Nom du fichier associé à la vue
    private string $fichier;
    // titre de la vue
    private string $titre;

    public function __construct(string $action)
    {
        // La vue à générer dépend de l'action demandée
        $this->fichier = __DIR__ . "/view" . $action . ".php";
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $donnees
     * @return string
     */
    public function generer(
        \Symfony\Component\HttpFoundation\Request $request,
        $donnees = null
    ): string {
        // on passe
        $donnees['request'] = $request;
        // Génération de la partie spécifique de la vue
        $content = $this->genererFichier($this->fichier, $donnees);
        // utilisation du template avec chargement des données spécifiques
        $vue = $this->genererFichier(
            __DIR__ . '/viewTemplate.php',
            [
                'title'   => $this->titre,
                'content' => $content,
                'request' => $request,
            ]
        );
        // Renvoi de la vue générée au navigateur
        return $vue;
    }

    /**
     * Génère et retourne la vue générée
     *
     * @param string $fichier
     * @param array<mixed> $donnees
     * @return string
     */
    private function genererFichier(string $fichier, array $donnees): string
    {
        if (file_exists($fichier) === true) {
            // déclare autant de variables qu'il y en a dans le tableau
            if ($donnees !== null) {
                extract($donnees);
            }
            // Toutes les données ne vont pas au navigateur mais dans un tampon
            ob_start();
            // La vue est envoyée dans la tampon de sortie
            include $fichier;
            // Renvoi du contenu du tampon et nettoyage
            return ob_get_clean();
        } else {
            throw new \Exception('Impossible de trouver la vue nommée ' . $fichier);
        }
    }
}
