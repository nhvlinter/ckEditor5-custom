export default function BtnConversionPlugin(editor) {
    let debug = false;
    if (debug) {
        console.log("convertBtn")
    }

    this.editor = editor;

    this.init = function () {
        if (debug) {
            console.log("convertBtn.init")
        }
        let thisEditor = this.editor;
        // thisEditor.model.schema.register('button', {
        //     allowWhere: '$text', isInline: true,
        //     allowContentOf: '$block', allowAttributes: ['class', 'id']
        // });
    };
    this.afterInit = function () {
        if (debug) {
            console.log("convertBtn.afterInit")
        }
        let thisEditor = this.editor;
        thisEditor.model.schema.addAttributeCheck(context => {
            if (debug) {
                console.log("convertBtn.addAttributeCheck", { context: context })
            }
            if (context.endsWith('button')) {
                return true;
            }
        });
        // The view-to-model converter converting a view <div> with all its attributes to the model.
        thisEditor.conversion.for('upcast').elementToElement({
            view: 'button',
            model: (viewElement, { writer: modelWriter }) => {
                if (debug) {
                    console.log("convertBtn.upcast.elementToElement", { viewElement: viewElement })
                }
                return modelWriter.createElement('button', viewElement.getAttributes());
            },
            converterPriority: 'high'
        });

        // The model-to-view converter for the <div> element (attributes are converted separately).
        thisEditor.conversion.for('downcast').elementToElement({
            model: 'button',
            view: 'button',
            converterPriority: 'high'
        });

        // The model-to-view converter for <div> attributes.
        // Note that a lower-level, event-based API is used here.
        thisEditor.conversion.for('downcast').add(dispatcher => {
            if (debug) {
                console.log("convertBtn.downcast.addDispatcher", { dispatcher: dispatcher })
            }
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log({
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }
                // Convert <div> attributes only.
                if (data.item.name !== 'button') {
                    return;
                }

                const viewWriter = conversionApi.writer;
                const viewDiv = conversionApi.mapper.toViewElement(data.item);

                // In the model-to-view conversion we convert changes.
                // An attribute can be added or removed or changed.
                // The below code handles all 3 cases.
                if (data.attributeNewValue) {
                    viewWriter.setAttribute(data.attributeKey, data.attributeNewValue, viewDiv);
                } else {
                    viewWriter.removeAttribute(data.attributeKey, viewDiv);
                }
            });
        });
    };
}