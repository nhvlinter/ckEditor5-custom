export default function UlConversionPlugin(editor) {
    let debug = false;
    if (debug) {
        console.log("convertUl")
    }

    this.editor = editor;
    if (debug) {
        console.log("convertUl.init")
    }
    let thisEditor = this.editor;
    // thisEditor.model.schema.register( 'ul', {
    //     inheritAllFrom: 'listItem',
    //     allowAttributes : ['class']
    // } );
    this.afterInit = function () {
        // this.init = function () {
        editor.model.schema.extend('listItem', { allowAttributes: 'class' });

        // editor.conversion.for( 'upcast' ).attributeToAttribute( {
        //     model: 'listItem',
        //     view: {
        //         name: 'li',
        //         key: 'class'
        //     },
        //     converterPriority: 'low'
        // } );


        // Allow <div> elements in the model to have all attributes.
        editor.model.schema.addAttributeCheck(context => {
            if (context.endsWith('listItem')) {
                if (debug) {
                    console.log("context.endsWith( 'listItem' )")
                }
                return true;
            }
        });

        editor.conversion.for('downcast').attributeToAttribute({
            model: 'customClass',
            view: 'class'
        });
        // //TODO this is the original
        // // Tell the editor that the model "linkTarget" attribute converts into <a target="..."></a>
        // editor.conversion.for( 'downcast' ).attributeToElement( {
        //     model: 'ul',
        //     view: ( attributeValue, { writer } ) => {
        //         const linkElement = writer.createAttributeElement( 'ul', { "class": attributeValue
        //         }, { priority: 0 } );
        //         if(debug){console.log({
        //             attributeValue : attributeValue, writer : writer, linkElement : linkElement
        //         })}
        //         writer.setCustomProperty( 'ul', true, linkElement );
        //
        //         return linkElement;
        //     },
        //     converterPriority: 'low'
        // } );
        //
        // //
        // //TODO this is the original
        // // Tell the editor that <a target="..."></a> converts into the "linkTarget" attribute in the model.
        // editor.conversion.for( 'upcast' ).attributeToAttribute( {
        //     view: {
        //         name : 'li'
        //     },
        //     model: 'ul',
        //     converterPriority: 'high'
        // } );
        // editor.conversion.for( 'upcast' ).attributeToAttribute( {
        //     view: 'listItem',
        //     model: 'li',
        //     converterPriority: 'high'
        // } );


        // The model-to-view converter for the <div> element (attributes are converted separately).
        // editor.conversion.for( 'downcast' ).elementToElement( {
        //     model: 'classParagraph',
        //     view: 'paragraph'
        // } );

        // The model-to-view converter for <div> attributes.
        // Note that a lower-level, event-based API is used here.
        editor.conversion.for('downcast').add(dispatcher => {
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log('downcast|' + evt.name, {
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }

            });
            dispatcher.on('element', (evt, data, conversionApi) => {
                if (debug) {
                    console.log('downcast|' + evt.name, {
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }

            });
        });
        editor.conversion.for('upcast').add(dispatcher => {
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log('upcast|' + evt.name, {
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }

            });
            dispatcher.on("element:li", (evt, data, conversionApi) => {
                // if(debug){console.log('upcast|'+evt.name)}
                const viewItem = data.viewItem;
                const writer = conversionApi.writer;

                if (viewItem.name !== 'li') {
                    return;
                }
                const modelRange = data.modelRange;

                const modelElement = modelRange && modelRange.start.nodeAfter;

                if (!modelElement) {
                    return;
                }

                // The upcast conversion picks up classes from the base element and from the <figure> element so it should be extensible.
                const currentAttributeValue = modelElement.getAttribute('class') || [];

                currentAttributeValue.push(...viewItem.getClassNames());

                writer.setAttribute('customClass', currentAttributeValue, modelElement);
                if (debug) {
                    console.log('upcast|' + evt.name, {
                        evt: evt, data: data, writer: writer, viewItem: viewItem, modelRange: modelRange,
                        modelElement: modelElement, currentAttributeValue: currentAttributeValue
                    })
                }

            });
        });

    }
}