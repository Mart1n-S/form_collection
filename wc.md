# 🧩 Comparaison : Web Components vs Twig Components (Symfony)

---

## 🌐 Web Components

### ✅ Avantages
- **Standard web natif** : utilisable dans n’importe quel framework (React, Vue, Angular, Symfony…)
- **Encapsulation forte** via le **Shadow DOM**
- Peut être **packagé et publié sur npm**
- Idéal pour créer un **design system transversal** et réutilisable dans plusieurs écosystèmes (frontend, CMS…)

### ❌ Inconvénients
- **Complexité élevée** : il faut penser à tout (structure, style, accessibilité, JS, lifecycle…)
- Manipulation **difficile** : le Shadow DOM isole le contenu du reste de la page (style, DOM, focus, JS)
- **Intégration dans Symfony complexe** :
  - Pas compatible avec `form_row`, `form_widget`, etc.
  - Symfony ne voit pas les champs internes → il faut gérer manuellement la **valeur**, la **soumission**, la **validation**

---

## 🧩 Twig Components (Symfony UX)

### ✅ Avantages
- **Intégration native** avec Symfony
- Facile à prendre en main pour les développeurs PHP/Twig
- Compatible avec le **système de formulaire Symfony** : `form_row`, `form_widget`, `form_start`, etc.
- Rendu **côté serveur** → rapide, bon pour le **SEO**
- Interactions possibles via **Stimulus/Turbo**
- Peut être packagé sous forme de **bundle Composer** et partagé entre projets Symfony

### ❌ Inconvénients
- Non utilisable hors Symfony/Twig
- Gestion des **assets CSS/JS à configurer manuellement**

---

## 🧠 En résumé

| Besoin                                       | Twig Components | Web Components      |
| -------------------------------------------- | --------------- | ------------------- |
| Intégration avec Symfony (formulaires, Twig) | ✅ Oui           | ❌ Non               |
| Réutilisable dans React / Vue / Angular      | ❌ Non           | ✅ Oui               |
| Facilité de mise en place                    | ✅ Facile        | ❌ Complexe          |
| Publication de composant                     | ✅ Composer      | ✅ npm               |
| Manipulation des données de formulaire       | ✅ Automatique   | ❌ À faire à la main |
| Encapsulation (Shadow DOM)                   | ❌ Non           | ✅ Oui               |

---

## 🎯 Conclusion

- **Twig Components** : Twig Components est probablement le meilleur choix si on veut aller vite. La prise en main est simple, surtout pour un dev Symfony.
 reste à valider la façon de packager ce design system sous forme de bundle Composer, pour pouvoir le maintenir proprement.

L'idée serait de :

Créer un bundle Symfony avec tous les composants Twig

L’installer via Composer dans nos projets Symfony

Et quand le design system évolue, on met simplement à jour le package (composer update) dans chaque projet

- **Web Components** : idéal si vous visez un design system **universel**, utilisable dans plusieurs stacks (Symfony, JS, CMS, etc.), au prix d'une complexité bien plus élevée.
