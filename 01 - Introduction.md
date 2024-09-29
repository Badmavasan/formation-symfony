# Concept de MVC avec Diagramme

Le modèle MVC (Model-View-Controller) est une architecture logicielle utilisée pour séparer les différentes parties d'une application.

1. Modèle (Model) : Il contient la logique métier, les données, et gère les interactions avec la base de données ou d'autres sources de données
2. Vue (View) : Il s'agit de la partie visible par l'utilisateur, autrement dit, l'interface utilisateur (UI). Elle affiche les données que le modèle fournit
3. Contrôleur (Controller) : Il agit comme intermédiaire entre le modèle et la vue. Il prend les demandes de l'utilisateur, interagit avec le modèle, et renvoie les données formatées à la vue.

Voici un diagramme simple :

```
+------------------+       +------------------+         +------------------+
|    View (Vue)    | <---> | Controller (Crtl) | <---> |   Model (Modèle)  |
+------------------+       +------------------+         +------------------+
```

# Avantages du Design Pattern MVC

1. Séparation des responsabilités : MVC permet de séparer la logique métier (modèle), la logique de présentation (vue), et la gestion des interactions (contrôleur).
2. Modularité : Les composants sont modulaires, ce qui facilite la maintenance, la modification et la mise à jour du code.
3. Testabilité : Les tests unitaires sont plus faciles à mettre en œuvre car chaque couche (modèle, vue, contrôleur) peut être testée indépendamment.
4. Réutilisabilité : Les vues peuvent être réutilisées pour afficher des informations différentes, les contrôleurs peuvent réutiliser les modèles pour différentes actions.

# Correspondance en Symfony

1. Modèle (Model) : En Symfony, le modèle est représenté par les Entités (Entities) et Repositories. Ce sont des classes qui définissent les structures des données et interagissent avec la base de données.
2. Vue (View) : Les vues sont gérées par les fichiers Twig. Twig est le moteur de template de Symfony, utilisé pour rendre les données et les affichages HTML.
3. Contrôleur (Controller) : Les contrôleurs en Symfony sont des classes dans le dossier Controller qui gèrent les requêtes HTTP, appellent les modèles et renvoient une réponse au client via la vue.

# Explication des Concepts

1. Contrôleur (Controller) : Un contrôleur est une classe qui gère les requêtes HTTP. Il reçoit les données envoyées par l'utilisateur, appelle les services ou modèles nécessaires, et retourne une réponse. Par exemple, un contrôleur peut extraire des données de la base, les formater, et les passer à une vue Twig pour affichage.
2. Modèle (Model) : Le modèle représente la structure des données dans l'application. Dans Symfony, il est souvent représenté par des Entités qui définissent les propriétés et les relations des objets.
3. Vue (View) : Une vue est le rendu visuel des données. En Symfony, les fichiers Twig permettent de structurer le contenu HTML tout en intégrant les variables et boucles pour afficher dynamiquement les données.

# Exemple : Contrôleur, Modèle et Vue Twig en Symfony

  1. Contrôleur :

  ```
  // src/Controller/ProductController.php
  namespace App\Controller;
  
  use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
  use Symfony\Component\HttpFoundation\Response;
  use App\Repository\ProductRepository;
  
  class ProductController extends AbstractController
  {
      public function index(ProductRepository $productRepository): Response
      {
          $products = $productRepository->findAll();
  
          return $this->render('product/index.html.twig', [
              'products' => $products,
          ]);
      }
  }
  ``` 

  2. Modèle (Entity) :

  ```
  // src/Entity/Product.php
  namespace App\Entity;
  
  use Doctrine\ORM\Mapping as ORM;
  
  #[ORM\Entity(repositoryClass: ProductRepository::class)]
  class Product
  {
      #[ORM\Id]
      #[ORM\GeneratedValue]
      #[ORM\Column(type: 'integer')]
      private $id;
  
      #[ORM\Column(type: 'string', length: 255)]
      private $name;
  
      // Getters and setters...
  }
  ```

  3. Vue (Twig) :

  ```
  {# templates/product/index.html.twig #}
  <h1>Product List</h1>
  
  <ul>
      {% for product in products %}
          <li>{{ product.name }}</li>
      {% endfor %}
  </ul>
  ```

# Exercice : Afficher des Données en Twig sans Base de Données

1. Créer un contrôleur qui envoie des données statiques à une vue Twig.
2. Dans le contrôleur, simuler une liste d'objets produits et les passer à la vue.

**Exercice :**

- Créer un contrôleur `ProductController`.
- Simuler une liste de produits dans le contrôleur.
- Afficher la liste dans un fichier `index.html.twig`.

# Initialisation de la Base de Données

1. Configurer la Base de Données : Modifier le fichier `.env` pour établir la connexion avec la base de données :

```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/symfony_db"
```

2. Créer la Base de Données : Utiliser la commande Symfony :

```
php bin/console doctrine:database:create
```

3. Créer des Entités : Utiliser la commande pour créer une entité :

```
php bin/console make:entity
```

