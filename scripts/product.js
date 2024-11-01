
// Changes the SKU data value on the button when a variation product is chosen
jQuery( function() {
    jQuery( 'input.variation_id' ).change( function() {
        var sku = jQuery( '#viubox-syz-variation-main-sku' ).val();
        if ( '' != jQuery( this ).val() ) {
            var variationID = jQuery( this ).val();
            if ( jQuery( '#viubox-syz-variation-sku-' + variationID ).length > 0 ) {
                sku = jQuery( '#viubox-syz-variation-sku-' + variationID ).val();
            }
        }
        jQuery( '#viubox-syz-measurments' ).attr( 'data-syzsku', sku );
    });
});
