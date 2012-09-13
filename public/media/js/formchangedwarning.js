//Εχετε αλλάξει κάποια πεδία. Θέλετε σίγουρα να απομακρυνθείτε απο αυτήν την σελίδα; ΑΡΧΗ

var catcher = function() {
  var changed = false;
  $('form').each(function() {
    if ($(this).data('initialForm') != $(this).serialize()) {
      changed = true;
    }
  });
  if (changed) {
    return 'Εχετε αλλάξει κάποια πεδία. Θέλετε σίγουρα να απομακρυνθείτε απο αυτήν την σελίδα;';
  }
};

$(initialFormSetup);

function initialFormSetup() {
  window.onbeforeunload = catcher;
  $('form').each(function() {
      $(this).data('initialForm', null);
  })

  $('form').each(function() {
    $(this).data('initialForm', $(this).serialize());
  }).submit(function(e) {
  var formEl = this;
  var changed = false;
  $('form').each(function() {
    if (this != formEl && $(this).data('initialForm') != $(this).serialize()) {
      changed = true;
    }
  });
  if (changed) {
    e.preventDefault();
  } else {
      window.onbeforeunload = null;
  }
  });
window.onbeforeunload = catcher;
}

//  Εχετε αλλάξει κάποια πεδία. Θέλετε σίγουρα να απομακρυνθείτε απο αυτήν την σελίδα; TΕΛΟΣ