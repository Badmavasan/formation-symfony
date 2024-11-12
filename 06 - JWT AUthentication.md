# Guide Complet pour l'Implémentation de l'Authentification JWT dans Symfony 7 avec PHP 8.3

Source code : [JWT Authentication Implementation](https://github.com/Badmavasan/jwt-symfony-example)

## 1. Créer un Projet Symfony

Commencez par créer un projet Symfony avec la commande suivante :

```
composer create-project symfony/skeleton mon_projet
cd mon_projet
```

## 2. Installer les Bundles Nécessaires

Nous devons installer le bundle de sécurité et l'ORM pour interagir avec la base de données :

``` 
composer require symfony/security-bundle symfony/orm-pack
```

Ensuite, nous installons la bibliothèque pour gérer les tokens JWT :

``` 
composer require lcobucci/jwt
```

## 3. Configurer la Base de Données

Configurez votre connexion à la base de données dans le fichier `.env` :

```
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/nom_de_la_base_de_donnees?serverVersion=8.0"
```

Remplacez `utilisateur`, `motdepasse` et `nom_de_la_base_de_donnees` par vos informations de base de données. Puis, créez la base de données :

```
php bin/console doctrine:database:create
```

## 4. Créer l'Entité Utilisateur

Générez l'entité User pour représenter les utilisateurs de l'application :

```
php bin/console make:user
```

Suivez les instructions et choisissez `email` comme identifiant unique.


## 5. Configurer le Hasher de Mot de Passe

Dans le fichier `config/packages/security.yaml`, configurez le hasher de mot de passe avec l'algorithme `bcrypt` :

```
# config/packages/security.yaml
security:
    password_hashers:
        App\Entity\User:
            algorithm: bcrypt
            cost: 12
```

## 6. Créer l'Entité JwtToken

Générez une entité `JwtToken` pour stocker les tokens JWT dans la base de données :

```
php bin/console make:entity JwtToken
```

Ajoutez les champs suivants :

- `id` : clé primaire (entier)
- `token` : chaîne de caractères, unique
- `expiresAt` : date et heure
- `user` : relation ManyToOne avec l'entité User

Voici un exemple de l'entité `JwtToken` :

```
namespace App\Entity;

use App\Repository\JwtTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: JwtTokenRepository::class)]
class JwtToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $token = null;

    #[ORM\Column]
    private ?\DateTimeInterface $expiresAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserInterface $user = null;

    // Getters et setters...
}
```

## 7. Exécuter les Migrations

Générez et appliquez les migrations pour créer les tables nécessaires :

```
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## 8. Créer le Service JwtService

Le service `JwtService` est responsable de la création et de la validation des tokens JWT :

```
// src/Service/JwtService.php

namespace App\Service;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\Clock\SystemClock;
use DateTimeImmutable;
use Exception;

class JwtService
{
    private Configuration $config;

    public function __construct()
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText('votre-clé-sécurisée-de-256-bits')
        );
    }

    public function createToken(User $user): Plain
    {
        $now = new DateTimeImmutable();
        $expiresAt = $now->modify('+1 hour');

        return $this->config->builder()
            ->issuedBy('votre-application')
            ->permittedFor('votre-application')
            ->identifiedBy(bin2hex(random_bytes(16)), true)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($expiresAt)
            ->withClaim('uid', $user->getId())
            ->getToken($this->config->signer(), $this->config->signingKey());
    }

    public function parseToken(string $token): ?Plain
    {
        try {
            $parsedToken = $this->config->parser()->parse($token);
            if (!$parsedToken instanceof Plain) {
                return null;
            }

            $constraints = [
                new IssuedBy('votre-application'),
                new LooseValidAt(SystemClock::fromUTC()),
            ];

            if (!$this->config->validator()->validate($parsedToken, ...$constraints)) {
                return null;
            }

            return $parsedToken;
        } catch (Exception $e) {
            return null;
        }
    }
}
``` 

## 9. Créer le JwtAuthenticator

Le JwtAuthenticator est un filtre qui intercepte les requêtes pour vérifier la validité des tokens JWT :

```
// src/Security/JwtAuthenticator.php

namespace App\Security;

use App\Repository\UserRepository;
use App\Service\JwtService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class JwtAuthenticator extends AbstractAuthenticator
{
    private $jwtService;
    private $userRepository;

    public function __construct(JwtService $jwtService, UserRepository $userRepository)
    {
        $this->jwtService = $jwtService;
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request): ?bool
    {
        $authHeader = $request->headers->get('Authorization');
        return $authHeader && str_starts_with($authHeader, 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization');
        $token = substr($authHeader, 7);

        $parsedToken = $this->jwtService->parseToken($token);

        if (!$parsedToken) {
            throw new AuthenticationException('Token JWT invalide ou expiré.');
        }

        $userId = $parsedToken->claims()->get('uid');
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new AuthenticationException('Utilisateur non trouvé.');
        }

        return new SelfValidatingPassport(new UserBadge($userId, function () use ($user) {
            return $user;
        }));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        return new JsonResponse(['error' => $exception->getMessage()], JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse(['error' => 'Authentification requise.'], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
```

# 10. Configurer la Sécurité

Modifiez le fichier `config/packages/security.yaml` pour utiliser `JwtAuthenticator` :

```
# config/packages/security.yaml
security:
    enable_authenticator_manager: true

    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false

        api:
            pattern: ^/api
            stateless: true
            custom_authenticators:
                - App\Security\JwtAuthenticator

    access_control:
        - { path: ^/api, roles: IS_AUTHENTICATED_FULLY }
```

## 11. Créer le HomepageController

Ce contrôleur vérifie si l'utilisateur est authentifié :

```
// src/Controller/HomepageController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/homepage', name: 'app_homepage', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Vous n\'êtes pas authentifié.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'message' => 'Bienvenue sur la page d\'accueil !',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ],
        ]);
    }
}
```

## 12. Tester l'Authentification JWT

- Requête Authentifiée : Faites une requête GET vers /homepage avec un token JWT valide dans l'en-tête Authorization.
- Requête Non Authentifiée : Faites une requête sans token JWT. Vous devriez recevoir une réponse 401 Unauthorized avec le message "Vous n'êtes pas authentifié."

## Explication de l'Implémentation

- `JwtService` : Gère la création et la validation des tokens JWT.
- `JwtAuthenticator` : Filtre personnalisé qui intercepte les requêtes, extrait et valide les tokens JWT, et authentifie l'utilisateur.
- Filtre de Sécurité : `JwtAuthenticator` agit comme un filtre qui s'assure que seules les requêtes avec un token JWT valide sont autorisées à accéder aux routes sécurisées.
