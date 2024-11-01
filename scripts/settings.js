
// We turn some of the fields into a color picker button
jQuery( function() {
    jQuery( '#viubox-syz-text-color' ).wpColorPicker();
    jQuery( '#viubox-syz-text-color-hover' ).wpColorPicker();
    jQuery( '#viubox-syz-background-color' ).wpColorPicker();
    jQuery( '#viubox-syz-background-color-hover' ).wpColorPicker();
    jQuery( '#viubox-syz-border-color' ).wpColorPicker();
    jQuery( '#viubox-syz-border-color-hover' ).wpColorPicker();
});

// Asks for confirmation and submits the form to resets the settings
function ResetButtonSettings() {
    confirmResult = confirm( localizedButtonData.confirmReset );
    if ( true !== confirmResult ) {
        return;
    }
    jQuery( '#viubox-syz-reset-hidden' ).val( 'yes' );
    jQuery( '#viubox-syz-settings-form' ).submit();
}
