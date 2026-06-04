


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
        $("#owner_document").removeClass('d-none');
        $("#other_document").addClass('d-none');
    } else {
        $("#owner_document").addClass('d-none');
        $("#other_document").removeClass('d-none');
    }
});

$(document).ready(function () {

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
    $("#hasAnimals").change(function () {
        if ($(this).val() === '1') {
            // If "Yes" is selected, show the animal-related fields
            $("#animalFields").show();
        } else {
            // If "No" is selected, hide the animal-related fields
            $("#animalFields").hide();
        }
    });


    $("#hasAnimals").change(function () {
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





});






$("#animals_container").on("click", ".delete-component", function () {
    $(this).closest(".row.mb-2").remove(); // Remove the closest parent row
});


$("#equipments_container").on("click", ".delete-equiment-component", function () {
    $(this).closest(".row.mb-2").remove(); // Remove the closest parent row
});


function setFormValues(data) {

    console.log("data is", data);
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

    // document.querySelector('input[name="mouzasVillage"]').value = data.mouzasVillage;



    // Append the cloned element to a container
    // $('#container').append(clonedFileUploader);
    // // Background Information
    document.querySelector('input[name="farmer_name"]').value = data?.farmer_name;
    document.querySelector('input[name="father_name"]').value = data?.father_name;
    document.querySelector('input[name="date_of_birth"]').value = data?.date_of_birth;
    document.querySelector('input[name="cnic_number"]').value = data?.cnic_number;
    document.querySelector('input[name="cnic_issuance_date"]').value = data?.cnic_issuance_date;
    document.querySelector('input[name="contact_number_1"]').value = data?.contact_number_1;
    document.querySelector('input[name="contact_number_2"]').value = data?.contact_number_2;
    document.querySelector('input[name="alternative_contact_name"]').value = data?.alternative_contact_name;
    document.querySelector('input[name="alternative_cnic"]').value = data?.alternative_cnic;
    document.querySelector('input[name="alternative_contact_number_1"]').value = data?.alternative_contact_number_1;
    document.querySelector('input[name="alternative_contact_number_2"]').value = data?.alternative_contact_number_2;

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





    // // ... Repeat this for other fields

    // // Agricultural Land and Assets Information
    document.querySelector('input[name="khasra_number"]').value = data?.khasra_number;
    document.querySelector('input[name="landOwnership"]').value = data?.landOwnership;
    document.querySelector('input[name="totalLandArea"]').value = data?.totalLandArea;



    // setting All Fruits 
    if (data?.fruits.length > 0) {
        const educationSelect = document.querySelector('select[name="fruits[]"]');
        data?.fruits.forEach(function (fruit) {
            const option = document.createElement('option');
            option.value = fruit?.fruit_type?.id;
            option.text = fruit?.fruit_type?.name;
            option.selected = true;
            educationSelect.appendChild(option);
        });
    }

    if (data?.khariffarmer_crops.length > 0) {
        const cropsSelect = document.getElementById('cropsKharif');
        data?.khariffarmer_crops.forEach(function (crop) {
            const option = document.createElement('option');
            option.value = crop?.crop_type?.id;
            option.text = crop?.crop_type?.title;
            option.selected = true;
            cropsSelect.appendChild(option);
        });
    }

    if (data?.rabifarmer_crops.length > 0) {
        const cropsSelect = document.getElementById('crops_rabi');
        data?.rabifarmer_crops.forEach(function (crop) {
            const option = document.createElement('option');
            option.value = crop?.crop_type?.id;
            option.text = crop?.crop_type?.title;
            option.selected = true;
            cropsSelect.appendChild(option);
        });
    }
    // // Additional Logic for Multi-Selects
    // // For example, for multiple crops selection
    // const selectedCropsRabi = data.cropsRabi; // Assuming it's an array of selected crop values
    // const cropsRabiSelect = document.querySelector('select[name="cropsRabi[]"]');
    // for (let option of cropsRabiSelect.options) {
    //     option.selected = selectedCropsRabi.includes(option.value);
    // }

    // // ... Repeat this for other multi-select fields

    // // Additional Logic for Radio Buttons
    // // For example, for the "isFather" radio buttons
    document.querySelector('input[name="isFather"][value="' + data?.isFather + '"]').checked = true;

    // // ... Repeat this for other radio button fields

    // // Additional Logic for Checkboxes
    // // For example, for the "hasAnimals" checkbox
    document.querySelector('select[name="hasAnimals"]').value = data?.hasAnimals;


}
