export default function SpanConversionPlugin(editor) {
    let debug = false;
    if (debug) { console.log("convertSpan") }

    this.editor = editor;
    this.init = function () {
        if (debug) { console.log("convertSpan.init") }
        let thisEditor = this.editor;
        thisEditor.model.schema.register('span', {
            allowWhere: '$text',
            allowContentOf: '$block'
        });

    };

    this.afterInit = function () {
        if (debug) { console.log("convertSpan.afterInit") }
        let thisEditor = this.editor;

        thisEditor.model.schema.addAttributeCheck(context => {
            if (debug) {
                console.log("convertSpan.addAttributeCheck", {
                    context: context
                })
            }
            if (context.endsWith('span')) {
                if (debug) { console.log("convertSpan.addAttributeCheck.endsWith( 'span' )") }
                return true;
            }
        });
        // The view-to-model converter converting a view <div> with all its attributes to the model.
        thisEditor.conversion.for('upcast').elementToElement({
            view: 'span',
            model: (viewElement, { writer: modelWriter }) => {
                if (debug) {
                    console.log("convertSpan", {
                        viewElement: viewElement
                    })
                }
                return modelWriter.createElement('span', viewElement.getAttributes());
            }
        });

        // The model-to-view converter for the <div> element (attributes are converted separately).
        thisEditor.conversion.for('downcast').elementToElement({
            model: 'span',
            view: 'span'
        });

        // The model-to-view converter for <div> attributes.
        // Note that a lower-level, event-based API is used here.
        thisEditor.conversion.for('downcast').add(dispatcher => {
            if (debug) { console.log("convertSpan.conversion.downcast") }
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log("convertSpan.conversion.downcast.dispatcher:attribute", {
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }
                // Convert <a> attributes only.
                if (data.item.name !== 'span') {
                    return;
                }

                const viewWriter = conversionApi.writer;
                const view = conversionApi.mapper.toViewElement(data.item);
                if (debug) {
                    console.log("convertSpan.conversion.downcast.dispatcher:attribute", {
                        viewWriter: viewWriter, view: view
                    })
                }

                // In the model-to-view conversion we convert changes.
                // An attribute can be added or removed or changed.
                // The below code handles all 3 cases.
                if (data.attributeNewValue) {
                    viewWriter.setAttribute(data.attributeKey, data.attributeNewValue, view);
                } else {
                    viewWriter.removeAttribute(data.attributeKey, view);
                }
            });
        } );
    };
}