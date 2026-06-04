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
    const wizardValidationFormStep1 = wizardValidationForm.querySelector('#cnic_detail');
    const wizardValidationFormStep2 = wizardValidationForm.querySelector('#general_information');
    const wizardValidationFormStep3 = wizardValidationForm.querySelector('#background_information');
    const wizardValidationFormStep4 = wizardValidationForm.querySelector('#agriculture_land_and_asset');
    const wizardValidationFormStep5 = wizardValidationForm.querySelector('#preview');
    // Wizard next prev button
    const wizardValidationNext = [].slice.call(wizardValidationForm.querySelectorAll('.btn-next'));
    const wizardValidationPrev = [].slice.call(wizardValidationForm.querySelectorAll('.btn-prev'));

    const validationStepper = new Stepper(wizardValidation, {
      linear: true,
    });

    // cnic_detail
    const FormValidation1 = FormValidation.formValidation(wizardValidationFormStep1, {
      fields: {
        check_cnic_number: {
          validators: {
            notEmpty: {
              message: 'The CNIC is required'
            },
            regexp: {
              regexp: /^\d{5}-\d{7}-\d$/,
              message: 'Please enter a valid CNIC in the format: XXXXX-XXXXXXX-X'
            }
          }
        }
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
      },
      init: instance => {
        instance.on('plugins.message.placed', function (e) {
          //* Move the error message out of the `input-group` element
          if (e.element.parentElement.classList.contains('input-group')) {
            e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
          }
        });
      }
    }).on('core.form.valid', function () {
      Fromstep1Action(validationStepper);
    });

    // general_information
    const FormValidation2 = FormValidation.formValidation(wizardValidationFormStep2, {
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
        formValidationCountry: {
          validators: {
            notEmpty: {
              message: 'The Country is required'
            }
          }
        },
        formValidationLanguage: {
          validators: {
            notEmpty: {
              message: 'The Languages is required'
            }
          }
        }
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
      // Jump to the next step when all fields in the current step are valid
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
    const FormValidation3 = FormValidation.formValidation(wizardValidationFormStep3, {
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
      onNextSave(validationStepper, 3);
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
      onNextSave(validationStepper, 4);
    });


    const FormValidation5 = FormValidation.formValidation(wizardValidationFormStep5, {
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
          case 4:
            FormValidation5.validate();
            break;
          default:
            break;
        }
      });
    });

    wizardValidationPrev.forEach(item => {
      item.addEventListener('click', event => {
        switch (validationStepper._currentIndex) {
          case 4:
            validationStepper.previous();
            break;
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


  // @Action - Check CNIC Step 1
  function Fromstep1Action(validationStepper) {
    const cnicNumber = $('#check_cnic_number').val();
    const url = $('#check_cnic_number').data('action');

    if (cnicNumber.length === 15) {
      startLoading('Data checking with existing Database...');
      $.ajax({
        url: url,
        type: 'POST',
        data: {
          cnic_number: cnicNumber
        },
        success: function (response) {
          stopLoading();
          if (response?.success === false) {
            Swal.fire({
              icon: 'error',
              title: response?.message,
              showConfirmButton: false,
              timer: 3500
            })
          } else {
            setFormValues(response?.data);
            validationStepper.to(response?.data?.step + 1);
          }
        },
        error: function () {
          // Handle AJAX error here
          stopLoading();
        }
      });
    }
  }

  function onNextSave(validationStepper, step = 2) {
    const url = $('#farmer-validation-form').attr('action');
    const formData = new FormData($('#farmer-validation-form')[0]);
    formData.append('step', step);
    formData.append('cnic_number', $('#check_cnic_number').val());
    $.ajax({
      url: url,
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
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

  // function onNextSave(validationStepper, step = 2) {
  //   const url = $('#farmer-validation-form').attr('action');
  //   let formData = $('#farmer-validation-form').serialize();
  //   formData += '&step=' + encodeURIComponent(step);
  //   formData += '&cnic_number=' + encodeURIComponent($('#check_cnic_number').val());
  //   // var secretKey = 'abc1232'; // Replace with your secret key
  //   // var encryptedData = CryptoJS.AES.encrypt(formData, secretKey).toString();
  //   // if (step === 4) {
  //   //   startLoading('Verifying Record With BOR/CRS please wait...')
  //   // }
  //   $.ajax({
  //     url: url,
  //     type: 'POST',
  //     data: formData,
  //     success: function (response) {
  //       stopLoading();
  //       if (response?.success === true) {
  //         validationStepper.next();
  //       }
  //     },
  //     error: function () {
  //       stopLoading();
  //     }
  //   });
  // }
})();
