(function (Drupal, drupalSettings) {
    Drupal.behaviors.trustarc = {
      attach: function (context, settings) {
        console.log('TrustArc Name:', drupalSettings.trustarc.name);
      }
    };
  })(Drupal, drupalSettings);
  