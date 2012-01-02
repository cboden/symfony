CHANGELOG for 2.1.x
===================

This changelog references the relevant changes (bug and security fixes) done
in 2.1 minor versions.

To get the diff for a specific change, go to https://github.com/symfony/symfony/commit/XXX where XXX is the change hash
To get the diff between two versions, go to https://github.com/symfony/symfony/compare/v2.1.0...v2.1.1

2.1.0
-----

### DoctrineBridge

 * added a default implementation of the ManagerRegistry
 * added a session storage for Doctrine DBAL

### AbstractDoctrineBundle

 * This bundle has been removed and the relevant code has been moved to the Doctrine bridge

### DoctrineBundle

 * added optional `group_by` property to `EntityType` that supports either a `PropertyPath` or a `\Closure` that is evaluated on the entity choices
 * The `em` option for the `UniqueEntity` constraint is now optional (and should probably not be used anymore).

### FrameworkBundle

 * [BC BREAK] removed the possibility to pass a non-scalar attributes when calling render() to make the call works with or without a reverse proxy
 * added a router:match command
 * added kernel.event_subscriber tag
 * added a way to create relative symlinks when running assets:install command (--relative option)
 * added Controller::getUser()
 * [BC BREAK] assets_base_urls and base_urls merging strategy has changed
 * changed the default profiler storage to use the filesystem instead of SQLite
 * added support for placeholders in route defaults and requirements (replaced by the value set in the service container)
 * added Filesystem component as a dependency
 * [BC BREAK] changed `session.xml` service name `session.storage.native` to `session.storage.native_file`
 * added new session storage drivers to session.xml: `session.storage.native_memcache`, `session.storage.native_memcached`,
   `session.storage.native_sqlite`, `session.storage.null`, `session.storage.memcache`,
   and `session.storage.memcached`.  Added `session.storage.mock_file` service for functional session testing.
 * removed `session.storage.filesystem` service.

### SecurityBundle

 * [BC BREAK] The custom factories for the firewall configuration are now
   registered during the build method of bundles instead of being registered
   by the end-user (you need to remove the 'factories' keys in your security
   configuration).

 * [BC BREAK] The Firewall listener is now registered after the Router one. It
   means that specific Firewall URLs (like /login_check and /logout must now
   have proper route defined in your routing configuration)

 * [BC BREAK] refactored the user provider configuration. The configuration
   changed for the chain provider and the memory provider:

   Before:

        security:
            providers:
                my_chain_provider:
                    providers: [my_memory_provider, my_doctrine_provider]
                my_memory_provider:
                    users:
                        toto: { password: foobar, roles: [ROLE_USER] }
                        foo: { password: bar, roles: [ROLE_USER, ROLE_ADMIN] }

   After:

        security:
            providers:
                my_chain_provider:
                    chain:
                        providers: [my_memory_provider, my_doctrine_provider]
                my_memory_provider:
                    memory:
                        users:
                            toto: { password: foobar, roles: [ROLE_USER] }
                            foo: { password: bar, roles: [ROLE_USER, ROLE_ADMIN] }

 * added a validator for the user password
 * added 'erase_credentials' as a configuration key (true by default)
 * added new events: `security.authentication.success` and `security.authentication.failure`
   fired on authentication success/failure, regardless of authentication method,
   events are defined in new event class: `Symfony\Component\Security\Core\AuthenticationEvents`.

### SwiftmailerBundle

 * moved the data collector to the bridge
 * replaced MessageLogger class with the one from Swiftmailer 4.1.3

### TwigBundle

 * added the real template name when an error occurs in a Twig template

### WebProfilerBundle

[BC BREAK] You must clear old profiles after upgrading to 2.1 (don't forget to
           remove the table if you are using a DB)

 * added support for the request method
 * added a routing panel
 * added a timeline panel
 * The toolbar position can now be configured via the `position` option (can be `top` or `bottom`)

### BrowserKit

 * [BC BREAK] The CookieJar internals have changed to allow cookies with the same name on different sub-domains/sub-paths

### Config

 * added a way to add documentation on configuration
 * implemented `Serializable` on resources
 * LoaderResolverInterface is now used instead of LoaderResolver for type hinting

### Console

 * added a --raw option to the list command
 * added support for STDERR in the console output class (errors are now sent to STDERR)
 * made the defaults (helper set, commands, input definition) in Application more easily customizable
 * added support for the shell even if readline is not available

### ClassLoader

 * added support for loading globally-installed PEAR packages

### DependencyInjection

 * component exceptions that inherit base SPL classes are now used exclusively (this includes dumped containers)

### DomCrawler

 * added a way to get parsing errors for Crawler::addHtmlContent() and Crawler::addXmlContent() via libxml functions
 * added support for submitting a form without a submit button

### EventDispatcher

 * added a reference to the EventDispatcher on the Event
 * added a reference to the Event name on the event

### Filesystem

 * created this new component

