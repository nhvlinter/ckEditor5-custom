export default function TextAreaConversionPlugin(editor) {

    let debug = false;
    if (debug) { console.log("convertTextarea") }

    this.editor = editor;
    this.init = function () {
        if (debug) { console.log("convertTextarea.init") }
        let thisEditor = this.editor;
        thisEditor.model.schema.register('textarea', {
            allowWhere: 'div',
            allowContentOf: 'div',
            isSelectable: false,
            allowAttributes: ['id', 'class', 'disabled', 'required', 'type', 'name', 'placeholder']
        });

    };

    this.afterInit = function () {
        if (debug) { console.log("convertTextarea.afterInit") }
        let thisEditor = this.editor;

        thisEditor.model.schema.addAttributeCheck(context => {
            if (debug) {
                console.log("convertTextarea.addAttributeCheck", {
                    context: context
                })
            }
            if (context.endsWith('textarea')) {
                if (debug) { console.log("convertTextarea.addAttributeCheck.endsWith( 'textarea' )") }
                return true;
            }
        });
        // The view-to-model converter converting a view <textarea> with all its attributes to the model.
        thisEditor.conversion.for('upcast').elementToElement({
            view: 'textarea',
            model: (viewElement, { writer: modelWriter }) => {
                if (debug) {
                    console.log("convertTextarea", {
                        viewElement: viewElement
                    })
                }
                return modelWriter.createElement('textarea', viewElement.getAttributes());
            }
        });

        // The model-to-view converter for the <textarea> element (attributes are converted separately).
        thisEditor.conversion.for('downcast').elementToElement({
            model: 'textarea',
            view: 'textarea'
        });

        // The model-to-view converter for <textarea> attributes.
        // Note that a lower-level, event-based API is used here.
        thisEditor.conversion.for('downcast').add(dispatcher => {
            if (debug) { console.log("convertTextarea.conversion.downcast") }
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log("convertTextarea.conversion.downcast.dispatcher:attribute", {
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }
                // Convert <textarea> attributes only.
                if (data.item.name !== 'textarea') {
                    return;
                }

                const viewWriter = conversionApi.writer;
                const view = conversionApi.mapper.toViewElement(data.item);
                if (debug) {
                    console.log("convertTextarea.conversion.downcast.dispatcher:attribute", {
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