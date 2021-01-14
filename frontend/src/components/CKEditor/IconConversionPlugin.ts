export default function IconConversionPlugin(editor) {
    let debug = false;
    if (debug) { console.log("convertIcon") }

    this.editor = editor;
    this.init = function () {
        if (debug) { console.log("convertIcon.init") }
        let thisEditor = this.editor;
        thisEditor.model.schema.register('i', {
            allowWhere: '$text',
            allowContentOf: '$block'
        });
    };

    this.afterInit = function () {
        if (debug) { console.log("convertIcon.afterInit") }
        let thisEditor = this.editor;

        thisEditor.model.schema.addAttributeCheck(context => {
            if (debug) {
                console.log("convertIcon.addAttributeCheck", {
                    context: context
                })
            }
            if (context.endsWith('i')) {
                if (debug) { console.log("convertIcon.addAttributeCheck.endsWith( 'i' )") }
                return true;
            }
        });
        // The view-to-model converter converting a view <i> with all its attributes to the model.
        thisEditor.conversion.for('upcast').elementToElement({
            view: 'i',
            model: (viewElement, { writer: modelWriter }) => {
                if (debug) {
                    console.log("convertIcon", {
                        viewElement: viewElement
                    })
                }
                return modelWriter.createElement('i', viewElement.getAttributes());
            }
        });

        // The model-to-view converter for the <i> element (attributes are converted separately).
        thisEditor.conversion.for('downcast').elementToElement({
            model: 'i',
            view: 'i',
        });

        // The model-to-view converter for <a> attributes.
        // Note that a lower-level, event-based API is used here.
        thisEditor.conversion.for('downcast').add(dispatcher => {
            if (debug) { console.log("convertIcon.conversion.downcast") }
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log("convertAnchor.conversion.downcast.dispatcher:attribute", {
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }
                // Convert <a> attributes only.
                if (data.item.name !== 'i') {
                    return;
                }

                const viewWriter = conversionApi.writer;
                const view = conversionApi.mapper.toViewElement(data.item);
                if (debug) {
                    console.log("convertAnchor.conversion.downcast.dispatcher:attribute", {
                        viewWriter: viewWriter, view: view
                    })
                }

                // In the model-to-view conversion we convert changes.
                // An attribute can be added or removed or changed.
                // The below code handles all 3 cases.

                    viewWriter.setAttribute(data.attributeKey, data.attributeNewValue, view);

            });
        });
    };
}