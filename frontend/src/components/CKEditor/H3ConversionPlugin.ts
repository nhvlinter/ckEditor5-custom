export default function H3ConversionPlugin(editor) {
    let debug = false;
    if (debug) { console.log("convertH3") }

    this.editor = editor;
    this.init = function () {
        if (debug) { console.log("convertH3.init") }
        let thisEditor = this.editor;
        thisEditor.model.schema.register('h3', {
            allowWhere: '$text',
            allowContentOf: '$block',
            allowAttributes: ['id', 'class']
        });

    };

    this.afterInit = function () {
        if (debug) { console.log("convertH3.afterInit") }
        let thisEditor = this.editor;

        thisEditor.model.schema.addAttributeCheck(context => {
            if (debug) {
                console.log("convertH3.addAttributeCheck", {
                    context: context
                })
            }
            if (context.endsWith('h3')) {
                if (debug) { console.log("convertH3.addAttributeCheck.endsWith( 'div' )") }
                return true;
            }
        });
        // The view-to-model converter converting a view <h3> with all its attributes to the model.
        thisEditor.conversion.for('upcast').elementToElement({
            view: 'h3',
            model: (viewElement, { writer: modelWriter }) => {
                if (debug) {
                    console.log("convertH3", {
                        viewElement: viewElement
                    })
                }
                return modelWriter.createElement('h3', viewElement.getAttributes());
            }
        });

        // The model-to-view converter for the <h3> element (attributes are converted separately).
        thisEditor.conversion.for('downcast').elementToElement({
            model: 'h3',
            view: 'h3'
        });

        // The model-to-view converter for <h3> attributes.
        // Note that a lower-level, event-based API is used here.
        thisEditor.conversion.for('downcast').add(dispatcher => {
            if (debug) { console.log("convertH3.conversion.downcast") }
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log("convertH3.conversion.downcast.dispatcher:attribute", {
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }
                // Convert <h3> attributes only.
                if (data.item.name !== 'h3') {
                    return;
                }

                const viewWriter = conversionApi.writer;
                const view = conversionApi.mapper.toViewElement(data.item);
                if (debug) {
                    console.log("convertH3.conversion.downcast.dispatcher:attribute", {
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
        });
    };
}