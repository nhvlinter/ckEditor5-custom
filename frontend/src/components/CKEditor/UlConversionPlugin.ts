
export default function UlConversionPlugin(editor) {

    let debug = false;
    if (debug) {
        console.log("convertForm")
    }

    this.editor = editor;
    this.init = function () {
        if (debug) {
            console.log("convertForm.init")
        }
        let thisEditor = this.editor;
        thisEditor.model.schema.register('ul', {
            allowWhere: '$block',
            allowContentOf: '$root',
            allowIn: ['style'],
            allowAttributes: ['id', 'class', 'style']
        });

    };

    this.afterInit = function () {
        if (debug) {
            console.log("convertForm.afterInit")
        }
        let thisEditor = this.editor;

        thisEditor.model.schema.addAttributeCheck(context => {
            if (debug) {
                console.log("convertForm.addAttributeCheck", {
                    context: context
                })
            }
            if (context.endsWith('ul')) {
                if (debug) {
                    console.log("convertForm.addAttributeCheck.endsWith( 'form' )")
                }
                return true;
            }
        });
        // The view-to-model converter converting a view <div> with all its attributes to the model.
        thisEditor.conversion.for('upcast').elementToElement({
            view: 'ul',
            model: (viewElement, { writer: modelWriter }) => {
                if (debug) {
                    console.log("convertForm", {
                        viewElement: viewElement
                    })
                }
                return modelWriter.createElement('ul', viewElement.getAttributes());
            }
        });

        // The model-to-view converter for the <div> element (attributes are converted separately).
        thisEditor.conversion.for('downcast').elementToElement({
            model: 'ul',
            view: 'ul'
        });

        // The model-to-view converter for <div> attributes.
        // Note that a lower-level, event-based API is used here.
        thisEditor.conversion.for('downcast').add(dispatcher => {
            if (debug) {
                console.log("convertForm.conversion.downcast")
            }
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log("convertForm.conversion.downcast.dispatcher:attribute", {
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }
                // Convert <div> attributes only.
                if (data.item.name !== 'ul') {
                    return;
                }

                const viewWriter = conversionApi.writer;
                const view = conversionApi.mapper.toViewElement(data.item);
                if (debug) {
                    console.log("convertForm.conversion.downcast.dispatcher:attribute", {
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