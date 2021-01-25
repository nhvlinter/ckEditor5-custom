export default function H1ConversionPlugin(editor) {
    // Allow <p> elements in the model.
    editor.model.schema.register( 'h1', {
        allowWhere: '$block',
        allowContentOf: '$root',
    } );


    // Allow <p> elements in the model to have all attributes.
    editor.model.schema.addAttributeCheck( context => {
        if ( context.endsWith( 'h1' ) ) {
            return true;
        }
    } );

    // The view-to-model converter converting a view <p> with all its attributes to the model.
    editor.conversion.for( 'upcast' ).elementToElement( {
        view: 'h1',
        model: ( viewElement, { writer: modelWriter } ) => {
            return modelWriter.createElement( 'h1', viewElement.getAttributes() );
        }
    } );

    // The model-to-view converter for the <p> element (attributes are converted separately).
    editor.conversion.for( 'downcast' ).elementToElement( {
        model: 'h1',
        view: 'h1'
    } );

    // The model-to-view converter for <p> attributes.
    // Note that a lower-level, event-based API is used here.
    editor.conversion.for( 'downcast' ).add( dispatcher => {
        dispatcher.on( 'attribute', ( evt, data, conversionApi ) => {
            // Convert <p> attributes only.
            if ( data.item.name != 'h1' ) {
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