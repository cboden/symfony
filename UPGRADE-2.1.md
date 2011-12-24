UPGRADE FROM 2.0 to 2.1
=======================

### General

* assets_base_urls and base_urls merging strategy has changed

  Unlike most configuration blocks, successive values for
  ``assets_base_urls`` will overwrite each other instead of being merged.
  This behavior was chosen because developers will typically define base
  URL's for each environment. Given that most projects tend to inherit
  configurations (e.g. ``config_test.yml`` imports ``config_dev.yml``)
  and/or share a common base configuration (i.e. ``config.yml``), merging
  could yield a set of base URL's for multiple environments.

### [HttpFoundation]

* moved management of the locale from the Session class to the Request class

  Configuring the default locale:

  Before:

      framework:
        session:
            default_locale: fr

  After:

      framework:
        default_locale: fr

  Retrieving the locale from a Twig template:

  Before: {{ app.request.session.locale }} or {{ app.session.locale }}
  After: {{ app.request.locale }}

  Retrieving the locale from a PHP template:

  Before: $view['session']->getLocale()
  After: $view['request']->getLocale()

  Retrieving the locale from PHP code:

  Before: $session->getLocale()
  After: $request->getLocale()

* Flash Messages now returns and array based on type

  Before (PHP):

  <?php if ($view['session']->hasFlash('notice')): ?>
      <div class="flash-notice">
          <?php echo $view['session']->getFlash('notice') ?>
      </div>
  <?php endif; ?>

  After (PHP):

      <?php foreach ($view['session']->popFlashes('notice') as $notice): ?>
          <div class="flash-notice">
              <?php echo $notice; ?>
          </div>
      <?php endforeach; ?>

  If You wanted to process all flash types you could also make use of the `popAllFlashes()` API:

      <?php foreach ($view['session']->popAllFlashes() as $type => $flashes): ?>
          <?php foreach ($flashes as $flash): ?>
              <div class="flash-$type">
                  <?php echo $flash; ?>
              </div>
          <?php endforeach; ?>
      <?php endforeach; ?>

.. note::

    The Flash Message API provides constants which you can optionally use.  For example
    `Symfony\Component\HttpFoundation\FlashBag::NOTICE`, which can also be abbreviated to
    `FlashBag::NOTICE` providing you declare `<?php use Symfony\Component\HttpFoundation\FlashBag; ?>`
    at the beginning of the PHP template.

  Before (Twig):

  {% if app.session.hasFlash('notice') %}
      <div class="flash-notice">
          {{ app.session.flash('notice') }}
      </div>
  {% endif %}

  After (Twig):

  {% for flashMessage in app.session.popFlashes('notice') %}
      <div class="flash-notice">
          {{ flashMessage }}
      </div>
  {% endforeach %}

  Again you can process all flash messages in one go with

  {% for type, flashMessages in app.session.popAllFlashes() %}
      {% for flashMessage in flashMessages) %}
          <div class="flash-{{ type }}">
              {{ flashMessage }}
          </div>
      {% endforeach %}
  {% endforeach %}

.. note::

    You can access optionally use constants in Twig templates using `constant()` e.g.
    `constant('Symfony\Component\HttpFoundation\FlashBag::NOTICE')`.

* Session object

  The methods, `setFlash()`, `hasFlash()`, and `removeFlash()` have been removed from the `Session`
  object.  You may use `addFlash()` to add flashes.  `getFlashes()`, now returns an array. Use
  `popFlashes()` to get flashes for display, or `popAllFlashes()` to process all flashes in on go.

* Session storage drivers

  Session storage drivers should inherit from
  `Symfony\Component\HttpFoundation\SessionStorage\AbstractSessionStorage`
  and no longer should implement `read()`, `write()`, `remove()` which were removed from the
  `SessionStorageInterface`.

  Any session storage drive that wants to use custom save handlers should
  implement `Symfony\Component\HttpFoundation\SessionStorage\SessionSaveHandlerInterface`

### [FrameworkBundle]

  The service `session.storage.native` is now called `session.storage.native_file`

  The service `session.storage.filesystem` is now called `session.storage.mock_file`
  and is used for functional unit testing.  You will need to update any references
  in functional tests.

