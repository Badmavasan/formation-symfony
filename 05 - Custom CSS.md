## Étape 1 : Ajouter le Fichier CSS dans le Répertoire Public

Placez votre fichier CSS dans le répertoire `public` de votre projet Symfony. Par exemple :

```
/public/css/style.css
```

## Étape 2 : Référencer le Fichier CSS dans Votre Template Twig

Dans votre template Twig (par exemple, `base.html.twig`), vous pouvez inclure le fichier CSS comme suit :

```
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{% block title %}Mon Application Symfony{% endblock %}</title>

    {# Lien vers le fichier CSS #}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {% block stylesheets %}{% endblock %}
</head>
<body>
    {% block body %}{% endblock %}
</body>
</html>
```

Étape 3 : Étendre le Template de Base dans d'Autres Templates Twig

Si vous avez un template de base (`base.html.twig`), vous pouvez l’étendre dans vos autres templates Twig et ajouter des fichiers CSS spécifiques si nécessaire.

Par exemple, dans un template `homepage.html.twig` :

```
{% extends 'base.html.twig' %}

{% block title %}Page d'Accueil{% endblock %}

{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
{% endblock %}

{% block body %}
    <h1>Bienvenue sur la Page d'Accueil</h1>
    <p>Voici le contenu de la page d'accueil.</p>
{% endblock %}
```

Ici, `homepage.css` est un fichier CSS supplémentaire qui s'appliquera uniquement à cette page spécifique. L'appel de `{{ parent() }}` garantit que le fichier CSS de base `style.css` est également inclus.

