/**
 * Created by Lazarev Aleksey on 22.08.16.
 */


$(document).ready(function () {
    setInterval(function () {

            $.ajax({
                dataType: "json",
                url: "/ajax/index",
                success: function (data) {
                    $.each(data, function (index, currencies) {
                        $('#' + currencies.name).html(currencies.value);
                    });
                }
            });
        }
        , 5000);
});
