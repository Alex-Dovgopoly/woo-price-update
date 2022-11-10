jQuery(document).ready(function ($) {

    // Display variations table on proguct change
    $('#product').on('change', function () {
        var productType = $(this).find(':selected').attr('data-prod-type');
        var variationsTableElement = $('.variations-row');

        if (productType == 'variable') {
            var data = {
                'action': 'get_product_variations',
                'prod_id': this.value
            };

            $.post(ajaxurl, data, function (response) {
                if (response !== null) {
                    variationsTableElement.html(response);
                } else {
                    displayFormNotification('Somthing went wrong!', 4000);
                }
            });

        } else {
            variationsTableElement.empty();
        }
    });

    // Update product price on form submit
    $("#update_product_price").submit(function (e) {
        e.preventDefault();

        var productType = $('#product').find(':selected').attr('data-prod-type');

        // Check variable product. Is checked at least one variation?
        if (productType == 'variable' && !validateCheckboxes()) {
            displayFormNotification('Choose at least one variation', 5000);

            return;
        }

        var formData = new FormData($(this).get(0));
        formData.append("action", "update_price_submit");

        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: onSuccessUpdate,
            error: onErrorUpdate,
        });

    });

    function onSuccessUpdate(data, status) {
        $("#update_product_price").trigger('reset');
        $('.variations-row').empty();
        displayFormNotification('Product update success', 4000);
    }

    function onErrorUpdate(data, status) {
        displayFormNotification('Somthing went wrong!', 4000);
    }

    // Validate checkboxes of variation product
    function validateCheckboxes() {
        var isCheckedAny = false;

        $(".variations-row input[type=checkbox]").each(function () {
            if ($(this).prop('checked')) isCheckedAny = true;
        });

        return isCheckedAny;
    }

    // Display form notification
    function displayFormNotification(text, timeout) {
        $(".woo-price-update-notification").html(text);

        setTimeout(function () {
            $(".woo-price-update-notification").html('');
        }, timeout);
    }

});