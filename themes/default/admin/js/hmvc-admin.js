$(function() {
    $('#side-menu').metisMenu();
    $(window).bind("load resize", function() {
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.sidebar-collapse').addClass('collapse')
        } else {
            $('div.sidebar-collapse').removeClass('collapse')
        }
    })


});

function H_CHECK_ALL(_name) {
    $("input[name='" + _name + "']").each(function() {
        console.log(this.checked);
        if (this.checked) {
            $(this).prop("checked", false);
        } else {
            $(this).prop("checked", true);
        }
    });

}

function goBack(url){    
    location.href = url;
}


function H_Confirm(message){    
    if(!message){
        message = 'Are you sure?'
    }
    if(confirm(message)){
        return true;
    }
    return false;
}


