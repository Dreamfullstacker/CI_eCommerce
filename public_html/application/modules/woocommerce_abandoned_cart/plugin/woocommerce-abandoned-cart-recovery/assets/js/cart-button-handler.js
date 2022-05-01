(function( window, $ ) {
    
    $(document).ready(function() {
        
        /* Submit button */    
        var xit_wacr_cart_button = $('.single_add_to_cart_button'),
            xit_wacr_checkout = $('.woocommerce-checkout'),
            xit_wacr_unchecked_msg = $('#xit-unchecked-car-button-message'),
            cart_user_unique_ref = $('#cart_user_unique_ref'),
            xit_wacr_checkbox_status = $('#xit-wacr-checkbox-status');
        
        /* Maintains cookies */
        var unique_ref_id = cart_user_unique_ref.val();
        
        eraseCookie('cart_user_unique_ref');
        eraseCookie('xit_wacr_checkbox_status');
        
        setCookie('cart_user_unique_ref', unique_ref_id, 1);
        setInterval(function() {
            setCookie('xit_wacr_checkbox_status', window.myCheckBoxState_01, 1);
        }, 100);
           
        /* Unbinds everything from checkout form */
        $(xit_wacr_checkout).unbind();
           
        /* Checks if the FB checkbox is checked */
        xit_wacr_cart_button.on('click', function(e) {
            e.preventDefault();
            
            if (window.myCheckBoxState_01 === 'checked') {
                xit_wacr_checkbox_status.val(window.myCheckBoxState_01);
                $(this).unbind('click');
                $(this).trigger('click');
                
                setCookie('xit_wacr_checkbox_status', window.myCheckBoxState_01, 1);
                xit_wacr_unchecked_msg.hide();
            } 
            
            if (window.myCheckBoxState_01 === 'unchecked') {
                e.preventDefault();
                
                xit_wacr_checkbox_status.val(window.myCheckBoxState_01);
                setCookie('xit_wacr_checkbox_status', window.myCheckBoxState_01, 1);
                xit_wacr_unchecked_msg.show();
            }
        });
        
        /* Triggers click event on skipping FB checkbox */
        $('#xit-skip-fb-checkbox').on('click', function() {
            xit_wacr_cart_button.unbind('click');
            xit_wacr_cart_button.trigger('click');
        });
        
        /* Defines cookie handler functions */
        /* Credits - https://stackoverflow.com/a/24103596 */
        function setCookie(name, value, hours) {
            var expires = "";
            
            if (hours) {
                var date = new Date();
                date.setTime(date.getTime() + (hours*60*60*1000));
                expires = "; expires=" + date.toUTCString();
            }
            
            document.cookie = name + "=" + (value || "")  + expires + "; path=/";
        }
        
        function getCookie(name) {
            var nameEQ = name + "=";
            
            var ca = document.cookie.split(';');
            
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) === '0') return c.substring(nameEQ.length,c.length);
            }
            
            return null;
        }
        
        function eraseCookie(name) {   
            document.cookie = name+'=; Max-Age=-99999999;';  
        }
    });
    
})( window, jQuery );
    


