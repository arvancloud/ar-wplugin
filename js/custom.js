jQuery(document).ready(function(){

    let $purge_button;

    jQuery('#arvan-total-purge').on('click',function(){

        $purge_button = jQuery(this);

        $purge_button.prop('disabled', true);
        request_total_purge($purge_button);
        //$purge_button.prop('disabled', false);
        
        
    });

});


function request_total_purge($purge_button){
    
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
          $purge_button.prop('disabled', false);
          return;
        }
        $purge_button.prop('disabled', false);
        alert(msg.data.message);
      });
       
      request.fail(function( jqXHR, textStatus ) {
        console.log(textStatus);
        $purge_button.prop('disabled', false);
      });

}