4. Migrer les Entités vers la Base de Données : Après avoir créé ou modifié des entités, exécuter une migration :

```
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

# Diagramme : Repository et Relation avec le Contrôleur

```
+-------------------+       +--------------------+       +------------------+
|   Controller      | ----> |   Repository       | ----> |    Database       |
+-------------------+       +--------------------+       +------------------+
```

**Repository** : Un repository est une classe qui encapsule la logique de requête de la base de données. Il permet au contrôleur d'accéder aux données sans connaître les détails d'implémentation.

# Méthodes par Défaut dans un Repository Doctrine

1. `find($id)` : Trouve une entité par son identifiant.

```
$product = $productRepository->find($id);
```
  - Renvoie une entité ou `null` si elle n'existe pas.

2. `findAll()` : Renvoie toutes les entités de la table associée.

```
$products = $productRepository->findAll();
```
  - Renvoie un tableau d'entités.

3. `findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)` : Trouve des entités selon des critères spécifiques.

```
$products = $productRepository->findBy(['category' => 'electronics'], ['price' => 'ASC'], 10, 0);
```
  - `criteria` : Un tableau de conditions pour filtrer les résultats.
  - `orderBy` : Un tableau pour définir l'ordre des résultats (ex: `['price' => 'ASC']`).
  - `limit` : Le nombre maximum d'entités à retourner.
  - `offset` : Le décalage à partir duquel commencer à récupérer les entités.

4. `findOneBy(array $criteria, array $orderBy = null)` : Trouve une seule entité selon des critères spécifiques.

```
$product = $productRepository->findOneBy(['name' => 'iPhone 12']);
```
  - Renvoie une entité ou `null` si aucune entité ne correspond aux critères.

5. `count(array $criteria)` : Compte le nombre d'entités correspondant à un certain critère.

```
count(array $criteria) : Compte le nombre d'entités correspondant à un certain critère.
```
  - Renvoie un entier correspondant au nombre d'entités correspondant aux critères.

# Exemple de Requête Custom

Parfois, les méthodes de base ne suffisent pas, vous pouvez alors créer des méthodes personnalisées en utilisant le QueryBuilder :

```
// src/Repository/ProductRepository.php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findProductsByPriceRange($minPrice, $maxPrice)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.price >= :minPrice')
            ->andWhere('p.price <= :maxPrice')
            ->setParameter('minPrice', $minPrice)
            ->setParameter('maxPrice', $maxPrice)
            ->orderBy('p.price', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
```

Ici, la méthode `findProductsByPriceRange` recherche des produits dans une fourchette de prix donnée.

Utilisation dans le contrôleur :

```
$products = $productRepository->findProductsByPriceRange(100, 500);
```

# CRUD (sans Read)

1. Insertion de Données

Pour insérer une nouvelle entité dans la base de données, on utilise la méthode `persist()` suivie de `flush()` pour envoyer les modifications à la base de données.

**Exemple d'insertion :**

```
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function create(EntityManagerInterface $entityManager): Response
    {
        // Créer un nouveau produit
        $product = new Product();
        $product->setName('New Product');
        $product->setPrice(100);

        // Persister l'entité
        $entityManager->persist($product);
        // Appliquer les changements dans la base de données
        $entityManager->flush();

        return new Response('Product created with ID: '.$product->getId());
    }
}
``` 

  - `persist()` : Prépare l'entité pour être insérée dans la base de données.
  - `flush()` : Exécute toutes les opérations en attente, comme l'insertion des entités dans la base de données.

**Autre méthode :**

Si vous utilisez le repository directement, vous pouvez appeler `save()` (méthode à créer dans votre repository) qui va gérer le persist et flush automatiquement.

```
// src/Repository/ProductRepository.php
namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}

// Utilisation dans le contrôleur
$productRepository->save($product);
```

2. Mise à Jour de Données

Pour mettre à jour une entité, on n'a pas besoin d'appeler `persist()` si l'entité provient déjà de la base de données (par exemple via `find()`). Il suffit de modifier l'entité, puis de faire un `flush()` pour appliquer les modifications.

**Exemple de mise à jour :**

```
// src/Controller/ProductController.php
namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function update(int $id, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        // Trouver le produit existant
        $product = $productRepository->find($id);
        if (!$product) {
            return new Response('Product not found.');
        }

        // Modifier le produit
        $product->setPrice(200);

        // Inutile de faire un persist() car l'entité est déjà gérée par Doctrine
        $entityManager->flush();

        return new Response('Product updated');
    }
}
```

  - `flush()` : Applique les changements à la base de données. `persist()` n'est pas nécessaire si l'entité est déjà gérée par Doctrine (c’est-à-dire qu'elle a été récupérée par `find()` ou `findBy()`).

3. Suppression de Données

Pour supprimer une entité de la base de données, on utilise remove() suivi de flush().

**Exemple de suppression :**

```
// src/Controller/ProductController.php
namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function delete(int $id, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        // Trouver le produit à supprimer
        $product = $productRepository->find($id);
        if (!$product) {
            return new Response('Product not found.');
        }

        // Supprimer l'entité
        $entityManager->remove($product);
        $entityManager->flush();

        return new Response('Product deleted');
    }
}
```

  - `remove()` : Prépare l'entité pour être supprimée de la base de données.
  - `flush()` : Applique la suppression dans la base de données.

# Récupérer des Données à partir de Variables dans l'URL (Path Variables)

Les variables de chemin (path variables) sont des parties dynamiques de l'URL que Symfony peut associer à des paramètres de méthode dans un contrôleur. Vous définissez ces variables dans les routes via les annotations ou la configuration des routes, et Symfony extrait automatiquement les valeurs des parties dynamiques de l'URL.

**Exemple de Route avec Path Variable**

Prenons un exemple où vous voulez récupérer un ID depuis l'URL pour afficher les détails d'un produit.

```
// src/Controller/ProductController.php
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;

