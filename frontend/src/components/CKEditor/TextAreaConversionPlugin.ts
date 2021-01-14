export default function TextAreaConversionPlugin(editor) {
    let debug = false;
    if (debug) {
        console.log("convertTextArea")
    }
    this.editor = editor;
    // Allow <div> elements in the model.
    this.init = function () {
        if (debug) {
            console.log("convertTextArea.init")
        }
        let thisEditor = this.editor;
        thisEditor.model.schema.register('textarea', {
            allowWhere: 'div',
            allowContentOf: 'div',
            isSelectable: false,
            allowAttributes: ['id', 'class', 'disabled', 'required', 'type', 'name', 'placeholder']
        });

    };

    this.afterInit = function () {
        if (debug) {
            console.log("convertTextArea.afterInit")
        }
        let thisEditor = this.editor;
        // Allow <div> elements in the model to have all attributes.
        thisEditor.model.schema.addAttributeCheck(context => {
            if (context.endsWith('textarea')) {
                return true;
            }
        });
        // The model-to-view converter for the <div> element (attributes are converted separately).
        thisEditor.conversion.for('downcast').elementToElement({
            model: 'textarea',
            view: (modelElement, conversionApi) => {
                const modelWriter = conversionApi.writer;
                let view = modelWriter.createAttributeElement('textarea', modelElement.getAttributes(), { priority: 5 });
                // modelWriter.setAttribute('disabled', 'true', view);

                if (debug) {
                    console.log("convertTextArea.downcast.elementToElement", {
                        modelElement: modelElement, view: view
                    })
                }
                return view;

                // return writer.createContainerElement( 'h' + modelElement.getAttribute( 'level' ) );
            }
        });
        // The view-to-model converter converting a view <div> with all its attributes to the model.
        thisEditor.conversion.for('upcast').elementToElement({
            view: {
                name: 'textarea'
            },
            model: (viewElement, { writer: modelWriter }) => {
                let view = modelWriter.createElement('textarea', viewElement.getAttributes());
                if (debug) {
                    console.log("convertTextArea.upcast.elementToElement", {
                        viewElement: viewElement, view: view
                    })
                }
                // modelWriter.setAttribute('disabled', 'true', view);
                return view;
            }

        });

        thisEditor.conversion.for('downcast').add(dispatcher => {
            dispatcher.on('element', (evt, data, conversionApi) => {
                // if(debug){console.log("downcast|element:input")}
                if (debug) {
                    console.log(evt.name)
                }
            });
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                // if(debug){console.log("downcast|attribute:disabled")}
                if (debug) {
                    console.log(evt.name)
                }
            });
            dispatcher.on('properties', (evt, data, conversionApi) => {
                // if(debug){console.log("downcast|attribute:disabled")}
                if (debug) {
                    console.log(evt.name)
                }
            });
        });

        thisEditor.conversion.for('upcast').add(dispatcher => {
            dispatcher.on('element:input', (evt, data, conversionApi) => {
                if (debug) {
                    console.log("upcast|element:input")
                }
            });
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                // if(debug){console.log("upcast|attribute:disabled")}
                if (debug) {
                    console.log(evt.name)
                }
            });
        });

    };
}