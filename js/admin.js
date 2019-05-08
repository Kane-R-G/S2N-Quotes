(function($){
    $(document).ready( function(){

        $('.edit-quotes').click( function(e) {
            console.log('CLICKED');
            e.preventDefault();
            var quote = $(this).attr('data-quote');
            window.location.href = '/wp-admin/admin.php?page=s2n_quotes_options&tab=edit&edit=' + quote;
        });

    });
})(jQuery);
    
