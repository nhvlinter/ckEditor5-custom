export default function PConverstionPlugin(editor) {
    // Allow <p> elements in the model.
    editor.model.schema.register( 'p', {
        allowWhere: '$block',
        allowContentOf: '$root',
        isObject: true,
        allowAttributes: ['class', 'id']
    } );

    // Allow <p> elements in the model to have all attributes.
    editor.model.schema.addAttributeCheck( context => {
        if ( context.endsWith( 'p' ) ) {
            return true;
        }
    } );

    // The view-to-model converter converting a view <p> with all its attributes to the model.
    editor.conversion.for( 'upcast' ).elementToElement( {
        view: 'p',
        model: ( viewElement, { writer: modelWriter } ) => {
            return modelWriter.createElement( 'p', viewElement.getAttributes() );
        }
    } );

    // The model-to-view converter for the <p> element (attributes are converted separately).
    editor.conversion.for( 'downcast' ).elementToElement( {
        model: 'p',
        view: 'p'
    } );

    // The model-to-view converter for <p> attributes.
    // Note that a lower-level, event-based API is used here.
    editor.conversion.for( 'downcast' ).add( dispatcher => {
        dispatcher.on( 'attribute', ( evt, data, conversionApi ) => {
            // Convert <p> attributes only.
            if ( data.item.name != 'p' ) {
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