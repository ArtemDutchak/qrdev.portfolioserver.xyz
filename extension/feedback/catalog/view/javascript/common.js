$(document).ready(function() {
    
    $(document).on("click", ".lang-select", function(){
        
		if ($(this).hasClass('active')) {
			return;
		}
        
        let lang = $(this).attr('data-code')
        
      $.ajax({
        url: 'index.php?route=ajax/language|set_lang',
        type: 'post',
        data: {lang},
        dataType: 'json',
        success: function(res) {
          if (res.success) {
            location.reload()
          }
        }
      });
      
	})
    
})



function setTariff(tariffId) {

    const month = $('#mouth_' + tariffId).val();
    const data = {
        tariff_id: tariffId,
        month: month
    };
    
    if(!month || !tariffId){
        console.error('wrong data!');
    }

    $.ajax({
        url: 'index.php?route=checkout/confirm_tariff',
        dataType: 'json',
        method: 'POST',
        data,
        success: function(json) {
            if (json.errors.length) {
                alert(json.errors.join());
            }else{
                if (json.redirect) {
                    window.open(json.redirect, '_blank');
                }
                location.reload();
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
    
}