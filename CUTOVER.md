# Cutover prod — api.appli-v.net (Symfony 4.2 → 6.4 LTS / PHP 7.4 → 8.3)

Checklist à dérouler **dans l'ordre** pour le passage en prod. Le déploiement est manuel (pas de CI/CD) : renommage de l'ancien dossier prod en backup, puis copie du contenu de ce dossier migré par-dessus.

Ne pas cocher une étape avant de l'avoir vérifiée. En cas de blocage, voir la section **Rollback** en bas.

## 0. Avant de commencer

- [ ] Dump de sécurité de la base de données prod (`mysqldump`), horodaté, stocké hors du serveur.
- [ ] Renommer le dossier prod actuel (ex. `api.appli-v.net` → `api.appli-v.net.bak-YYYYMMDD`), **ne pas le supprimer**.
- [ ] Copier le contenu de la branche `finalUpdate` par-dessus (décision : `master` reste inchangé, il sert de filet de sécurité/rollback — pas de merge).

## 1. Fichiers à NE PAS écraser par la copie

Ces fichiers sont gitignorés — absents du contrôle de version, spécifiques à l'environnement. Une copie brute du dossier local migré les écraserait avec des valeurs **dev**, pas prod :

- [ ] **`.env`** — ne pas copier celui du poste de dev. Le `.env` prod garde ses propres valeurs (voir section 2 pour les mises à jour à y faire).
- [ ] **`config/jwt/private.pem` + `config/jwt/public.pem`** — ⚠️ **CRITIQUE** : si ces clés sont écrasées par celles du dev, **tous les tokens JWT déjà émis en prod deviennent invalides instantanément** → déconnexion immédiate de tous les utilisateurs actifs (appli-v + toute intégration côté client). Conserver précieusement les clés prod existantes.
- [ ] **`config/bundles.php`** et **`config/config_bundles.yaml`** — gitignorés, maintenus à la main par environnement (cf. `ReadMe` du repo). Vérifier qu'ils sont cohérents avec les bundles réellement utilisés par cette version (comparer avec le `config/bundles.php` local si besoin, mais ne pas écraser aveuglément).

Recommandé : sauvegarder ces fichiers depuis l'ancien dossier prod (déjà renommé en backup à l'étape 0) avant de lancer la copie, puis les recopier par-dessus le nouveau dossier juste après.

## 2. Valeurs à poser dans le `.env` prod après la copie

| Variable | Action | Détail |
|---|---|---|
| `APP_SECRET` | **Remplacer** | Nouvelle valeur générée : `472713d50a839b34e879e1fad146431c` (32 hex, format standard Symfony). L'ancienne valeur était exposée dans `.env.dist` versionné depuis 2018 — rotation obligatoire, pas optionnelle. |
| `CORS_ALLOW_ORIGIN` | **Remplacer** | Valeur actuelle en prod = `''` (vide) → résout en `$` → matche **toutes** les origines. Poser une regex ancrée, ex. `^https://(appli-v\.net\|energykidsacademy\.net)$` (adapter aux domaines prod réels). |
| Mot de passe BDD prod | **Changer** | Le mot de passe actuel (`root2`) a été exposé — à régénérer côté MySQL/Plesk, puis répercuter dans `DATABASE_URL` du `.env` prod. |
| `APP_ENV` | **Vérifier = `prod`** | Actuellement `env` (probable typo/valeur incorrecte) sur le serveur — doit être exactement `prod`. C'est ce qui charge `config/packages/prod/*.yaml`. |
| `MAILER_DSN` | **Confirmer** | Local = `null://null` (emails désactivés). Vérifier que c'est bien la valeur voulue en prod, ou renseigner le vrai DSN SMTP si l'envoi d'email doit fonctionner. |

**Variables requises pour que l'app boote en `APP_ENV=prod`** (confirmé en local en simulant un environnement prod complet) : `APP_ENV`, `APP_SECRET`, `DATABASE_URL`, `CORS_ALLOW_ORIGIN`, `MAILER_DSN`, `TRUSTED_HOSTS`. Si l'une manque, l'app lève une `EnvNotFoundException` (500 immédiat, pas de page blanche silencieuse une fois `APP_DEBUG=0` — vérifier les logs `var/log/prod.log` le cas échéant).

## 3. Après la copie

- [ ] Restaurer `.env`, `config/jwt/*.pem`, `config/bundles.php`, `config/config_bundles.yaml` depuis le backup (étape 1), avec les valeurs mises à jour de la section 2.
- [ ] `composer install --no-dev --optimize-autoloader` (si le dossier copié inclut encore les dépendances dev, ou si `vendor/` n'a pas été copié).
- [ ] `php bin/console cache:clear --env=prod --no-debug`.
- [ ] Vérifier les permissions de `var/` pour l'utilisateur du serveur web (ACL type `setfacl -R -m u:www-data:rwX var`, cf. setup local) + lecture de `config/jwt/private.pem` par ce même utilisateur.
- [ ] Tester les routes clés directement sur le serveur (curl ou navigateur) :
  - `GET /` → 200 (pas de 500)
  - `POST /user/api/authenticate` avec de vrais identifiants → 200 + token
  - Une route authentifiée (ex. `/invoice/list`) avec le token → 200/403 selon les droits, jamais 500
- [ ] Vérifier `var/log/prod.log` : aucune exception fraîche après ce cycle de test.
- [ ] Vérifier qu'`appli-v` (qui consomme cette API) fonctionne toujours normalement : login + une page qui appelle l'API.

## 4. Rollback

Si un point critique casse après bascule :

1. Repointer le vhost / DocumentRoot vers l'ancien dossier renommé en backup (étape 0) — pas de perte, il n'a pas été touché.
2. Si la base de données a été modifiée entre-temps (migrations Doctrine exécutées), évaluer si un rollback DB (dump de l'étape 0) est nécessaire ou si les migrations sont rétro-compatibles avec l'ancien code.
3. Ne supprimer le dossier backup qu'après une période de validation en prod jugée suffisante (pas le jour même).
