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
    private $fichier;
    // titre de la vue
    private $title;

    public function __construct($action)
    {
        // La vue à générer dépend de l'action demandée
        $this->fichier = "views/view" . $action . ".php";
    }

    /*
     * Génère et affiche la vue
     */

    public function generer($donnees = null)
    {
        // Génération de la partie spécifique de la vue
        $content = $this->genererFichier($this->fichier,
                $donnees);
        // utilisation du template avec chargement des données spécifiques
        $vue = $this->genererFichier(__DIR__ . '/viewTemplate.php',
                ['title' => $this->title,
            'content' => $content]);
        // Renvoi de la vue générée au navigateur
        echo $vue;
    }

    /*
     * Génère et retourne la vue générée
     */

    private function genererFichier($fichier, $donnees)
    {
        if (file_exists($fichier)) {
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
            throw new Exception('Impossible de find a view named ' . $fichier);
        }
    }

}
