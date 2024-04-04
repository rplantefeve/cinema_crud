# Cinéma CRUD

## Déploiement

- Décompresser le zip dans votre répertoire de travail (ex. : ``c:/users/romain/source/repos/php/``)
- Créer un virtual host nommé ``cinema.local`` qui pointe vers ``c:/users/romain/source/repos/php/cinema_crud/src`` via l'assistant de Wamp
- Exécuter les scripts de BDD contenus dans ``db/``
    - D'abord ``create_base.sql``
    - Puis ``insert_data.sql``
    - Enfin ``create_constraints.sql``
- Rendez-vous à l'URL http://cinema.local

## Exercices

### Exercice 01

La liste des cinémas ne s'affiche pas bien. Faites les modifications nécessaires dans `cinemasList.php` pour corriger ce problème.
