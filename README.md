# ratWallet.


## Getting Started : Project Init
```
git clone https://github.com/KaazDW/A3S5-ratWallet.git
cd A3S5-ratWallet/
composer install
composer require phpoffice/phpspreadsheet
```
Create new db named : "ratwallet"
```
php bin/console doctrine:schema:update -f
symfony server:start
```

dev sass compile command :
```
sass public\assets\scss\base.scss:public\assets\css\base.css -w                  
sass public\assets\scss\pages\accueil.scss:public\assets\css\pages\accueil.css -w
sass public\assets\scss\pages\dashboard.scss:public\assets\css\pages\dashboard.css -w
sass public\assets\scss\pages\login.scss:public\assets\css\pages\login.css -w   
```

## Notes
Important :
- ajout
- filtre complexe (filtre multiplle sur les objets - recherche texte, catégorie couplé)

Vu notre sujet :
- mettre l'accent sur les statistiques 
- il faut utilser impoerattivement doctrine de toutes facons.

Doc sur les components Symfony :
https://symfony.com/doc/current/templates.html#embedding-controllers

Doctrine :
> "En doctrine on s'en bas les couilles des id : `SELECT * FROM Student WHERE idschool=2;` deviens `$querybuilder("s")->andWhere(s.school=:idschool)->setParameter('idschool', 2);` `$querybuilder("student")->innerJoin("student.school")->innerJoin("school.city","city")->andWhere("city.name"=:name)->setparameter("name","Bourg-en-bresse")->getQuery()->getResult();`"


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
