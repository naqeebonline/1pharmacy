/**
 *  Form Wizard
 */

'use strict';

(function () {
  const select2 = $('.select2'),
    selectPicker = $('.selectpicker');

  // Wizard Validation
  // --------------------------------------------------------------------
  const wizardValidation = document.querySelector('#wizard-validation');
  if (typeof wizardValidation !== undefined && wizardValidation !== null) {
    // Wizard form
    const wizardValidationForm = wizardValidation.querySelector('#farmer-validation-form');
    // Wizard steps

    const wizardValidationFormStep1 = wizardValidationForm.querySelector('#general_information');
    const wizardValidationFormStep2 = wizardValidationForm.querySelector('#background_information');
    const wizardValidationFormStep3 = wizardValidationForm.querySelector('#agriculture_land_and_asset');
    const wizardValidationFormStep4 = wizardValidationForm.querySelector('#preview');
    // Wizard next prev button
    const wizardValidationNext = [].slice.call(wizardValidationForm.querySelectorAll('.btn-next'));
    const wizardValidationPrev = [].slice.call(wizardValidationForm.querySelectorAll('.btn-prev'));

    const validationStepper = new Stepper(wizardValidation, {
      linear: true,
    });



    // general_information
    const FormValidation1 = FormValidation.formValidation(wizardValidationFormStep1, {
      fields: {
        formValidationFirstName: {
          validators: {
            notEmpty: {
              message: 'The first name is required'
            }
          }
        },
        formValidationLastName: {
          validators: {
            notEmpty: {
              message: 'The last name is required'
            }
          }
        },

      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: '.col-sm-6'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
        submitButton: new FormValidation.plugins.SubmitButton()
      }
    }).on('core.form.valid', function () {
      // validationStepper.next();
      onNextSave(validationStepper, 2);
    });

    // Bootstrap Select (i.e Language select)
    if (selectPicker.length) {
      selectPicker.each(function () {
        var $this = $(this);
        $this.selectpicker().on('change', function () {
          FormValidation2.revalidateField('formValidationLanguage');
        });
      });
    }

    // select2
    if (select2.length) {
      select2.each(function () {
        var $this = $(this);
        $this.wrap('<div class="position-relative"></div>');
        $this
          .select2({
            placeholder: 'Select an country',
            dropdownParent: $this.parent()
          })
          .on('change.select2', function () {
            // Revalidate the color field when an option is chosen
            FormValidation2.revalidateField('formValidationCountry');
          });
      });
    }

    // background_information links
    const FormValidation2 = FormValidation.formValidation(wizardValidationFormStep2, {
      fields: {
        farmer_name: {
          validators: {
            regexp: {
              regexp: /^[A-Za-z\s\-']+$/i,
              message: 'Please enter a valid name.'
            }
          }
        },
        father_name: {
          validators: {
            regexp: {
              regexp: /^[A-Za-z\s\-']+$/i,
              message: 'Please enter a valid name.'
            }
          }
        },
        cnic_number: {
          validators: {
            regexp: {
              regexp: /^\d{5}-\d{7}-\d$/,
              message: 'Please enter a valid CNIC in the format: XXXXX-XXXXXXX-X'
            }
          }
        },
        contact_number_1: {
          validators: {
            regexp: {
              regexp: /^\d{10}$/,
              message: 'Please enter a valid 10-digit phone number.'
            }
          }
        },
        contact_number_2: {
          validators: {
            regexp: {
              regexp: /^\d{10}$/,
              message: 'Please enter a valid 10-digit phone number.'
            }
          }
        },

        alternative_contact_name: {
          validators: {
            regexp: {
              regexp: /^[A-Za-z\s\-']+$/i,
              message: 'Please enter a valid name.'
            }
          }
        },
        alternative_contact_number_1: {
          validators: {
            regexp: {
              regexp: /^\d{10}$/,
              message: 'Please enter a valid 10-digit phone number.'
            }
          }
        },
        alternative_contact_number_2: {
          validators: {
            regexp: {
              regexp: /^\d{10}$/,
              message: 'Please enter a valid 10-digit phone number.'
            }
          }
        },


      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: '.col-sm-6'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
        submitButton: new FormValidation.plugins.SubmitButton()
      }
    }).on('core.form.valid', function () {
      // validationStepper.next();
      onNextSave(validationStepper, 3);
    });


    const FormValidation3 = FormValidation.formValidation(wizardValidationFormStep3, {
      fields: {
        formValidationTwitter: {
          validators: {
            notEmpty: {
              message: 'The Twitter URL is required'
            },
            uri: {
              message: 'The URL is not proper'
            }
          }
        },
        formValidationFacebook: {
          validators: {
            notEmpty: {
              message: 'The Facebook URL is required'
            },
            uri: {
              message: 'The URL is not proper'
            }
          }
        },


      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: '.col-sm-6'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
        submitButton: new FormValidation.plugins.SubmitButton()
      }
    }).on('core.form.valid', function () {
      // validationStepper.next();
      onNextSave(validationStepper, 4);
    });


    const FormValidation4 = FormValidation.formValidation(wizardValidationFormStep4, {
      fields: {
        formValidationTwitter: {
          validators: {
            notEmpty: {
              message: 'The Twitter URL is required'
            },
            uri: {
              message: 'The URL is not proper'
            }
          }
        },



      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: '.col-sm-6'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
        submitButton: new FormValidation.plugins.SubmitButton()
      }
    }).on('core.form.valid', function () {

      // wizardValidationForm.submit()

    });

    wizardValidationNext.forEach(item => {
      item.addEventListener('click', event => {
        // When click the Next button, we will validate the current step
        switch (validationStepper._currentIndex) {
          case 0:
            FormValidation1.validate();
            break;
          case 1:
            FormValidation2.validate();
            break;
          case 2:
            FormValidation3.validate();
            break;
          case 3:
            FormValidation4.validate();
            break;
          default:
            break;
        }
      });
    });

    wizardValidationPrev.forEach(item => {
      item.addEventListener('click', event => {
        switch (validationStepper._currentIndex) {
          case 3:
            validationStepper.previous();
            break;
          case 2:
            validationStepper.previous();
            break;

          case 1:
            validationStepper.previous();
            break;
          case 0:

          default:
            break;
        }
      });
    });
  }




  function onNextSave(validationStepper, step = 2) {
    const url = $('#farmer-validation-form').attr('action');
    let formData = $('#farmer-validation-form').serialize();
    formData += '&step=' + encodeURIComponent(step);
    // var secretKey = 'abc1232'; // Replace with your secret key
    // var encryptedData = CryptoJS.AES.encrypt(formData, secretKey).toString();
    $.ajax({
      url: url,
      type: 'PUT',
      data: formData,
      success: function (response) {
        stopLoading();
        if (response?.success === true) {
          validationStepper.next();
        }
      },
      error: function () {
        stopLoading();
      }
    });
  }
})();
