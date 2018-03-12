jQuery(document).ready(function ($) {

    $('.bpe_form_select-country select').select2({
        placeholder: "Select a Country",
        allowClear: true
    });
    $('.bpe_form_select-country select').on("select2-close", function () {
        $('.bpe_form_select-country select').select2("open");
    });

    $('.bpe_form_select-network_type').on('change', function () {

        $(".bpe_form_select-country select").select2('val', '');
        $(".bpe_form_select-country option").prop('disabled', true);
        $(".bpe_form_select-country select").prop('disabled', true);

        var aNetworkType = [];
        $(this).find(":selected").each(function () {
            aNetworkType.push(this.value);
        });

        if (aNetworkType.length > 0) {

            var data = {
                action: 'bpe_category_validaion',
                network_cats: aNetworkType
            };


            $.ajax({
                url: bpe_object.ajax_url,
                type: "POST",
                data: data,
                beforeSend: function () {

                },
                success: function (result, textStatus) {
                    $.each(result.data, function (i, v) {
                        //$(".bpe_form_select-country option:contains('Value " + v + "')").prop('disabled',true);
                        $(".bpe_form_select-country option[value*='" + v + "']").prop('disabled', false);
                        $(".bpe_form_select-country select").prop('disabled', false);

                    });

                    $(".bpe_form_select-country select").val($(".bpe_form_select-country select").val());
                   
                    $('.bpe_form_select-country select').select2("destroy").select2({
                        placeholder: "Select a Country",
                        allowClear: true
                    });
                },
                error: function () {
                    $(".bpe_form_select-country select").prop('disabled', false);
                    $('.bpe_form_select-country select').select2("destroy").select2({
                        placeholder: "Select a Country",
                        allowClear: true
                    });
                }
            });
        }

        $('.bpe_form_select-country select').select2("destroy").select2({
            placeholder: "Select a Country",
            allowClear: true
        });
    });
});