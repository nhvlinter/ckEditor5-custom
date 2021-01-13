export default function SpanConversionPlugin(editor) {
    let debug = false;
    if (debug) {
        console.log("convertSpan")
    }

    this.editor = editor;

    this.init = function () {
        if (debug) {
            console.log("convertSpan.init")
        }

        let thisEditor = this.editor;
        thisEditor.model.schema.register('span', {
            inheritAllFrom: 'paragraph',
            allowAttributes: ['class']
        });
    };
    this.afterInit = function () {
        if (debug) {
            console.log("convertSpan.afterInit")
        }

        let thisEditor = this.editor;
        // Tell the editor that <a target="..."></a> converts into the "linkTarget" attribute in the model.
        // editor.conversion.for( 'upcast' ).attributeToAttribute( {
        //     view: 'span',
        //     model: 'span',
        //     converterPriority: 'high'
        // } );

        thisEditor.conversion.for('upcast').elementToElement({
            view: 'span',
            model: (viewElement, { writer: modelWriter }) => {
                let attr = viewElement.getAttributes();
                attr['contenteditable'] = 'false';
                if (debug) {
                    console.log("convertSpan.conversion.forUpcast.elementToElement", {
                        viewElement: viewElement, attr: attr
                    })
                }
                return modelWriter.createElement('span', viewElement.getAttributes());
            },
            converterPriority: 'low'
        });


        thisEditor.conversion.for('downcast').elementToElement({
            model: 'span',
            view: (modelElement, conversionApi) => {
                const modelWriter = conversionApi.writer;
                let view = modelWriter.createAttributeElement('span', modelElement.getAttributes(), { priority: 0 });

                if (debug) {
                    console.log("convertInput.downcast.elementToElement", {
                        modelElement: modelElement, view: view
                    })
                }
                return view;

                // return writer.createContainerElement( 'h' + modelElement.getAttribute( 'level' ) );
            },
            converterPriority: 'low'
        });

        // thisEditor.conversion.for( 'downcast' ).add( dispatcher => {
        //     dispatcher.on( 'attribute', ( evt, data, conversionApi ) => {
        //         if ( data.item.name !== 'span' ) {
        //             return;
        //         }
        //         const viewWriter = conversionApi.writer;
        //         const viewDiv = conversionApi.mapper.toViewElement( data.item );
        //
        //         // In the model-to-view conversion we convert changes.
        //         // An attribute can be added or removed or changed.
        //         // The below code handles all 3 cases.
        //         if ( data.attributeNewValue ) {
        //             viewWriter.setAttribute( data.attributeKey, data.attributeNewValue, viewDiv );
        //         } else {
        //             viewWriter.removeAttribute( data.attributeKey, viewDiv );
        //         }
        //     } );
        // } );


    };
}