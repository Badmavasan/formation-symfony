# Relations 

En Doctrine ORM (utilisé avec Symfony), les associations entre entités sont gérées via des annotations ou des attributs dans les classes d'entités. Il existe trois types principaux d'associations : OneToOne, OneToMany et ManyToMany. Voici des explications sur chacun de ces types ainsi que des exemples pour les implémenter en Symfony.

## Association OneToOne (Un-à-Un)

Une relation OneToOne signifie qu'une entité est liée à une seule autre entité, et inversement. Par exemple, un Utilisateur pourrait avoir un Profil unique, et un Profil pourrait appartenir à un seul Utilisateur.

**Exemple : Un Utilisateur et un Profil**

- Un utilisateur ne peut avoir qu'un seul profil.
- Un profil ne peut appartenir qu'à un seul utilisateur.

Implémentation dans Symfony
1. Créez les entités User et Profile.

```
php bin/console make:entity User
php bin/console make:entity Profile
```

2. Configurez l'association **`OneToOne`**.

  Dans l'entité `User` :

  ```
  // src/Entity/User.php
  namespace App\Entity;
  
  use Doctrine\ORM\Mapping as ORM;
  
  #[ORM\Entity]
  class User
  {
      #[ORM\Id]
      #[ORM\GeneratedValue]
      #[ORM\Column(type: 'integer')]
      private $id;
  
      #[ORM\OneToOne(targetEntity: Profile::class, cascade: ['persist', 'remove'])]
      #[ORM\JoinColumn(nullable: false)]
      private $profile;
  
      // Getters et setters
      public function getProfile(): ?Profile
      {
          return $this->profile;
      }
  
      public function setProfile(Profile $profile): self
      {
          $this->profile = $profile;
  
          return $this;
      }
  }
  ```

  Dans l'entité `Profile` :

  ```
  // src/Entity/Profile.php
  namespace App\Entity;
  
  use Doctrine\ORM\Mapping as ORM;
  
  #[ORM\Entity]
  class Profile
  {
      #[ORM\Id]
      #[ORM\GeneratedValue]
      #[ORM\Column(type: 'integer')]
      private $id;
  
      #[ORM\OneToOne(targetEntity: User::class, mappedBy: 'profile')]
      private $user;
  
      // Getters et setters
      public function getUser(): ?User
      {
          return $this->user;
      }
  
      public function setUser(User $user): self
      {
          // set the owning side of the relation if necessary
          if ($user->getProfile() !== $this) {
              $user->setProfile($this);
          }
  
          $this->user = $user;
  
          return $this;
      }
  }
  ```
Explication :
  - `@OneToOne` : Indique une relation un-à-un entre User et Profile.
  - `cascade` : ['persist', 'remove'] : Lors de la création ou de la suppression d'un utilisateur, son profil est également créé ou supprimé.
  - `mappedBy` et `inversedBy` : Utilisés pour indiquer quelle entité est propriétaire de la relation.

## Association OneToMany / ManyToOne (Un-à-Plusieurs / Plusieurs-à-Un)

Une relation OneToMany signifie qu'une entité peut être liée à plusieurs autres entités, mais chaque entité liée appartient à une seule entité. Par exemple, un Auteur peut écrire plusieurs Livres, mais chaque Livre a un seul Auteur.

**Exemple : Un Auteur et plusieurs Livres**
- Un auteur peut avoir plusieurs livres.
- Un livre ne peut avoir qu'un seul auteur.

Implémentation dans Symfony

1. Créez les entités Author et Book.

```
php bin/console make:entity Author
php bin/console make:entity Book
```

2. Configurez l'association `OneToMany` dans l'entité Author et `ManyToOne` dans l'entité Book.

  Dans l'entité `Author` (`OneToMany`) :
  
  ```
  // src/Entity/Author.php
  namespace App\Entity;
  
  use Doctrine\ORM\Mapping as ORM;
  use Doctrine\Common\Collections\ArrayCollection;
  use Doctrine\Common\Collections\Collection;
  
  #[ORM\Entity]
  class Author
  {
      #[ORM\Id]
      #[ORM\GeneratedValue]
      #[ORM\Column(type: 'integer')]
      private $id;
  
      #[ORM\Column(type: 'string', length: 255)]
      private $name;
  
      #[ORM\OneToMany(mappedBy: 'author', targetEntity: Book::class, cascade: ['persist', 'remove'])]
      private $books;
  
      public function __construct()
      {
          $this->books = new ArrayCollection();
      }
  
      public function getBooks(): Collection
      {
          return $this->books;
      }
  
      public function addBook(Book $book): self
      {
          if (!$this->books->contains($book)) {
              $this->books[] = $book;
              $book->setAuthor($this);
          }
  
          return $this;
      }
  
      public function removeBook(Book $book): self
      {
          if ($this->books->removeElement($book)) {
              // Set the owning side to null
              if ($book->getAuthor() === $this) {
                  $book->setAuthor(null);
              }
          }
  
          return $this;
      }
  }
  ```

  Dans l'entité `Book` (`ManyToOne`) :

  ```
  // src/Entity/Book.php
  namespace App\Entity;
  
  use Doctrine\ORM\Mapping as ORM;
  
  #[ORM\Entity]
  class Book
  {
      #[ORM\Id]
      #[ORM\GeneratedValue]
      #[ORM\Column(type: 'integer')]
      private $id;
  
      #[ORM\Column(type: 'string', length: 255)]
      private $title;
  
      #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'books')]
      #[ORM\JoinColumn(nullable: false)]
      private $author;
  
      public function getAuthor(): ?Author
      {
          return $this->author;
      }
  
      public function setAuthor(?Author $author): self
      {
          $this->author = $author;
  
          return $this;
      }
  }
  ```

