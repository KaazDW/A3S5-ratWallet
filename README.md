# ratWallet.

## Getting Started : Project Init

```
git clone https://github.com/KaazDW/A3S5-ratWallet.git
cd A3S5-ratWallet/
composer install
npm install
```

Créez une nouvelle bd name : `ratwallet`

```
php bin/console doctrine:migrations:migrate
symfony server:start
```

Sass compilation pour dev:

```
sass public\assets\scss\base.scss:public\assets\css\base.css -w
sass public\assets\scss\pages\accueil.scss:public\assets\css\pages\accueil.css -w
sass public\assets\scss\pages\dashboard.scss:public\assets\css\pages\dashboard.css -w
sass public\assets\scss\pages\login.scss:public\assets\css\pages\login.css -w
```

Librairie utilisés:

- ApexCharts
- PHPSpreadSheet
<br/><br/>

## Specifications

Sujet : Système de suivi des dépenses personnelles.
Listes des fonctionnalitées (attendu et réalisées) :
  - ✅ Login - Création, Suppression, Modifications de comptes
  - ✅ Enregistrement de plusieurs comptes bancaires
  - ✅ Enregistrement des dépenses par catégories (aliementation, loyer, ...)
  - ✅ Enregistrement des revenus (Salaire, APL, Aide au logement)
  - ✅ Définition d'objectif financiers sur chaque compte et affichage graphique
  - ✅ Définition d'objectif financiers sur chaque catégories et affichage graphique
  - ✅ Enregistrement et affichage de l'historique de chaques comptes
  - ✅ Fonctions de tri sur les historiques des comptes
  - ✅ Possibiltié de suppression d'une entrée depuis l'historiques des comptes
  - ✅ Exportation des données (Excel)
  - ✅ Dashboard avec graphiques
  - ✅ Sécurité des données (Middleware Symfony twig protège nativement des injections XSS)

<div align="center">
  <img src="https://github.com/KaazDW/A3S5-ratWallet/blob/master/DOC/cg_1.png">
  <img src="https://github.com/KaazDW/A3S5-ratWallet/blob/master/DOC/cg_2.png">
  <img src="https://github.com/KaazDW/A3S5-ratWallet/blob/master/DOC/cg_3.png">
  <img src="https://github.com/KaazDW/A3S5-ratWallet/blob/master/DOC/wireframes.png">
</div>
