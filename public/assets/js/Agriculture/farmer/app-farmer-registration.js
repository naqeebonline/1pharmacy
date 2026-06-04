


function addAnimalRow() {
    animalComponent();
    initializeSelect2();
}


function addEquipmentRow() {
    EquipmentComponent();
    initializeSelect2();
}

$("#farmer_type_id").on("change", function () {
    var selectedValue = $(this).val();
    if (selectedValue === "1") {
        $(".owner_document").removeClass('d-none');
        $(".other_document").addClass('d-none');
    } else {
        $(".owner_document").addClass('d-none');
        $(".other_document").removeClass('d-none');
    }
});

$(document).ready(function () {


    // Initialize Data of birth from 1990 for farmer type field
    flatpickr('#dateOfBirth', {
        dateFormat: 'Y-m-d',
        defaultDate: '1990-01-01',

    });

    $('.remove-leading-zero').on('input', function () {
        var inputValue = $(this).val();

        // Check if the input starts with zero
        if (inputValue.startsWith('0')) {
            // Remove leading zeros and update the input value
            $(this).val(inputValue.replace(/^0+/, ''));
        }
    });
    // Hide animal-related fields on page load
    $("#animalFields").hide();

    // Listen for changes in the "Do you have Animals?" dropdown
    $("#has_animals").change(function () {
        if ($(this).val() === '1') {
            // If "Yes" is selected, show the animal-related fields
            $("#animalFields").show();
        } else {
            // If "No" is selected, hide the animal-related fields
            $("#animalFields").hide();
        }
    });


    $("#has_animals").change(function () {
        if ($(this).val() === '1') {
            // If "Yes" is selected, show the animal-related fields
            $(".animalFields").show();
        } else {
            // If "No" is selected, hide the animal-related fields
            $(".animalFields").hide();
        }
    });








    // Initialize select2 for district field
    $('#district_id').select2({
        placeholder: "Select an District",
        ajax: {
            url: function () {
                return $('#district_id').data('route');
            },
            dataType: "json",
            type: "GET",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    province_id: 1,
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data?.data, function (item) {
                        return {
                            text: item?.title,
                            id: item?.id,
                        };
                    }),
                    pagination: {
                        more: data.next_page_url !== null,
                    },
                };
            },
            cache: true,
        },
    });


    $('#tehsil_id').select2({
        placeholder: "Select an Tehsil",
        ajax: {
            url: function () {
                return $('#tehsil_id').data('route');
            },
            dataType: "json",
            type: "GET",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    district_id: $('#district_id').val(),
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data?.data, function (item) {
                        return {
                            text: item?.title,
                            id: item?.id,
                        };
                    }),
                    pagination: {
                        more: data.next_page_url !== null,
                    },
                };
            },
            cache: true,
        },
    });


    $('#uc_vc_list_id').select2({
        placeholder: "Select an Tehsil",
        ajax: {
            url: function () {
                return $('#uc_vc_list_id').data('route');
            },
            dataType: "json",
            type: "GET",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    tehsil_id: $('#tehsil_id').val(),
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data?.data, function (item) {
                        return {
                            text: item?.name,
                            id: item?.id,
                        };
                    }),
                    pagination: {
                        more: data.next_page_url !== null,
                    },
                };
            },
            cache: true,
        },
    });



    $('#cropsKharif').select2({
        placeholder: "Select an Crops Rabi",
        ajax: {
            url: function () {
                return $('#cropsKharif').data('route');
            },
            dataType: "json",
            type: "GET",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    crop_season_id: $('#cropsKharif').data('crop_season_id'),
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data?.data, function (item) {
                        return {
                            text: item?.title,
                            id: item?.id,
                        };
                    }),
                    pagination: {
                        more: data.next_page_url !== null,
                    },
                };
            },
            cache: true,
        },
    });

    $('#crops_rabi').select2({
        placeholder: "Select an Crops Rabi",
        ajax: {
            url: function () {
                return $('#crops_rabi').data('route');
            },
            dataType: "json",
            type: "GET",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    crop_season_id: $('#crops_rabi').data('crop_season_id'),
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data?.data, function (item) {
                        return {
                            text: item?.title,
                            id: item?.id,
                        };
                    }),
                    pagination: {
                        more: data.next_page_url !== null,
                    },
                };
            },
            cache: true,
        },
    });



    $('#land-from-repeater').repeater({

        show: function () {
            $(this).slideDown(function () {
                var selects = $('body').find('.dynamic-select');
                var unitSelect = $('body').find('.unit-selector');

                $.each(selects, function (i, selectElement) {
                    $(selectElement).removeClass('select2-hidden-accessible').next('.select2-container').remove();
                    $(selectElement).removeAttr('data-select2-id tabindex aria-hidden');
                });
                initializeSelect2();
                unitSelect.val('jareb');
                var selectedValue = $('#farmer_type_id').val();
                if (selectedValue === "1") {
                    $(".owner_document").removeClass('d-none');
                    $(".other_document").addClass('d-none');
                } else {
                    $(".owner_document").addClass('d-none');
                    $(".other_document").removeClass('d-none');
                }
                // applyClickEventToClass('card-expand');
            });
        },
        hide: function (remove) {
            if (confirm('Are you sure you want to delete this element?')) {
                $(this).slideUp(remove);
            }
        }
    });


    $("#animals_container").on("click", ".delete-component", function () {
        $(this).closest(".row.mb-2").remove(); // Remove the closest parent row
    });


    $("#equipments_container").on("click", ".delete-equiment-component", function () {
        $(this).closest(".row.mb-2").remove(); // Remove the closest parent row
    });

    $('#previewButton').on('click', function () {
        var url = $(this).data('url');
        window.open(url, '_blank');
    });


    //Perfume Converstion For Land Area 
    document.querySelector('.repeater').addEventListener('change', function (event) {
        if (event.target.classList.contains('unit-selector') || event.target.classList.contains('input-value')) {
            var container = event.target.closest('[data-repeater-item]');
            var inputValue = parseFloat(container.querySelector('.input-value').value) || 0;
            var selectedUnit = container.querySelector('.unit-selector').value;
            var acreValue = 0;

            // Perform conversion based on selected unit
            if (selectedUnit === 'square_feet') {
                acreValue = inputValue * 0.000023;
            } else if (selectedUnit === 'square_yard') {
                acreValue = inputValue * 0.000206612;
            } else if (selectedUnit === 'kanal') {
                acreValue = inputValue * 0.125;
            }
            else if (selectedUnit === 'marla') {
                acreValue = inputValue * 0.00625001;
            }
            else if (selectedUnit === 'jareb') {
                acreValue = inputValue * 0.49421076293433;
            } else if (selectedUnit === 'acre') {
                acreValue = inputValue;
            }

            // Set jerab value in the corresponding result input field
            container.querySelector('.result-jerab').value = acreValue.toFixed(6);

            // Set hidden and visible input values (if needed)
            container.querySelector('.hidden-input').value = acreValue.toFixed(6);
            ;
        }
    });



    // perform Conversion on total land 
    document.querySelector('.repeater').addEventListener('change', function (event) {
        if (event.target.classList.contains('unit-selector') || event.target.classList.contains('total_land_area')) {
            var container = event.target.closest('[data-repeater-item]');
            var inputValue = parseFloat(container.querySelector('.total_land_area').value) || 0;
            var selectedUnit = container.querySelector('.unit-selector').value;
            var acreValue = 0;

            // Perform conversion based on selected unit
            if (selectedUnit === 'square_feet') {
                acreValue = inputValue * 0.000023;
            } else if (selectedUnit === 'square_yard') {
                acreValue = inputValue * 0.000206612;
            } else if (selectedUnit === 'kanal') {
                acreValue = inputValue * 0.125;
            }
            else if (selectedUnit === 'marla') {
                acreValue = inputValue * 0.00625001;
            }
            else if (selectedUnit === 'jareb') {
                acreValue = inputValue * 0.49421076293433;
            } else if (selectedUnit === 'acre') {
                acreValue = inputValue;
            }

            // Set jerab value in the corresponding result input field
            container.querySelector('.result-acre-cultivative_land_area').value = acreValue.toFixed(6);

            // Set hidden and visible input values (if needed)
            container.querySelector('.hidden-input-cultivative_land_area').value = acreValue.toFixed(6);
            ;
        }
    });
});







