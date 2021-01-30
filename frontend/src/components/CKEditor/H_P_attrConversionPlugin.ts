export default function H_P_attrConversionPlugin(editor) {
    this.editor = editor;
    this.afterInit = function () {
        let batch = {
            paragraph: "p", heading1: "h1", heading2: "h2", heading3: "h3",
            heading4: "h4", heading5: "h5", heading6: "h6", label: "label"
        };
        Object.keys(batch).forEach(function (modelName) {
            let viewName = batch[modelName];
            _convertersApi._batch(editor, viewName, modelName);
        });
    };
}

let _convertersApi = {
    _batch: function (editor, viewName, modelName) {
        let debug = false;
        if (debug) {
            console.log("_convertersApi._batch()", arguments)
        }

        editor.model.schema.extend(modelName, {allowAttributes: modelName + 'Class'});


        // //TODO this is the original
        // // Tell the editor that the model "linkTarget" attribute converts into <a target="..."></a>
        editor.conversion.for('downcast').attributeToElement({
            model: modelName,
            view: (attributeValue, {writer}) => {
                const linkElement = writer.createAttributeElement(viewName, {
                    "class": attributeValue
                }, {priority: 0});
                if (debug) {
                    console.log({
                        attributeValue: attributeValue, writer: writer, linkElement: linkElement
                    })
                }
                writer.setCustomProperty(modelName, true, linkElement);

                return linkElement;
            },
            converterPriority: 'low'
        });

        //
        //TODO this is the original
        // Tell the editor that <a target="..."></a> converts into the "linkTarget" attribute in the model.
        editor.conversion.for('upcast').attributeToAttribute({
            view: "class",
            model: 'class',
            converterPriority: 'low'
        });
        editor.conversion.for('upcast').attributeToAttribute({
            view: 'id',
            model: 'id',
            converterPriority: 'low'
        });


        // Allow <div> elements in the model to have all attributes.
        editor.model.schema.addAttributeCheck(context => {
            if (context.endsWith(modelName)) {
                return true;
            }
        });


        // The model-to-view converter for the <div> element (attributes are converted separately).
        // editor.conversion.for( 'downcast' ).elementToElement( {
        //     model: 'classParagraph',
        //     view: 'paragraph'
        // } );

        // The model-to-view converter for <div> attributes.
        // Note that a lower-level, event-based API is used here.
        editor.conversion.for('downcast').add(dispatcher => {
            dispatcher.on('attribute', (evt, data, conversionApi) => {
                if (debug) {
                    console.log({
                        evt: evt, data: data, conversionApi: conversionApi
                    })
                }
                // Convert <div> attributes only.
                if (data.item.name !== modelName) {
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
    }
};