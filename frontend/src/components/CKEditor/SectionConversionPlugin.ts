export default function SectionConversionPlugin(editor) {
    // Allow <section> elements in the model.
    editor.model.schema.register( 'section', {
        allowWhere: '$block',
        allowContentOf: '$root'
    } );

    // Allow <section> elements in the model to have all attributes.
    editor.model.schema.addAttributeCheck( context => {
        if ( context.endsWith( 'section' ) ) {
            return true;
        }
    } );

    // The view-to-model converter converting a view <section> with all its attributes to the model.
    editor.conversion.for( 'upcast' ).elementToElement( {
        view: 'section',
        model: ( viewElement, { writer: modelWriter } ) => {
            return modelWriter.createElement( 'section', viewElement.getAttributes() );
        }
    } );

    // The model-to-view converter for the <section> element (attributes are converted separately).
    editor.conversion.for( 'downcast' ).elementToElement( {
        model: 'section',
        view: 'section'
    } );

    // The model-to-view converter for <section> attributes.
    // Note that a lower-level, event-based API is used here.
    editor.conversion.for( 'downcast' ).add( dispatcher => {
        dispatcher.on( 'attribute', ( evt, data, conversionApi ) => {
            // Convert <section> attributes only.
            if ( data.item.name != 'section' ) {
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