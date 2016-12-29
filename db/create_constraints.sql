USE `cinema_crud`;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `prefere`
--
ALTER TABLE `prefere`
  ADD CONSTRAINT `fk_prefere_film` FOREIGN KEY (`FILMID`) REFERENCES `film` (`FILMID`),
  ADD CONSTRAINT `fk_prefere_utilisateur` FOREIGN KEY (`USERID`) REFERENCES `utilisateur` (`USERID`);

--
-- Contraintes pour la table `seance`
--
ALTER TABLE `seance`
  ADD CONSTRAINT `fk_seance_cinema` FOREIGN KEY (`CINEMAID`) REFERENCES `cinema` (`CINEMAID`),
  ADD CONSTRAINT `fk_seance_film` FOREIGN KEY (`FILMID`) REFERENCES `film` (`FILMID`);