class ProductController extends AbstractController
{
    #[Route('/product/{id}', name: 'product_show')]
    public function show(Product $product): Response
    {
        // Symfony injecte automatiquement l'entité "Product" correspondant à l'ID dans l'URL
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
```
**Explication :**
  - `{id}` dans la route `/product/{id}` est une variable de chemin. Elle capture la partie de l'URL qui suit `/product/` et la transmet à la méthode `show()` du contrôleur.
  - Symfony sait que la variable de chemin `id` correspond à un entier et utilise l'ID pour charger automatiquement l'entité Product à partir de la base de données grâce à l'auto-injection des entités.

**Autre Exemple : Sans Auto-Injection de l'Entité**

Si vous ne voulez pas que Symfony injecte automatiquement l'entité, vous pouvez récupérer l'ID de manière classique :

```
#[Route('/product/{id}', name: 'product_show')]
public function show(int $id, ProductRepository $productRepository): Response
{
    // Récupérer le produit depuis le repository
    $product = $productRepository->find($id);

    return $this->render('product/show.html.twig', [
        'product' => $product
    ]);
}
```

# Récupérer des Données à partir de la Requête (GET ou POST)

Pour récupérer des données transmises par une requête HTTP (GET ou POST), Symfony offre la possibilité d'utiliser l'objet `Request`. Cet objet encapsule toutes les informations relatives à la requête, y compris les paramètres, les en-têtes, les fichiers, etc.
Récupérer des Données de la Requête GET

Les données GET sont envoyées via les paramètres d'URL (appelés query parameters). Vous pouvez les récupérer en utilisant l'objet `Request`.

**Exemple de récupération de paramètres GET :**

```
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/search', name: 'search')]
public function search(Request $request): Response
{
    // Récupérer un paramètre GET 'q' (par exemple, /search?q=symfony)
    $query = $request->query->get('q');

    return new Response('You searched for: ' . $query);
}
```

**Explication :**
  - `$request->query->get('q')` : Récupère la valeur du paramètre `q` qui est passé dans l'URL comme paramètre GET (par exemple, `/search?q=symfony`).
  - `$request->query->all()` : Renvoie tous les paramètres GET sous forme de tableau associatif.

Récupérer des Données de la Requête POST

Les données POST sont souvent envoyées par des formulaires. Vous pouvez également les récupérer via l'objet `Request`.

**Exemple de récupération de données POST :**

```
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/submit', name: 'form_submit', methods: ['POST'])]
public function submit(Request $request): Response
{
    // Récupérer une donnée POST 'name'
    $name = $request->request->get('name');

    return new Response('Hello, ' . $name);
}
```

**Explication :**
  - $request->request->get('name') : Récupère la donnée name envoyée par un formulaire en méthode POST.
  - $request->request->all() : Renvoie toutes les données POST sous forme de tableau associatif.

## Différence entre GET et POST dans Symfony

- GET : Les données sont envoyées dans l'URL (visible), généralement utilisées pour la recherche, la navigation, ou toute opération qui n'a pas d'effet de modification sur les données.
- POST : Les données sont envoyées dans le corps de la requête (invisible dans l'URL), généralement utilisées pour les soumissions de formulaires ou les opérations qui modifient l'état du serveur (comme la création ou la mise à jour des données).

# Exercice Symfony : 3 Modèles, 3 Pages, 3 Contrôleurs avec Opérations CRUD (Sans Liens entre les Modèles)

Dans cet exercice, vous allez créer une application Symfony avec trois modèles (entités) indépendants, chaque modèle ayant son propre contrôleur pour gérer des opérations de base comme la suppression, l'affichage de tous les enregistrements, la recherche par ID et la recherche par critères spécifiques.
Objectif
1. Créer trois entités indépendantes : Product, Category, et Customer.
2. Créer trois contrôleurs correspondants.
3. Afficher les pages suivantes :
   - Une page qui affiche tous les enregistrements pour chaque modèle. `(/product), (/category), (/customer)`
   - Une page qui supprime un enregistrement `(/product/del/{id}), (/category/{id}), (/customer/{id})`
   - Une page pour rechercher un enregistrement par ID et un critère.
4. Afficher un message confirmant la suppression, et une confirmation pour les opérations de recherche.
