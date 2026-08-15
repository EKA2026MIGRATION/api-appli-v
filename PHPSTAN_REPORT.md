# Rapport PHPStan — api.appli-v.net

## Chiffres clés

- **599 erreurs** au total, gelées dans `phpstan-baseline.neon`.
- **Niveau utilisé : 3**, pas le niveau max prévu au plan initial. Sur 463 fichiers / ~77 000 lignes de legacy jamais analysées, le niveau max noierait le rapport sous du bruit de typage strict sans plus-value immédiate. Le niveau 3 attrape déjà les vraies classes de bugs (voir ci-dessous) sans ce bruit. **Trajectoire suggérée** : monter progressivement (4 → 5 → ... → max) au fil du refactoring de la Phase 4, pas d'un coup.
- Scope : `src/` + `tests/`. `patched/c975l/*` (bundles forkés, montés en path-repositories) est **exclu**, traité comme du code tiers au même titre que `vendor/`.
- Mécanisme : `phpstan-baseline.neon` gèle l'état actuel. `vendor/bin/phpstan analyse` est **vert** — seules les erreurs **nouvelles** feront échouer une future exécution.

## Répartition par catégorie

| Identifiant PHPStan | Groupes | Occurrences | Priorité |
|---|---|---|---|
| `doctrine.associationType` | 137 | 137 | Basse (voir note ci-dessous) |
| `doctrine.columnType` | 131 | 131 | Basse (voir note ci-dessous) |
| `variable.undefined` | 90 | 113 | **Haute** |
| `assign.propertyType` | 57 | 57 | Basse (voir note ci-dessous) |
| `property.notFound` | 17 | 32 | **Haute** |
| `return.type` | 28 | 29 | Moyenne |
| `parameter.phpDocType` | 23 | 23 | Basse (docblocks) |
| `return.phpDocType` | 6 | 21 | Basse (docblocks) |
| `parameter.notFound` | 10 | 12 | Moyenne (docblocks obsolètes) |
| `return.missing` | 11 | 11 | Moyenne |
| `method.notFound` | 7 | 9 | Moyenne |
| `arguments.count` | 7 | 9 | Moyenne |
| autres (`phpDoc.parseError`, `empty.variable`, `method.nonObject`, `isset.variable`, `constant.notFound`, `array.duplicateKey`, `class.nameCase`) | 9 | 12 | Variable |

### Pourquoi `doctrine.columnType` / `doctrine.associationType` / `assign.propertyType` restent "basse priorité" (325 erreurs)

Ce sont très majoritairement des mismatches de nullabilité entre le mapping Doctrine et le type PHP déclaré (colonne nullable en base, propriété PHP non-nullable, ou l'inverse selon les tables). Pas un bug fonctionnel — Doctrine hydrate correctement dans les deux cas. Ratio bug-réel/volume faible sur ces trois catégories — candidat pour un nettoyage ciblé en Phase 4, pas urgent.

### Pourquoi `variable.undefined` et `property.notFound` sont "haute priorité"

- `property.notFound` (ex. `PersonService::$phoneService`, `RegistrationService::$cascadeService`/`$personService`, `CredentialService::$staffService`/`$userService`) : dépendances référencées dans le code mais jamais déclarées/injectées dans la classe. Soit une vraie dépendance manquante (bug), soit du code mort — à trancher au cas par cas.
- `variable.undefined` (la plus grosse catégorie, concentrée dans `StatistiqueService.php`, `RideService.php`, `PickupService.php`) : variables potentiellement non définies sur un chemin d'exécution donné. Demande de lire chaque méthode pour distinguer un vrai bug d'un faux positif lié au flux de contrôle — pas une correction mécanique, à traiter en chantier dédié.

## Fichiers les plus concernés (nombre de groupes d'erreurs distincts)

1. `src/Service/StatistiqueService.php` — 18
2. `src/Entity/StockProduct.php` — 16
3. `src/Entity/StockProductInventory.php` — 15
4. `src/Entity/StockOrder.php` — 14
5. `src/Entity/Registration.php` — 14
6. `src/Service/RideService.php` — 13
7. `src/Entity/StaffPresence.php` — 13
8. `src/Entity/Product.php` — 13
9. `src/Entity/PickupActivity.php` — 13
10. `src/Entity/ChildPresence.php` — 13
11. `src/Service/PickupService.php` — 11
12. `src/Entity/SurveySession.php` — 10
13. `src/Entity/Staff.php` — 10
14. `src/Entity/Ride.php` — 10
15. `src/Entity/Pickup.php` — 9

## Comment l'utiliser

```bash
# Lancer l'analyse (doit rester vert)
php8.3 vendor/bin/phpstan analyse

# Après un chantier de fix, régénérer le baseline pour capturer la réduction
php8.3 vendor/bin/phpstan analyse --generate-baseline=phpstan-baseline.neon

```

Config : `phpstan.neon` (racine du repo). Bootstrap Doctrine pour la validation DQL/QueryBuilder : `.phpstan/object-manager.php` (boote le kernel en mode `dev`, hors scope d'analyse). Dump du conteneur Symfony requis par `phpstan-symfony` : `var/cache/dev/App_KernelDevDebugContainer.xml` (régénéré automatiquement par `bin/console cache:warmup`, à refaire si le conteneur change significativement).

Pas de CI/CD dans ce repo à ce jour (cf. `CUTOVER.md`) — cette commande est à lancer manuellement pour l'instant, mais prête à être branchée sur un futur pipeline sans changement.