function setFormValues(data) {

    console.log("data is", data);

    // setting Preview Url 
    if (data?.preview_url) {
        var previewButton = document.getElementById("previewButton");
        previewButton.setAttribute("data-url", data?.preview_url || "#");
    }

    // // General Information
    if (data?.farmer_type) {
        const farmerTypeSelect = document.querySelector('select[name="farmer_type_id"]');
        const farmerTypeOption = document.createElement('option');
        farmerTypeOption.value = data?.farmer_type?.id;
        farmerTypeOption.text = data?.farmer_type?.name;
        farmerTypeSelect.innerHTML = '';
        farmerTypeSelect.appendChild(farmerTypeOption);
        // Create a new event
        const changeEvent = new Event('change');

        // Dispatch the change event on the select element
        farmerTypeSelect.dispatchEvent(changeEvent);
    }

    // Set District 
    if (data?.district) {
        const districtSelect = document.querySelector('select[name="district_id"]');
        const districtOption = document.createElement('option');
        districtOption.value = data?.district?.id;
        districtOption.text = data?.district?.title;
        districtSelect.innerHTML = '';
        districtSelect.appendChild(districtOption);
    }

    // Set Tehsil
    if (data?.tehsil) {
        const tehsilSelect = document.querySelector('select[name="tehsil_id"]');
        const tehsilOption = document.createElement('option');
        tehsilOption.value = data?.tehsil?.id;
        tehsilOption.text = data?.tehsil?.title;
        tehsilSelect.appendChild(tehsilOption);
    }

    if (data?.uc_vc) {
        const ucVcSelect = document.querySelector('select[name="uc_vc_list_id"]');
        const ucVcOption = document.createElement('option');
        ucVcOption.value = data?.uc_vc?.id;
        ucVcOption.text = data?.uc_vc?.name;
        ucVcSelect.appendChild(ucVcOption);
    }


    const farmerNameInput = document.querySelector('input[name="farmer_name"]');
    if (farmerNameInput) {
        farmerNameInput.value = data && data.farmer_name || '';
    }

    const fatherNameInput = document.querySelector('input[name="father_name"]');
    if (fatherNameInput) {
        fatherNameInput.value = data && data.father_name || '';
    }

    const dateOfBirthInput = document.querySelector('input[name="date_of_birth"]');
    if (dateOfBirthInput) {
        dateOfBirthInput.value = data && data.date_of_birth;
    }


    const inputElement = document.querySelector('input[name="cnic_number"]');
    if (inputElement) {
        inputElement.value = data?.cnic_number || $('#check_cnic_number').val() || '';
        inputElement.setAttribute('readonly', 'true');
    }


    const cnicIssuanceDateInput = document.querySelector('input[name="cnic_issuance_date"]');
    if (cnicIssuanceDateInput) {
        cnicIssuanceDateInput.value = data && data.cnic_issuance_date;
    }

    const contactNumber1Input = document.querySelector('input[name="contact_number_1"]');
    if (contactNumber1Input) {
        contactNumber1Input.value = data && data.contact_number_1 || '';
    }

    const contactNumber2Input = document.querySelector('input[name="contact_number_2"]');
    if (contactNumber2Input) {
        contactNumber2Input.value = data && data.contact_number_2 || '';
    }

    const alternativeContactNameInput = document.querySelector('input[name="alternative_contact_name"]');
    if (alternativeContactNameInput) {
        alternativeContactNameInput.value = data && data.alternative_contact_name || '';
    }

    const alternativeCnicInput = document.querySelector('input[name="alternative_cnic"]');
    if (alternativeCnicInput) {
        alternativeCnicInput.value = data && data.alternative_cnic || '';
    }

    const alternativeContactNumber1Input = document.querySelector('input[name="alternative_contact_number_1"]');
    if (alternativeContactNumber1Input) {
        alternativeContactNumber1Input.value = data && data.alternative_contact_number_1 || '';
    }



    const addressInput = document.getElementById('address');
    if (addressInput) {
        addressInput.value = data && data.address || '';
    }


    if (data?.gender) {
        const genderSelect = document.querySelector('select[name="gender_id"]');
        const genderOption = document.createElement('option');
        genderOption.value = data?.gender?.id;
        genderOption.text = data?.gender?.name;
        genderSelect.appendChild(genderOption);
    }

    if (data?.alternate_relation_type) {
        const alternateRelationSelect = document.querySelector('select[name="relationship_id"]');
        const alternateRelationOption = document.createElement('option');
        alternateRelationOption.value = data?.alternate_relation_type?.id;
        alternateRelationOption.text = data?.alternate_relation_type?.name;
        alternateRelationSelect.appendChild(alternateRelationOption);
    }


    if (data?.education) {
        const educationSelect = document.querySelector('select[name="education_id"]');
        const educationOption = document.createElement('option');
        educationOption.value = data?.education?.id;
        educationOption.text = data?.education?.title;
        educationSelect.appendChild(educationOption);
    }



    if (data?.hasAnimals) {
        const hasAnimalsSelect = document.getElementById('hasAnimals');
        hasAnimalsSelect.value = data?.hasAnimals;
        hasAnimalsSelect.dispatchEvent(new Event('change'));
    }


    // setting All Fruits 
    if (data?.fruits && data?.fruits.length > 0) {
        const educationSelect = document.querySelector('select[name="fruits[]"]');
        data?.fruits.forEach(function (fruit) {
            const option = document.createElement('option');
            option.value = fruit?.fruit_type?.id;
            option.text = fruit?.fruit_type?.name;
            option.selected = true;
            educationSelect.appendChild(option);
        });
    }

    if (data?.khariffarmer_crops && data?.khariffarmer_crops.length > 0) {
        const cropsSelect = document.getElementById('cropsKharif');
        data?.khariffarmer_crops.forEach(function (crop) {
            const option = document.createElement('option');
            option.value = crop?.crop_type?.id;
            option.text = crop?.crop_type?.title;
            option.selected = true;
            cropsSelect.appendChild(option);
        });
    }

    if (data?.rabifarmer_crops && data?.rabifarmer_crops.length > 0) {
        const cropsSelect = document.getElementById('crops_rabi');
        data?.rabifarmer_crops.forEach(function (crop) {
            const option = document.createElement('option');
            option.value = crop?.crop_type?.id;
            option.text = crop?.crop_type?.title;
            option.selected = true;
            cropsSelect.appendChild(option);
        });
    }

    if (data?.isFather) {
        document.querySelector('input[name="isFather"][value="' + data?.isFather || 0 + '"]').checked = true;
    }


    // // For example, for the "hasAnimals" checkbox
    const hasAnimalsSelect = document.querySelector('select[name="has_animals"]');
    if (hasAnimalsSelect) {
        hasAnimalsSelect.value = data && data.has_animals;
    }



}



