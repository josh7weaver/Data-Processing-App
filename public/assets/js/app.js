$(function(){
   // for delete method
   $('[data-method]').append(function(){
        return "\n"+
        "<form action='"+$(this).attr('href')+"' method='POST' style='display:none'>\n"+
        "   <input type='hidden' name='_method' value='"+$(this).attr('data-method')+"'>\n"+
        "   <input type='hidden' name='_token' value='"+$(this).attr('data-token')+"'>\n"+
        "</form>\n"
   })
   .removeAttr('href')
   .attr('style','cursor:pointer;')
   .attr('onclick','$(this).find("form").submit();');

    // for school lookup selector
    $('#school-lookup').change(function(){
        var selector = $(this),
            schoolId = selector.val(),
            token = selector.data('process-token');

        console.log("school id = "+schoolId);
        console.log("token = "+token);

        window.location.href = "/reports/"+token+"/school-id/"+schoolId;
    })
});