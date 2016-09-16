$( document ).ready(function() {


    $('.result-item').addClass('loaded');
    $('#mysubmit').on('click', function () {
        $('#myform').submit();
    });

    $(".search-live").keyup(function () {
        var searchid = $(this).val();
        var dataString = 'liveSearch=' + searchid;
        if (searchid != '') {
            $.ajax({
                type: "POST",
                url: "/ajax/liveSearch.php",
                data: dataString,
                cache: false,
                success: function (html) {
                    console.log(dataString);
                    $("#result-live").html(html).show();

                    $("#mysubmit").show();
                }
    });
        }
        return false;
    });


    /*jQuery("#result-live").on("click",function(e){
     var $clicked = $(e.target);
     var $name = $clicked.find('.name').html();
     var decoded = $("<div/>").html($name).text();
     $('#search').val(decoded);
     });*/
    jQuery(document).on("click", function (e) {
        var $clicked = $(e.target);
        if (!$clicked.hasClass("search-live")) {
            jQuery("#result-live").fadeOut();
            $("#mysubmit").fadeOut();
        }
    });
    $('#search').click(function () {
        jQuery("#result-live").fadeIn();
        $("#mysubmit").fadeIn();
    });



});
