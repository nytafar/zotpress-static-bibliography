/**
 * Zotpress Editor Button
 * Adds a Zotpress citation button to the WordPress Block Editor toolbar
 */

( function( wp ) {
    const { registerFormatType, toggleFormat } = wp.richText;
    const { RichTextToolbarButton } = wp.blockEditor;
    const { createElement } = wp.element;
    const { __ } = wp.i18n;
    
    // SVG icon for Zotpress
    const ZotpressIcon = createElement( 'svg', {
        width: 24,
        height: 24,
        viewBox: '0 0 24 24',
        xmlns: 'http://www.w3.org/2000/svg'
    }, createElement( 'path', {
        d: 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-3.3 14H8.1v-1.5h7.6V17zm.3-3H8.1v-1.5h7.9V14zm0-3H8.1V9.5h7.9V11zm0-3H8.1V6.5h7.9V8z',
        fill: 'currentColor'
    } ) );
    
    // Register a new format type for Zotpress citations
    registerFormatType( 'zotpress-static-bibliography/citation', {
        title: __( 'Zotpress Citation', 'zotpress-static-bibliography' ),
        tagName: 'span',
        className: 'zotpress-citation',
        edit: function( props ) {
            const { isActive, value, onChange } = props;
            
            return createElement( RichTextToolbarButton, {
                icon: ZotpressIcon,
                title: __( 'Zotpress Citation', 'zotpress-static-bibliography' ),
                onClick: function() {
                    // This is where we'll trigger the Zotpress citation dialog
                    // For now, we'll just open the same menu that the existing button opens
                    const zotpressButton = document.querySelector( '.components-dropdown-menu__menu-item[aria-label="Zotpress Shortcode"]' );
                    if ( zotpressButton ) {
                        zotpressButton.click();
                    } else {
                        // Fallback if we can't find the button
                        alert( __( 'Zotpress Shortcode button not found. Please use the dropdown menu.', 'zotpress-static-bibliography' ) );
                    }
                },
                isActive: isActive
            } );
        }
    } );
} )( window.wp );
