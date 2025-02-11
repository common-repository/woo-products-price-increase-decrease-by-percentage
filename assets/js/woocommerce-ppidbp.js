(function ($) {
    "use strict";

    var WooCommercePPIDBP = {
        formSubmit: function() {
            var form = $("#wcppidbp_go"),
                resultsWrapper = $("#wcppidbp_results"),
                loadingImg = $(".wcppidbp_loading");

            form.on("submit", function (ev) {
                ev.preventDefault();

                var me = $(this),
                    button = me.find('button');

                $.ajax({
                    cache: false,
                    url: woocommerce_ppidbp.ajax_url,
                    type: "POST",
                    dataType: "json",
                    data: me.serialize() + '&action=' + me.data('action'),
                    beforeSend: function () {
                        resultsWrapper.empty();
                        loadingImg.show();
                    },
                    success: function (data) {
                        loadingImg.hide();
                        resultsWrapper.html(data.results);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            })
        },
        init: function () {
            $(document).ready(this.formSubmit());
        }
    };

    WooCommercePPIDBP.init();
})(jQuery);