// assets/js/business-card.js

require('../css/business-card.scss');

var business_card = {
  launch: function() {
    // console.log("[business-card.js] /!\\ Work in progress ... /!\\");
    // Retrieve useful DOM nodes
    var n_body = document.querySelector('body');
    var n_bc_actions    = n_body.querySelector('.business-cards-actions');
    var n_bc_container  = n_body.querySelector('.business-cards-container');

    //
    // Doc ready
    //

    (function() {
      console.log('[business-card.js] 🌱 Radis ! ~');
      // Click on checkbox to disable hover
      n_bc_actions.querySelector('#bc-disable-hover').addEventListener('change', function() {
        n_bc_container.classList.toggle('-disable-hover');
      });

      // Click on checkbox to get sharp corners on cards
      n_bc_actions.querySelector('#bc-corners-squared').addEventListener('change', function() {
        n_bc_container.classList.toggle('-corners-squared');
      });
    })();
  }
};

business_card.launch();
