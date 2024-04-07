<?php

namespace Semeformation\Mvc\Cinema_crud\includes;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Utils
{
    /**
     * affiche le résultat d'une opération d'extraction
     * @param array|scalar $unResultat
     * @param string $uneLegende
     */
    public static function afficherResultat($unResultat, $uneLegende = "")
    {
        $event = 'onmouseover="basculeTitreValeur(this)" onmouseout="basculeTitreValeur(this)"';
        echo <<<HTML
                <style>
                    table.debug { margin-top: 1em; border-collapse: collapse; }
                    table.debug th, table.debug td { border: solid #444 1px; padding: 10px; font-family: Verdana, Tahoma, Helvetica, Arial; }
                    table.debug th { background-color: silver; color: #444; }
                    table.debug td { cursor : help; }
                    table.debug td.scalaire { border: dashed silver 1px; }
                </style>
                <script type="text/javascript">
                 function basculeTitreValeur(unElt)
                 {
                     var tempo = unElt.innerHTML;
                     unElt.innerHTML = unElt.title;
                     unElt.title = tempo;
                     if (unElt.style.backgroundColor != "Salmon") {
                        unElt.style.backgroundColor= "Salmon";
                     } else {
                        unElt.style.backgroundColor= "white";
                     }
                 }
                </script>
                <table class="debug">
                    <caption>$uneLegende</caption>

            HTML;
        if (is_array($unResultat)) {
            if (
                count($unResultat) != count(
                    $unResultat,
                    COUNT_RECURSIVE
                )
            ) {
                self::afficherNxN(
                    $unResultat,
                    $event
                ); // tableau à 2 dimensions
            } else {
                self::afficher1xN(
                    $unResultat,
                    $event
                ); // tableau à 1 dimension
            }
        } else {
            if ($unResultat) { // valeur unique
                echo "<tr><td class=\"scalaire\" title=\"var\" {$event}>$unResultat</td></tr>\n";
            } else { // valeur null
                echo "<tr><td class=\"scalaire\" title=\"null\" {$event}></td></tr>\n";
            }
        }
        echo "</table>";
    }

    /**
     * Undocumented function
     *
     * @param array $unResultat
     * @param string $unEvent
     * @return void
     */
    private static function afficherNxN($unResultat, $unEvent)
    {
        $entete = true;
        $i = 0;
        foreach ($unResultat as $uneLigne) {
            $noms = "";
            $valeurs = "";
            foreach ($uneLigne as $nom => $valeur) {
                if ($entete) {
                    $noms .= "<th>['{$nom}']</th>\n";
                }
                $valeur = htmlentities(
                    $valeur,
                    ENT_COMPAT,
                    "utf-8"
                );
                $valeurs .= "<td title=\"array[{$i}]['{$nom}']\" {$unEvent}>{$valeur}</td>\n";
            }
            if ($entete) {
                $entete = false;
                echo "<tr><th></th>{$noms}</tr>\n";
            }
            echo "  <tr><th>[{$i}]</th>{$valeurs}</tr>";
            $i++;
        }
    }

    /**
     * Affiche les champs d'un tableau à 1 dimension
     *
     * @param array $unResultat
     * @param string $unEvent
     * @return void
     */
    private static function afficher1xN($unResultat, $unEvent)
    {
        foreach ($unResultat as $nom => $valeur) {
            $valeur = htmlentities(
                $valeur,
                ENT_COMPAT,
                "utf-8"
            );
            echo "<tr><th>['$nom']</th>\n";
            echo "<td title=\"array['$nom']\" {$unEvent}>{$valeur}</td></tr>\n";
        }
    }
}