Explication :

- `OneToMany` dans Author indique qu'un auteur peut avoir plusieurs livres. L'association est établie via `mappedBy`.
- `ManyToOne` dans Book montre que chaque livre a un seul auteur. L'association est établie via `inversedBy`.
- `ArrayCollection` est utilisé pour gérer les collections d'entités (comme les livres).

## Association ManyToMany (Plusieurs-à-Plusieurs)

Une relation ManyToMany signifie qu'une entité peut être liée à plusieurs autres entités, et inversement. Par exemple, un Étudiant peut être inscrit à plusieurs Cours, et chaque Cours peut avoir plusieurs Étudiants.

**Exemple : Étudiants et Cours**

- Un étudiant peut suivre plusieurs cours.
- Un cours peut avoir plusieurs étudiants.

Implémentation dans Symfony

1. Créez les entités Student et Course.

```
php bin/console make:entity Student
php bin/console make:entity Course
```

2. Configurez l'association ManyToMany.

  Dans l'entité `Student` :

  ```
  // src/Entity/Student.php
  namespace App\Entity;
  
  use Doctrine\ORM\Mapping as ORM;
  use Doctrine\Common\Collections\ArrayCollection;
  use Doctrine\Common\Collections\Collection;
  
  #[ORM\Entity]
  class Student
  {
      #[ORM\Id]
      #[ORM\GeneratedValue]
      #[ORM\Column(type: 'integer')]
      private $id;
  
      #[ORM\Column(type: 'string', length: 255)]
      private $name;
  
      #[ORM\ManyToMany(targetEntity: Course::class, inversedBy: 'students')]
      #[ORM\JoinTable(name: 'students_courses')]
      private $courses;
  
      public function __construct()
      {
          $this->courses = new ArrayCollection();
      }
  
      public function getCourses(): Collection
      {
          return $this->courses;
      }
  
      public function addCourse(Course $course): self
      {
          if (!$this->courses->contains($course)) {
              $this->courses[] = $course;
              $course->addStudent($this);
          }
  
          return $this;
      }
  
      public function removeCourse(Course $course): self
      {
          if ($this->courses->removeElement($course)) {
              $course->removeStudent($this);
          }
  
          return $this;
      }
  }
  ```

  Dans l'entité `Course` :

  ```
  // src/Entity/Course.php
  namespace App\Entity;
  
  use Doctrine\ORM\Mapping as ORM;
  use Doctrine\Common\Collections\ArrayCollection;
  use Doctrine\Common\Collections\Collection;
  
  #[ORM\Entity]
  class Course
  {
      #[ORM\Id]
      #[ORM\GeneratedValue]
      #[ORM\Column(type: 'integer')]
      private $id;
  
      #[ORM\Column(type: 'string', length: 255)]
      private $title;
  
      #[ORM\ManyToMany(targetEntity: Student::class, mappedBy: 'courses')]
      private $students;
  
      public function __construct()
      {
          $this->students = new ArrayCollection();
      }
  
      public function getStudents(): Collection
      {
          return $this->students;
      }
  
      public function addStudent(Student $student): self
      {
          if (!$this->students->contains($student)) {
              $this->students[] = $student;
              $student->addCourse($this);
          }
  
          return $this;
      }
  
      public function removeStudent(Student $student): self
      {
          if ($this->students->removeElement($student)) {
              $student->removeCourse($this);
          }
  
          return $this;
      }
  }
  ```

  Explication :
    1. `ManyToMany` est utilisé dans les deux entités, avec `mappedBy` et `inversedBy` pour indiquer la direction de la relation.
    2. `@JoinTable` (optionnel) : Définit la table de jointure (le nom de la table) dans la base de données. Si vous ne spécifiez pas ce tableau, Doctrine la génère automatiquement.

## Commandes Symfony pour Générer des Entités et Migrations

1. Création des Entités :
   - Utilisez `php bin/console make:entity` pour créer vos entités.

2. Génération des Migrations :
  - Une fois les entités configurées avec leurs relations, générez les migrations avec :
  ```
  php bin/console make:migration
  ```
3. Exécution des Migrations :
   - Appliquez les migrations pour créer les tables et leurs relations dans la base de données :
   ```
   php bin/console doctrine:migrations:migrate
   ```
   
  
