# Architecture: Resource (Sylius Resource Component)

## Purpose

Provides foundational abstractions for Sylius domain models: resource interfaces, translatable entities, factories, repositories, state machines, and CRUD annotations.

## Directory Structure

```
Model/
  ResourceInterface.php         All Sylius models implement this (getId())
  TimestampableInterface/Trait   createdAt / updatedAt behaviour
  ToggleableInterface/Trait      enabled/disabled behaviour
  ArchivableInterface/Trait      archivedAt behaviour
  TranslatableInterface/Trait    Multi-language entity support via Translation models
  AbstractTranslation.php        Base class for translation entities
  ResourceLogEntry.php           Doctrine loggable log entry model
Factory/
  FactoryInterface.php           createNew() contract
  TranslatableFactory.php        Creates translatable entities with locale pre-filled
Generator/
  RandomnessGenerator.php        Generates random slugs, tokens, codes
Metadata/
  Metadata.php / MetadataInterface.php  Resource configuration (alias, driver, class, etc.)
  Registry.php                   Registry of all Metadata instances (one per resource type)
Repository/
  RepositoryInterface.php        find*, findAll, createPaginator
  InMemoryRepository.php         Array-backed repository for tests
Reflection/
  ClassReflection.php            Utility for checking trait usage
StateMachine/
  StateMachine.php               Winzou state machine wrapper
  StateMachineInterface.php
Translation/
  Provider/ImmutableTranslationLocaleProvider.php  Provides available locales
  TranslatableEntityLocaleAssigner.php              Sets locale on entity after load
Annotation/
  SyliusCrudRoutes.php           Annotation for auto-generating CRUD routes
  SyliusRoute.php                Single route annotation
Exception/                       Typed domain exceptions
```

## Key Design Decisions

- **Interface + trait pairs**: Behaviour like timestamping or toggling is defined in an interface and implemented in a companion trait. Models opt in by implementing the interface and using the trait.
- **Translation pattern**: Translatable entities hold a collection of `Translation` objects keyed by locale. The `TranslatableTrait` proxies property access to the current-locale translation.
- **Resource metadata**: A `Metadata` object describes a resource type (class, alias, factory, repository, etc.) and is stored in the `Registry`. This drives Sylius's resource routing and CRUD layer.
- **In-memory repository**: `InMemoryRepository` provides a full `RepositoryInterface` implementation backed by a plain array for use in tests without a database.

## Extension Points

- Implement `ResourceInterface` and use the behaviour traits to create a new Sylius-compatible model.
- Implement `FactoryInterface` for custom construction logic (e.g., applying defaults).
- Register resource metadata with the `Registry` to enable Sylius resource routing.

## Dependency Flow

```
DI container boot
  -> Registry->addFromAliasAndConfiguration('app.product', config)
     -> Metadata::fromAliasAndConfiguration(...)

HTTP request -> SyliusResourceBundle controller
  -> Registry::get('app.product') -> Metadata
  -> Metadata->getClass('model') -> App\Entity\Product
  -> Metadata->getServiceId('factory') -> app.factory.product
  -> Factory->createNew() -> Product instance
```
