# Projet ANC3 2425 - Groupe c08 - Excel

## Notes de version itération 1

### Liste des bugs connus

* Erreur SYNTAX_ERROR : quand on mets "= A1 + A2" (exemple : A1 vaut true et A2 vaut 10), l'application plante mais "= true + 1O", l'erreur s'affiche bien.

### Liste des fonctionnalités supplémentaires

### Divers

* Architecte MVVM non respecté
* Le diagramme de classe peut être meilleur
* Option 1 pour lancer le projet

## Notes de version itération 2

...

## Notes de version itération 3

...


## Pour lancer le projet

### Option 1 

Dans le menu d'exécution, ne pas choisir "Current File" mais "App"

### Option 2

Dans VM options, ajouter ça : 

```
--add-exports=javafx.base/com.sun.javafx.event=org.controlsfx.controls
--add-exports=javafx.controls/com.sun.javafx.scene.control.behavior=org.controlsfx.controls
```

Source : https://github.com/controlsfx/controlsfx/wiki/Using-ControlsFX-with-JDK-9-and-above 

### Option 3

Dans la console de maven, tapper `mvn javafx:run`