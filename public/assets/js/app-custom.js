$(document).ready(function () {


    var maxlengthInput = $('.maxlength-number-input');
    if (maxlengthInput.length) {
        maxlengthInput.each(function () {
            $(this).maxlength({
                warningClass: 'label label-success bg-success text-white',
                limitReachedClass: 'label label-danger',
                separator: '/',
                preText: '',
                postText: '',
                validate: true,
                threshold: +this.getAttribute('maxlength')
            });

            // Add input event listener to allow only numeric values
            this.addEventListener('input', function (event) {
                var newValue = event.target.value.replace(/\D/g, ''); // Remove non-numeric characters
                event.target.value = newValue;
            });
        });
    }



    $(".cnic-input").on("input", function () {
        let cnic = $(this).val();
        cnic = cnic.replace(/[^0-9]/g, ""); // Remove non-numeric characters
        cnic = cnic.slice(0, 13); // Limit the length to 13 characters

        // Add hyphens at the appropriate positions
        if (cnic.length >= 5) {
            cnic = cnic.slice(0, 5) + "-" + cnic.slice(5);
        }
        if (cnic.length >= 13) {
            cnic = cnic.slice(0, 13) + "-" + cnic.slice(13);
        }

        $(this).val(cnic);
    });


    // ini Select 2
    initializeSelect2();
    // applyClickEventToClass('card-expand');

    if ($("#myDataTable").length) {
        var dataTable = $("#myDataTable");
        var route = dataTable.data("route");
        var columns = (dataTable.attr("raw-columns") || "").split(",");
        var relations = (dataTable.attr("raw-relations") || "").split(",");
        var filterColumns = columns.map((item) => ({
            data: item,
        }));

        var table = dataTable.DataTable({
            pagingType: "full_numbers",
            lengthMenu: [25, 75, 100, 150],
            pageLength: 25,
            processing: true,
            serverSide: true,
            ajax: {
                url: route,
                data: {
                    relations: relations || "",
                },
            },
            columns: filterColumns,
        });
    }

    //delete Any Data
    $("#myDataTable").on("click", ".delete-btn", function () {
        var deleteUrl = $(this).data("url");
        // Show confirmation dialog
        if (confirm("Are you sure you want to delete this item?")) {
            // Send AJAX request to delete the data
            $.ajax({
                url: deleteUrl,
                type: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
                },
                success: function (data) {
                    $("#myDataTable")
                        .DataTable()
                        .row($(this).closest("tr"))
                        .remove()
                        .draw();
                },
                error: function () {
                    alert("An error occurred while deleting the data.");
                },
            });
        }
    });
});


// depend On Function 


//function For Dynamic Select 2
function initializeSelect2(filter_value = null) {
    $(".dynamic-select").each(function () {
        var $select = $(this);
        var route = $select.data("route");
        var model = $select.data("model");
        var value = $select.data("value");
        var label = $select.data("label");
        var connection = $select.data("connection");
        var dropdownParent = $select.data("parent");
        var filter_name = $select.data('filter_name');
        var isShowNull = $select.data('isshownull');
        var data = {}
        if (filter_value && filter_name) {
            data = { filter_value: filter_value, filter_name: filter_name }
        }
        var select2Options = {
            placeholder: $select.data("placeHolder") || "Select an option",
            ajax: {
                url: route,
                dataType: "json",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1,
                        model: model,
                        value: value,
                        label: label,
                        isShowNull: isShowNull,
                        connection: connection || "mysql",
                        ...data
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data?.data, function (item) {
                            return {
                                text: item?.label,
                                id: item?.value,
                            };
                        }),
                        pagination: {
                            more: data.next_page_url !== null,
                        },
                    };
                },
                cache: true,
            },
        };

        if (dropdownParent) {
            select2Options.dropdownParent = $(dropdownParent);
        }
        // Initialize Select2 with the options
        $select.select2(select2Options);
    });
}

function initializeSelect2single(element) {
    var $select = $(element);
    var route = $select.data("route");
    var model = $select.data("model");
    var value = $select.data("value");
    var label = $select.data("label");
    var connection = $select.data("connection");
    var dropdownParent = $select.data("parent");

    var select2Options = {
        placeholder: $select.data("placeHolder") || "Select an option",
        ajax: {
            url: route,
            dataType: "json",
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    model: model,
                    value: value,
                    label: label,
                    connection: connection || "mysql",

                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data?.data, function (item) {
                        return {
                            text: item?.label,
                            id: item?.value,
                        };
                    }),
                    pagination: {
                        more: data.next_page_url !== null,
                    },
                };
            },
            cache: true,
        },
    };

    if (dropdownParent) {
        select2Options.dropdownParent = $(dropdownParent);
    }
    // Initialize Select2 with the options
    $select.select2(select2Options);
}




// function Apply Card Expand property having Same Class name
// function applyClickEventToClass(className) {

//     const expandElementList = document.querySelectorAll('.' + className);

//     expandElementList.forEach(expandElement => {
//         expandElement.addEventListener('click', event => {
//             event.preventDefault();
//             // Toggle class bx-fullscreen & bx-exit-fullscreen
//             Helpers._toggleClass(expandElement.firstElementChild, 'bx-fullscreen', 'bx-exit-fullscreen');

//             const card = expandElement.closest('.card');
//             if (card) {
//                 card.classList.toggle('card-fullscreen');
//             }
//         });
//     });
// }



function startLoading(message = 'Loading...', props = {}) {
    Swal.fire({
        // title: 'Loading...',
        html: `<div class="spinner-grow text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p>${message}</p>`,
        allowEscapeKey: false,
        allowOutsideClick: false,
        showConfirmButton: false,
        ...props
    });
}


function stopLoading() {
    Swal.close();
}
