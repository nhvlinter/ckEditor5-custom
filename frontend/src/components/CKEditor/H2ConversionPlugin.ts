export default function H2ConversionPlugin(editor) {
    let debug = false;
    if (debug) { console.log("convertH2") }

    this.editor = editor;
    this.init = function () {
        if (debug) { console.log("convertH2.init") }
        let thisEditor = this.editor;
        thisEditor.model.schema.register('h2', {
            allowWhere: '$text',
            allowContentOf: '$block',
            allowAttributes: ['id', 'class']
        });

    };

    this.afterInit = function () {
        if (debug) { console.log("convertH2.afterInit") }
        let thisEditor = this.editor;

        thisEditor.model.schema.addAttributeCheck(context => {
            if (debug) {
                console.log("convertH2.addAttributeCheck", {
                    context: context
                })
            }
            if (context.endsWith('h2')) {
                if (debug) { console.log("convertH2.addAttributeCheck.endsWith( 'div' )") }
                return true;
            }
        });
        // The view-to-model converter converting a view <h2> with all its attributes to the model.
        thisEditor.conversion.for('upcast').elementToElement({
            view: 'h2',
            model: (viewElement, { writer: modelWriter }) => {
                if (debug) {
                    console.log("convertH2", {
                        viewElement: viewElement
                    })
                }
                return modelWriter.createElement('h2', viewElement.getAttributes());
            }
        });

        // The model-to-view converter for the <h2> element (attributes are converted separately).
        thisEditor.conversion.for('downcast').elementToElement({
            model: 'h2',
            view: 'h2'
        });

        // The model-to-view converter for <h2> attributes.
        // Note that a lower-level, event-based API is used here.
        thisEditor.conversion.for('downcast').add(dispatcher => {
            if (debug) { console.log("convertH2.conversion.downcast") }
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log("convertH2.conversion.downcast.dispatcher:attribute", {
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }
                // Convert <h2> attributes only.
                if (data.item.name !== 'h2') {
                    return;
                }

                const viewWriter = conversionApi.writer;
                const view = conversionApi.mapper.toViewElement(data.item);
                if (debug) {
                    console.log("convertH2.conversion.downcast.dispatcher:attribute", {
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