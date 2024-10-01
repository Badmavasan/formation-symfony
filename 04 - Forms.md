# Formulaire 


## Créer une Entité

Imaginons que nous voulons créer un formulaire pour ajouter des utilisateurs. Nous devons d'abord créer une entité User.

**Commande pour créer l'entité :**

```
php bin/console make:entity User
```

**Exemple d'entité `User` :**

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

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    // Getters et Setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
}
```

## Créer le Formulaire

Nous allons maintenant créer un formulaire pour cette entité en utilisant le générateur de formulaires Symfony.

**Commande pour générer le formulaire :**

```
php bin/console make:form UserType
```

Cela génère une classe `UserType` dans `src/Form/UserType.php`

Exemple de classe de formulaire `UserType` :

```
// src/Form/UserType.php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
```

## Créer le Contrôleur pour Afficher et Traiter le Formulaire

Nous allons maintenant créer un contrôleur qui affichera le formulaire et traitera les données soumises par l'utilisateur.

**Exemple de contrôleur `UserController` :**

```
// src/Controller/UserController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/new', name: 'user_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de l'entité User
        $user = new User();

        // Créer le formulaire et le lier à l'entité
        $form = $this->createForm(UserType::class, $user);

        // Traiter la requête HTTP
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger vers une autre page ou afficher un message de succès
            return $this->redirectToRoute('user_success');
        }

        // Rendre le formulaire pour qu'il soit affiché à l'utilisateur
        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
```

Explication du contrôleur :

- `$user` = new User(); : On crée une nouvelle instance de l'entité `User`.
- `$form = $this->createForm(UserType::class, $user);` : On crée un formulaire basé sur le `UserType` et on le lie à l'entité `User`.
- `$form->handleRequest($request);` : Cette méthode permet de gérer la soumission du formulaire. Si le formulaire a été soumis, les données du formulaire sont injectées dans l'entité `$user`.
- `if ($form->isSubmitted() && $form->isValid())` : On vérifie si le formulaire a été soumis et est valide. Si c'est le cas, les données sont persistées dans la base de données.
- `$entityManager->persist($user);` : On prépare l'objet `User` pour être inséré dans la base de données.
- `$entityManager->flush();` : On envoie la transaction à la base de données.


# Afficher le Formulaire dans Twig

Nous allons maintenant créer un template pour afficher le formulaire à l'utilisateur.

**Exemple de template new.html.twig :**

```
{# templates/user/new.html.twig #}

<h1>Créer un nouvel utilisateur</h1>

{{ form_start(form) }}
    {{ form_row(form.name) }}
    {{ form_row(form.email) }}

    <button type="submit">Créer</button>
{{ form_end(form) }}
```

Explication :

- `form_start(form)` : Cette fonction génère le début du formulaire HTML.
- `form_row(form.name)` : Génère un champ de formulaire complet pour le champ name avec le label et le champ input.
- `form_end(form)` : Génère la fin du formulaire HTML.


