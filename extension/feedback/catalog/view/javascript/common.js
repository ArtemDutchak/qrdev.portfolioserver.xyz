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

toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": true,
  "progressBar": true,
  "positionClass": "toast-top-right",
  "preventDuplicates": false,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}

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
                
                toastr.error(json.errors.join());
                
            }else if (json.redirect) {
                
                window.open(json.redirect, '_blank');
                start_checking_for_success_payment(json.order_id);
                
            }else if (json.success) {
                
                location.href = json.success;
                
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
    
}

function start_checking_for_success_payment(order_id) {
    
    let check_interval = setInterval(()=>{check_for_success_payment(order_id)}, 1000);

    function check_for_success_payment(order_id) {

        console.log(order_id);
        // clearInterval(check_interval);

        $.ajax({
            url: 'index.php?route=checkout/confirm|check_payment',
            dataType: 'json',
            method: 'POST',
            data:{order_id},
            success: function(json) {
                if (json.redirect) {
                    location.href = json.redirect;
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
        
    }
    
}