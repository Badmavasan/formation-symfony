# Twig Syntax

## Afficher des Variables

Twig permet d'afficher des variables dans un template avec les doubles accolades {{ }}.

**Exemple :**

```
<p>{{ variable }}</p>
```

Si `variable = "Bonjour le monde"`, l'affichage sera :

```
<p>Bonjour le monde</p>
```

## Boucle for

La boucle for permet d'itérer sur des tableaux ou des objets.

**Syntaxe :**

```
{% for item in items %}
    <li>{{ item.name }}</li>
{% endfor %}
```

**Exemple avec un table de produits:**

```
<ul>
    {% for product in products %}
        <li>{{ product.name }} - {{ product.price }} €</li>
    {% endfor %}
</ul>
```

Si `products` est un tableau d'objets, cela va afficher chaque produit avec son nom et son prix.

**Boucle avec un index :**

```
{% for product in products %}
    <li>{{ loop.index }}. {{ product.name }}</li>
{% endfor %}
```

- `loop.index` : Donne l'index actuel, à partir de 1.
- `loop.index0` : Donne l'index actuel, à partir de 0.
- `loop.length` : Nombre total d'éléments.
- `loop.first` : Retourne true si c'est le premier élément.
- `loop.last` : Retourne true si c'est le dernier élément.


## Condition `if`

Twig permet de faire des conditions avec `if`, `elseif` et `else`.

**Syntaxe :**

```
{% if condition %}
    <!-- Si la condition est vraie -->
{% elseif autre_condition %}
    <!-- Si l'autre condition est vraie -->
{% else %}
    <!-- Sinon -->
{% endif %}
```

**Exemple :**

```
{% if product.stock > 0 %}
    <p>En stock</p>
{% else %}
    <p>Rupture de stock</p>
{% endif %}
```

- Conditions logiques : Vous pouvez utiliser des opérateurs comme `and`, `or`, `not` :

  ```
  {% if user.isAdmin and user.isLoggedIn %}
    <p>Bienvenue administrateur</p>
  {% endif %}
  ```

## Inclusion de Templates : include

Vous pouvez inclure des sous-templates pour réutiliser des parties de vos templates.

**Syntaxe :**

```
{% include 'header.html.twig' %}
```

Cela inclut le fichier `header.html.twig` dans votre template.

**Exemple avec des variables :**

```
{% include 'header.html.twig' with { 'title': 'Bienvenue' } %}
```

## Héritage de Templates : extends et block

L'héritage de templates est un concept clé dans Twig. Il permet de créer des layouts globaux et de les personnaliser dans des sous-templates.

**Syntaxe de base pour l'héritage :**

```
{% extends 'base.html.twig' %}
```

Dans le template parent (comme `base.html.twig`), vous définissez des blocs que les templates enfants peuvent remplacer.

**Exemple du template parent :**

```
<!DOCTYPE html>
<html>
    <head>
        <title>{% block title %}Mon Site{% endblock %}</title>
    </head>
    <body>
        <header>{% block header %}{% endblock %}</header>
        <main>{% block content %}{% endblock %}</main>
    </body>
</html>
```

**Exemple du template enfant :**

```
{% extends 'base.html.twig' %}

{% block title %}Page d'accueil{% endblock %}

{% block content %}
    <h1>Bienvenue sur la page d'accueil</h1>
{% endblock %}
```

- `extends` : Hérite du template parent.
- `block` : Définit une section remplaçable dans les templates enfants.

# Filtre

Les filtres permettent de modifier la sortie d'une variable. Ils sont appliqués avec le symbole `|`.

** Exemple avec le filtre `upper` :**

```
<p>{{ 'bonjour'|upper }}</p>
```

Cela affichera `BONJOUR` 

**Autres filtres courants :**

  - date : Formater les dates.

    ```
    {{ user.createdAt|date('d/m/Y') }}
    ```
    Affiche la date au format jour/mois/année.

  - length : Donne la longueur d'un tableau ou d'une chaîne de caractères.
    ```
    {{ users|length }} utilisateurs inscrits.
    ```
    
  - default : Afficher une valeur par défaut si la variable est vide ou nulle.
    ```
    {{ product.name|default('Produit sans nom') }}
    ```

## Filtres conditionnels : ternary

Le filtre ternary permet d’écrire des conditions simples sur une seule ligne, comme un `if` court.

**Exemple :**
```
{{ product.stock > 0 ? 'En stock' : 'Rupture de stock' }}
```

Cela retourne "En stock" si la quantité est supérieure à zéro, sinon "Rupture de stock".

 ##Boucle sur les objets clés-valeurs

Lorsque vous bouclez sur un tableau associatif (clé-valeur), vous pouvez récupérer à la fois la clé et la valeur.

**Exemple :**

```
{% for key, value in product %}
    <p>{{ key }}: {{ value }}</p>
{% endfor %}
```

Cela affiche chaque clé et valeur du tableau

## Tests

Les tests permettent de vérifier des conditions spécifiques dans Twig. Par exemple, si une variable est vide ou définie.

**Tests courants :**

- empty : Vérifie si une variable est vide.
  ```
  {% if products is empty %}
    <p>Pas de produits disponibles.</p>
  {% endif %}
  ```
- defined : Vérifie si une variable est définie.
  ```
  {% if product is defined %}
    <p>{{ product.name }}</p>
  {% endif %}
  ```
- iterable : Vérifie si une variable est itérable (comme un tableau).
  ```
  {% if products is iterable %}
      {% for product in products %}
          {{ product.name }}
      {% endfor %}
  {% endif %}
  ```


