export default function AnchorConversionPlugin(editor) {
    let debug = false;
    if (debug) { console.log("convertAnchor") }

    this.editor = editor;
    this.init = function () {
        if (debug) { console.log("convertAnchor.init") }
        let thisEditor = this.editor;
        thisEditor.model.schema.register('a', {
            allowWhere: '$text',
            allowContentOf: '$block'
        });
    };

    this.afterInit = function () {
        if (debug) { console.log("convertAnchor.afterInit") }
        let thisEditor = this.editor;

        thisEditor.model.schema.addAttributeCheck(context => {
            if (debug) {
                console.log("convertAnchor.addAttributeCheck", {
                    context: context
                })
            }
            if (context.endsWith('a')) {
                if (debug) { console.log("convertAnchor.addAttributeCheck.endsWith( 'a' )") }
                return true;
            }
        });
        // The view-to-model converter converting a view <a> with all its attributes to the model.
        thisEditor.conversion.for('upcast').elementToElement({
            view: 'a',
            model: (viewElement, { writer: modelWriter }) => {
                if (debug) {
                    console.log("convertAnchor", {
                        viewElement: viewElement
                    })
                }
                return modelWriter.createElement('a', viewElement.getAttributes());
            }
        });

        // The model-to-view converter for the <a> element (attributes are converted separately).
        thisEditor.conversion.for('downcast').elementToElement({
            model: 'a',
            view: 'a',
        });

        // The model-to-view converter for <a> attributes.
        // Note that a lower-level, event-based API is used here.
        thisEditor.conversion.for('downcast').add(dispatcher => {
            if (debug) { console.log("convertAnchor.conversion.downcast") }
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log("convertAnchor.conversion.downcast.dispatcher:attribute", {
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }
                // Convert <a> attributes only.
                if (data.item.name !== 'a') {
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

                if (data.attributeNewValue && data.attributeKey != "href") {
                    viewWriter.setAttribute(data.attributeKey, data.attributeNewValue, view);
                } else {
                    viewWriter.removeAttribute(data.attributeKey, view);
                }
            });
        });
    };
}