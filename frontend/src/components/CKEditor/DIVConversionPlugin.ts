export default function DIVConversionPlugin(editor) {
    // Allow <div> elements in the model.
    editor.model.schema.register( 'div', {
        allowWhere: '$block',
        allowContentOf: '$root'
    } );

    // Allow <div> elements in the model to have all attributes.
    editor.model.schema.addAttributeCheck( context => {
        if ( context.endsWith( 'div' ) ) {
            return true;
        }
    } );

    // The view-to-model converter converting a view <div> with all its attributes to the model.
    editor.conversion.for( 'upcast' ).elementToElement( {
        view: 'div',
        model: ( viewElement, { writer: modelWriter } ) => {
            return modelWriter.createElement( 'div', viewElement.getAttributes() );
        }
    } );

    // The model-to-view converter for the <div> element (attributes are converted separately).
    editor.conversion.for( 'downcast' ).elementToElement( {
        model: 'div',
        view: 'div'
    } );

    // The model-to-view converter for <div> attributes.
    // Note that a lower-level, event-based API is used here.
    editor.conversion.for( 'downcast' ).add( dispatcher => {
        dispatcher.on( 'attribute', ( evt, data, conversionApi ) => {
            // Convert <div> attributes only.
            if ( data.item.name != 'div' ) {
                return;
            }

            const viewWriter = conversionApi.writer;
            const viewDiv = conversionApi.mapper.toViewElement( data.item );

            // In the model-to-view conversion we convert changes.
            // An attribute can be added or removed or changed.
            // The below code handles all 3 cases.
            if ( data.attributeNewValue ) {
                viewWriter.setAttribute( data.attributeKey, data.attributeNewValue, viewDiv );
            } else {
                viewWriter.removeAttribute( data.attributeKey, viewDiv );
            }
        } );
    } );
}