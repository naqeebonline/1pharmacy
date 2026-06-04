


$(document).ready(function () {
    initializeSelect2();

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

function initializeSelect2(filter_value = null) {
    $(".dynamic-select").each(function () {
        var $select = $(this);
        var route = $select.data("route");
        var statment = $select.data("statment");
        var dropdownParent = $select.data("parent");
        var isShowAll = $select.data("show_all");

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
                        token: statment,
                    };
                },
                processResults: function (data, params) {
                    // Add the "All" option only if it's the first page
                    var addAllOption = params.page || 1;

                    // Map the Ajax results to Select2 format
                    var results = $.map(data?.data, function (item) {
                        return {
                            text: item?.label,
                            id: item?.value,
                        };
                    });



                    if (addAllOption === 1 && isShowAll) {
                        var allOption = {
                            text: 'All',
                            id: '0'
                        };
                        results.unshift(allOption);
                    }

                    return {
                        results: results,
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