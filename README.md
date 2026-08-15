# api-appli-v

API centrale d'Energy Kids Academy — Symfony, authentification JWT, Doctrine ORM. Consommée par [`appli-v`](https://github.com/EKA2026MIGRATION/appli-v) (interface staff) et [`energykidsacademy.net`](https://github.com/EKA2026MIGRATION/energykidsacademy.net) (espace famille).

## Stack

- PHP 8.3
- Symfony 6.4 LTS (support jusqu'à fin 2027)
- Doctrine ORM, authentification JWT (LexikJWTAuthenticationBundle)

## Installation

```bash
composer install
```

Configuration : copier `.env.dist` en `.env` et renseigner les valeurs (base de données, `APP_SECRET`, `CORS_ALLOW_ORIGIN`...).

Fichiers spécifiques à l'environnement, non versionnés — à fournir séparément par environnement :
- `.env` (secrets, config infra)
- `config/jwt/private.pem` + `config/jwt/public.pem` (clés de signature JWT — **ne jamais régénérer en prod**, ça invaliderait tous les tokens déjà émis)
- `config/bundles.php`, `config/config_bundles.yaml` (bundles actifs par environnement)

```bash
php bin/console cache:clear --env=prod --no-debug
```

## Déploiement

Voir `CUTOVER.md` pour la procédure complète de mise en production (fichiers à ne pas écraser, valeurs `.env` attendues, checklist de vérification, rollback).

## Qualité du code

Analyse statique via PHPStan (niveau 3, cf. `PHPSTAN_REPORT.md`) :

```bash
vendor/bin/phpstan analyse
```
