# Modernisation technique des applications EKA

**Energy Kids Academy** · 3 applications · 15 août 2026

---

## Résumé

Les trois applications du système EKA ont été mises à niveau vers les standards techniques actuels — versions de PHP et de Symfony maintenues à long terme, dépendances à jour, et outillage de suivi de la qualité du code. Les trois sont en production depuis le 14 août 2026.

| Application | PHP | Framework / outillage | Statut |
|---|---|---|---|
| api.appli-v.net | 8.3 | Symfony 6.4 LTS | En production |
| appli-v | 8.3 | PHPStan | En production |
| EnergyAcademyClient | 8.3 | PHPStan | En production |

---

## api.appli-v.net — API

*API centrale (Symfony)*

- Migration progressive de Symfony 4.2 vers la version 6.4 LTS (support jusqu'à fin 2027), par paliers successifs pour sécuriser chaque étape.
- Mise à niveau de PHP 7.4 vers PHP 8.3 en cohérence avec la nouvelle version de Symfony.
- Dépendances tierces (authentification, cache, pagination, CORS…) mises à jour vers des versions activement maintenues.
- Mise en place d'une analyse statique du code (PHPStan), servant de référence pour le suivi qualité de l'équipe entrante.

Déployée en production le 14 août 2026.

## appli-v — Application principale

*Interface staff (PHP + jQuery + Vue.js)*

- Code mis en conformité avec PHP 8.3.
- Configuration de l'application centralisée dans un fichier dédié, à l'écart du code source.
- Gestion des sessions et des droits d'accès alignée sur les standards actuels.
- Mise en place d'une analyse statique du code (PHPStan).

Déployée en production le 14 août 2026.

## EnergyAcademyClient — Site client

*Espace famille & paiement en ligne*

- Code mis en conformité avec PHP 8.3.
- Configuration centralisée sur le même modèle que les autres applications.
- Gestion des sessions et du parcours de paiement alignée sur les standards actuels.
- Mise en place d'une analyse statique du code (PHPStan).

Déployée en production le 14 août 2026.

---

## Documentation livrée

- **Mise en production** — Procédure de bascule documentée pour `api.appli-v.net` (sauvegarde, étapes, retour arrière) ; bascule plus simple pour les deux autres applications, sans fichier dédié.
- **Qualité du code** — Un rapport d'analyse par application, point de référence pour le suivi dans le temps.
- **Dépôts** — L'ensemble des travaux est disponible sur les nouveaux dépôts GitHub (voir Hébergement du code ci-dessous).

## Hébergement du code

Les trois applications sont publiées sur de nouveaux dépôts GitHub (organisation `EKA2026MIGRATION`), avec un historique repartant de l'état actuel du code (post-modernisation), afin de constituer une base de référence propre et à jour pour la suite du projet et la passation à l'équipe entrante :

- `appli-v`
- `api-appli-v`
- `energykidsacademy.net`

---

*Rapport préparé le 15 août 2026 — EKA, chantier technique 2026*
