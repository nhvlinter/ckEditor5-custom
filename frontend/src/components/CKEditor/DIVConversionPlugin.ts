export default function DIVConversionPlugin(editor) {
    let debug = false;
    if(debug){console.log("convertDiv")}

    this.editor = editor;
    this.init = function () {
        if(debug){console.log("convertDiv.init")}
        let thisEditor = this.editor;
        thisEditor.model.schema.register( 'div', {
            allowWhere: '$text',
            allowContentOf: '$block'
        } );
    };

    this.afterInit = function () {
        if(debug){console.log("convertDiv.afterInit")}
        let thisEditor = this.editor;

        thisEditor.model.schema.addAttributeCheck( context => {
            if(debug){console.log("convertDiv.addAttributeCheck", {
                context : context
            })}
            if ( context.endsWith( 'div' ) ) {
                if(debug){console.log("convertDiv.addAttributeCheck.endsWith( 'div' )")}
                return true;
            }
        } );
        // The view-to-model converter converting a view <div> with all its attributes to the model.
        thisEditor.conversion.for( 'upcast' ).elementToElement( {
            view: 'div',
            model: ( viewElement, { writer: modelWriter } ) => {
                if(debug){console.log("convertDiv", {
                    viewElement : viewElement
                })}
                return modelWriter.createElement( 'div', viewElement.getAttributes() );
            }
        } );

        // The model-to-view converter for the <div> element (attributes are converted separately).
        thisEditor.conversion.for( 'downcast' ).elementToElement( {
            model: 'div',
            view: 'div'
        } );

        // The model-to-view converter for <div> attributes.
        // Note that a lower-level, event-based API is used here.
        thisEditor.conversion.for( 'downcast' ).add( dispatcher => {
            if(debug){console.log("convertDiv.conversion.downcast")}
            dispatcher.on( 'attribute', ( evt, data, conversionApi ) => {
                if(debug){console.log("convertDiv.conversion.downcast.dispatcher:attribute", {
                    evt : evt, data : data, conversionApi : conversionApi
                })}
                // Convert <div> attributes only.
                if ( data.item.name !== 'div' ) {
                    return;
                }

                const viewWriter = conversionApi.writer;
                const view = conversionApi.mapper.toViewElement( data.item );
                if(debug){console.log("convertDiv.conversion.downcast.dispatcher:attribute", {
                    viewWriter : viewWriter, view : view
                })}

                // In the model-to-view conversion we convert changes.
                // An attribute can be added or removed or changed.
                // The below code handles all 3 cases.
                if ( data.attributeNewValue ) {
                    viewWriter.setAttribute( data.attributeKey, data.attributeNewValue, view );
                } else {
                    viewWriter.removeAttribute( data.attributeKey, view );
                }
            } );
        } );
    };
}