jQuery(document).ready(function(){

    jQuery('#arvan-total-purge').on('click',function(){

        $purge_button = jQuery(this);

        
        request_total_purge();

        //$purge_button.prop('disabled', true);
        
    });

});


function request_total_purge(){
    
    const wpnounce = jQuery('#_wpnonce').val();
    


    var request = jQuery.ajax({
        url: WPData.ajaxurl,
        method: "POST",
        data: { action: "arvan_total_purge", nonce: wpnounce },
        dataType: "json"
      });
       
      request.done(function( msg ) {
        
        if(msg.data.message.trim() == "cdn/msg.caching.purge"){
          alert('Done');
          return;
        }
        alert(msg.data.message);
      });
       
      request.fail(function( jqXHR, textStatus ) {
        console.log(textStatus);
      });

}