export default function LabelConversionPlugin(editor) {
    let debug = false;
    if (debug) { console.log("convertLabel") }

    this.editor = editor;
    this.init = function () {
        if (debug) { console.log("convertLabel.init") }
        let thisEditor = this.editor;
        thisEditor.model.schema.register('label', {
            allowWhere: '$text',
            allowContentOf: '$block'
        });

    };

    this.afterInit = function () {
        if (debug) { console.log("convertLabel.afterInit") }
        let thisEditor = this.editor;

        thisEditor.model.schema.addAttributeCheck(context => {
            if (debug) {
                console.log("convertLabel.addAttributeCheck", {
                    context: context
                })
            }
            if (context.endsWith('label')) {
                if (debug) { console.log("convertLabel.addAttributeCheck.endsWith( 'div' )") }
                return true;
            }
        });
        // The view-to-model converter converting a view <label> with all its attributes to the model.
        thisEditor.conversion.for('upcast').elementToElement({
            view: 'label',
            model: (viewElement, { writer: modelWriter }) => {
                if (debug) {
                    console.log("convertLabel", {
                        viewElement: viewElement
                    })
                }
                return modelWriter.createElement('label', viewElement.getAttributes());
            }
        });

        // The model-to-view converter for the <label> element (attributes are converted separately).
        thisEditor.conversion.for('downcast').elementToElement({
            model: 'label',
            view: 'label'
        });

        // The model-to-view converter for <label> attributes.
        // Note that a lower-level, event-based API is used here.
        thisEditor.conversion.for('downcast').add(dispatcher => {
            if (debug) { console.log("convertLabel.conversion.downcast") }
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log("convertLabel.conversion.downcast.dispatcher:attribute", {
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }
                // Convert <label> attributes only.
                if (data.item.name !== 'label') {
                    return;
                }

                const viewWriter = conversionApi.writer;
                const view = conversionApi.mapper.toViewElement(data.item);
                if (debug) {
                    console.log("convertLabel.conversion.downcast.dispatcher:attribute", {
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