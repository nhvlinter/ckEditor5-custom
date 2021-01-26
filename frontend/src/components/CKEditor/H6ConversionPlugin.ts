export default function H6ConversionPlugin(editor) {
    let debug = false;
    if (debug) { console.log("convertH6") }

    this.editor = editor;
    this.init = function () {
        if (debug) { console.log("convertH6.init") }
        let thisEditor = this.editor;
        thisEditor.model.schema.register('h6', {
            allowWhere: '$text',
            allowContentOf: '$block',
            allowAttributes: ['id', 'class']
        });

    };

    this.afterInit = function () {
        if (debug) { console.log("convertH6.afterInit") }
        let thisEditor = this.editor;

        thisEditor.model.schema.addAttributeCheck(context => {
            if (debug) {
                console.log("convertH6.addAttributeCheck", {
                    context: context
                })
            }
            if (context.endsWith('h6')) {
                if (debug) { console.log("convertH6.addAttributeCheck.endsWith( 'div' )") }
                return true;
            }
        });
        // The view-to-model converter converting a view <h6> with all its attributes to the model.
        thisEditor.conversion.for('upcast').elementToElement({
            view: 'h6',
            model: (viewElement, { writer: modelWriter }) => {
                if (debug) {
                    console.log("convertH6", {
                        viewElement: viewElement
                    })
                }
                return modelWriter.createElement('h6', viewElement.getAttributes());
            }
        });

        // The model-to-view converter for the <h6> element (attributes are converted separately).
        thisEditor.conversion.for('downcast').elementToElement({
            model: 'h6',
            view: 'h6'
        });

        // The model-to-view converter for <h6> attributes.
        // Note that a lower-level, event-based API is used here.
        thisEditor.conversion.for('downcast').add(dispatcher => {
            if (debug) { console.log("convertH6.conversion.downcast") }
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log("convertH6.conversion.downcast.dispatcher:attribute", {
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }
                // Convert <h6> attributes only.
                if (data.item.name !== 'h6') {
                    return;
                }

                const viewWriter = conversionApi.writer;
                const view = conversionApi.mapper.toViewElement(data.item);
                if (debug) {
                    console.log("convertH6.conversion.downcast.dispatcher:attribute", {
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