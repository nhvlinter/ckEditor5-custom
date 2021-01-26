export default function H1ConversionPlugin(editor) {
    let debug = false;
    if (debug) { console.log("convertH1") }

    this.editor = editor;
    this.init = function () {
        if (debug) { console.log("convertH1.init") }
        let thisEditor = this.editor;
        thisEditor.model.schema.register('h1', {
            allowWhere: '$text',
            allowContentOf: '$block',
            allowAttributes: ['id', 'class']
        });

    };

    this.afterInit = function () {
        if (debug) { console.log("convertH1.afterInit") }
        let thisEditor = this.editor;

        thisEditor.model.schema.addAttributeCheck(context => {
            if (debug) {
                console.log("convertH1.addAttributeCheck", {
                    context: context
                })
            }
            if (context.endsWith('h1')) {
                if (debug) { console.log("convertH1.addAttributeCheck.endsWith( 'div' )") }
                return true;
            }
        });
        // The view-to-model converter converting a view <h1> with all its attributes to the model.
        thisEditor.conversion.for('upcast').elementToElement({
            view: 'h1',
            model: (viewElement, { writer: modelWriter }) => {
                if (debug) {
                    console.log("convertH1", {
                        viewElement: viewElement
                    })
                }
                return modelWriter.createElement('h1', viewElement.getAttributes());
            }
        });

        // The model-to-view converter for the <h1> element (attributes are converted separately).
        thisEditor.conversion.for('downcast').elementToElement({
            model: 'h1',
            view: 'h1'
        });

        // The model-to-view converter for <h1> attributes.
        // Note that a lower-level, event-based API is used here.
        thisEditor.conversion.for('downcast').add(dispatcher => {
            if (debug) { console.log("convertH1.conversion.downcast") }
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log("convertH1.conversion.downcast.dispatcher:attribute", {
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }
                // Convert <h1> attributes only.
                if (data.item.name !== 'h1') {
                    return;
                }

                const viewWriter = conversionApi.writer;
                const view = conversionApi.mapper.toViewElement(data.item);
                if (debug) {
                    console.log("convertH1.conversion.downcast.dispatcher:attribute", {
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