### Finder

 * Finder::exclude() now supports an array of directories as an argument

### Form

 * added support for validation groups as callbacks
 * made the translation catalogue configurable via the "translation_domain" option
 * added Form::getErrorsAsString() to help debugging forms
 * allowed setting different options for RepeatedType fields (like the label)

### HttpFoundation

 * added support for streamed responses
 * made Response::prepare() method the place to enforce HTTP specification
 * [BC BREAK] moved management of the locale from the Session class to the Request class
 * added a generic access to the PHP built-in filter mechanism: ParameterBag::filter()
 * made FileBinaryMimeTypeGuesser command configurable
 * added Request::getUser() and Request::getPassword()
 * added support for the PATCH method in Request
 * removed the ContentTypeMimeTypeGuesser class as it is deprecated and never used on PHP 5.3
 * added ResponseHeaderBag::makeDisposition() (implements RFC 6266)
 * made mimetype to extension conversion configurable
 * Flashes are now stored as a bucket of messages per `$type` so there can be multiple messages per type.
   There are four interface constants for type, `FlashBagInterface::INFO`, `FlashBagInterface::NOTICE`,
   `FlashBagInterface::WARNING` and `FlashBagInterface::ERROR`.
 * Added `FlashBag` (default). Flashes expire when retrieved by `popFlashes()`.
   This makes the implementation ESI compatible.
 * Added `AutoExpireFlashBag` to replicate Symfony 2.0.x auto expire behaviour of messages auto expiring
   after one page page load.  Messages must be retrived by `popFlashes()` but will expire regardless of
   being retrieved or not, which retains th old behaviour.
 * [BC BREAK] Removed the following methods from the Session class: `close()`, `setFlash()`, `hasFlash()`,
   and `removeFlash()` and added new methods.  Use `addFlashes()` to add new flash messages.
   `getFlashes()` now returns and array of flash messages.
 * `Session->clear()` now only clears session attributes as before it cleared flash messages and
   attributes. `Session->clearAllFlashes()` clears flashes now.
 * Added `AbstractSessionStorage` base class for session storage drivers.
 * Added `SessionSaveHandler` interface which storage drivers should implement after inheriting from
   `AbstractSessionStorage` when writing custom session save handlers.
 * [BC BREAK] `SessionStorageInterface` methods removed: `write()`, `read()` and `remove()`.  Added
   `getAttributes()`, `getFlashes()`.
 * Moved attribute storage to `AttributeBagInterface`.
 * Added `AttributeBag` to replicate attributes storage behaviour from 2.0.x (default).
 * Added `NamespacedAttributeBag` for namespace session attributes.
 * Session now implements `SessionInterface` making implementation customizable and portable.
 * [BC BREAK] Removed `NativeSessionStorage` and replaced with `NativeFileSessionStorage`.
 * Added session storage drivers for PHP native Memcache, Memcached and SQLite session save handlers.
 * Added session storage drivers for custom Memcache, Memcached and Null session save handlers.
 * Removed `FilesystemSessionStorage`, use `MockFileSessionStorage` for functional testing instead.

### HttpKernel

 * added CacheClearerInterface
 * added a kernel.terminate event
 * added a Stopwatch class
 * added WarmableInterface
 * improved extensibility between bundles
 * added a File-based profiler storage
 * added a MongoDB-based profiler storage
 * moved Filesystem class to its own component

### Locale

 * added Locale::getIcuVersion() and Locale::getIcuDataVersion()

### Process

 * added ProcessBuilder

### Routing

 * added a TraceableUrlMatcher
 * added the possibility to define default values and requirements for placeholders in prefix
 * added RouterInterface::getRouteCollection

### Security

 * after login, the user is now redirected to `default_target_path` if `use_referer` is true and the referrer is the `login_path`.
 * added a way to remove a token from a session

### Serializer

 * [BC BREAK] convert the `item` XML tag to an array

   ``` xml
   <?xml version="1.0"?>
   <response>
       <item><title><![CDATA[title1]]></title></item><item><title><![CDATA[title2]]></title></item>
   </response>
   ```

   Before:

        Array()

   After:

        Array(
            [item] => Array(
                [0] => Array(
                    [title] => title1
                )
                [1] => Array(
                    [title] => title2
                )
            )
        )


### Translation

 * changed the default extension for XLIFF files from .xliff to .xlf
 * added support for gettext
 * added support for more than one fallback locale
 * added support for translations in ResourceBundles
 * added support for extracting translation messages from templates (Twig and PHP)
 * added dumpers for translation catalogs
 * added support for QT translations

### Validator

 * added support for `ctype_*` assertions in `TypeValidator`
 * added a Size validator
 * added a SizeLength validator
 * improved the ImageValidator with min width, max width, min height, and max height constraints
 * added support for MIME with wildcard in FileValidator

### Yaml

 * Yaml::parse() does not evaluate loaded files as PHP files by default anymore (call Yaml::enablePhpParsing() to get back the old behavior)
