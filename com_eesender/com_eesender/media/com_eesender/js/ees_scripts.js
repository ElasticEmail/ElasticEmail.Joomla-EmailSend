$(document).ready(function(){
    
    $("#radio1").on( 'click' ,function(){
        $(this).attr("checked","no");
        $("#radio2").attr("checked", false);
    });
    $('#radio2').on( 'click', function(){
        $(this).attr("checked", true);
        $("#radio1").attr("checked", false);

    });
    $("#radio3").on( 'click' ,function(){
        $(this).attr("checked","no");
        $("#radio4").attr("checked", false);
    });
    $('#radio4').on( 'click', function(){
        $(this).attr("checked", true);
        $("#radio3").attr("checked", false);

    });
    

});

