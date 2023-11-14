# ratWallet.


## Getting Started : Project Init
```
git clone https://github.com/KaazDW/A3S5-ratWallet.git
cd A3S5-ratWallet/
composer install
```
> \> Create new db named : "ratwallet"
```
php bin/console doctrine:schema:update -f
symfony server:start
```

> dev sass compile command
```
sass public\assets\scss\base.scss:public\assets\css\base.css -w                  
sass public\assets\scss\pages\accueil.scss:public\assets\css\pages\accueil.css -w
sass public\assets\scss\pages\dashboard.scss:public\assets\css\pages\dashboard.css -w
```
## Specifications

Sujet : Système de suivi des dépenses personnelles.
- Login
    - Enregistrement de plusieurs comptes bancaires
    - Enregistrement des dépenses par catégories (aliementation, loyer, ...)
    - Enregistrement des revenus (Salaire, APL, Aide au logement)
    - Définition des budgets attendus dans chaque catégorie
    - Définition d'objectif financiers sur chaque compte
    - Exportation des données sous format EXCEL (pourquoi pas importation également)
    - Dashboard avec graphiques
    - Gestion des remboursements (ex: a rembourser : 30€ a Pierre, 12€ a .... etc etc)
- Responsive primordiale car utilisation sur mobile important
- Sécurité des données 

<div align="center">
  <img src="https://github.com/KaazDW/A3S5-ratWallet/blob/master/DOC/cg_1.png">
  <img src="https://github.com/KaazDW/A3S5-ratWallet/blob/master/DOC/cg_2.png">
  <img src="https://github.com/KaazDW/A3S5-ratWallet/blob/master/DOC/cg_3.png">
</div>
