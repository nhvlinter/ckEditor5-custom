export default function H3ConversionPlugin(editor) {
    // Allow <p> elements in the model.
    editor.model.schema.register( 'h3', {
        allowWhere: '$block',
        allowContentOf: '$root',
        isObject: true,
    } );

    // Allow <p> elements in the model to have all attributes.
    editor.model.schema.addAttributeCheck( context => {
        if ( context.endsWith( 'h3' ) ) {
            return true;
        }
    } );

    // The view-to-model converter converting a view <p> with all its attributes to the model.
    editor.conversion.for( 'upcast' ).elementToElement( {
        view: 'h3',
        model: ( viewElement, { writer: modelWriter } ) => {
            return modelWriter.createElement( 'h3', viewElement.getAttributes() );
        }
    } );

    // The model-to-view converter for the <p> element (attributes are converted separately).
    editor.conversion.for( 'downcast' ).elementToElement( {
        model: 'h3',
        view: 'h3'
    } );

    // The model-to-view converter for <p> attributes.
    // Note that a lower-level, event-based API is used here.
    editor.conversion.for( 'downcast' ).add( dispatcher => {
        dispatcher.on( 'attribute', ( evt, data, conversionApi ) => {
            // Convert <p> attributes only.
            if ( data.item.name != 'h3' ) {
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