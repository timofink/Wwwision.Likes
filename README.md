# Wwwision.Likes

Simple Neos Flow package that allows to track arbitrary "likes" or recommendations using Event-Sourcing.

# Installation

Install this package using composer:

```
composer require wwwision/likes
```

## Setup

### Event Store

By default, this package is configured to use the `DoctrineEventStorage` to store events with the default options.
This means, that "like"-events will be stored in a database table `neos_eventsourcing_eventstore_events` by default.
This can be changed with a few lines of `Settings.yaml`:

```yaml
Neos:
  EventSourcing:
    EventStore:
      stores:
        'Wwwision.Likes:EventStore':
          storageOptions:
            eventTableName: 'wwwision_like_events'
```

Afterwards the Event Store should be set-up via
```
./flow eventstore:setup Wwwision.Likes:EventStore
```

### Metadata / GDPR

By default all events published by this package will contain metadata that contains details about the currently
active HTTP request including request URL, method, userAgent and clientIP headers.

This behavior can be adjusted via `Settings.yaml`:
```yaml
Wwwision:
  Likes:
    eventMetadata:
      # disable tracking of absolute HTTP request URL
      url: true
      # disable tracking of HTTP request Method (GET, POST, ...)
      method: true
      # disable tracking of users IP address
      clientIpAddress: true
      # disable tracking of browsers "userAgent" header
      userAgent: true
```

# Usage

From PHP use the provided `LikeService` to add/revoke likes and to retrieve details about existing likes.
The service should be mostly self-explanatory, but it isn't meant to be used directly. Instead, it should be wrapped
in some service that is more specific to the actual domain.

## Example

```php
final class FavoriteCoffeeBeans {
  
    private LikeService $likeService;

    private User $authenticatedUser;

    public function __construct(LikeService $likeService, User $authenticatedUser) {
        $this->likeService = $likeService;
        $this->authenticatedUser = $authenticatedUser;
    }

    public function addCoffeeBean(CoffeeBean $coffeeBean): void
    {
        $this->likeService->addLike('CoffeeBeans', (string)$this->authenticatedUser->getId(), (string)$coffeeBean->getId());
    }

    public function removeCoffeeBean(CoffeeBean $coffeeBean): void
    {
        $this->likeService->revokeLike('CoffeeBeans', (string)$this->authenticatedUser->getId(), (string)$coffeeBean->getId());
    }

    public function contains(CoffeeBean $coffeeBean): bool
    {
        return $this->likeService->likeExists('CoffeeBeans', (string)$this->authenticatedUser->getId(), (string)$coffeeBean->getId());
    }
}
```
