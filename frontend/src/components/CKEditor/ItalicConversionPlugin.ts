export default function ItalicConversionPlugin(editor) {
    let debug = false;
    if (debug) {
        console.log("convertItalic")
    }
    this.editor = editor;
    this.init = function () {
        if (debug) {
            console.log("convertItalic.init")
        }
    };
    this.afterInit = function () {
        if (debug) {
            console.log("convertItalic.afterInit")
        }
        let thisEditor = this.editor;
        thisEditor.conversion.for('downcast').attributeToElement({
            model: 'italic',
            view: (attributeValue, { writer }) => {
                if (debug) {
                    console.log("convertItalic", {
                        attributeValue: attributeValue, writer: writer
                    })
                }
                return writer.createAttributeElement('em', {}, { priority: 5 });
            },
            converterPriority: 'high'
        });
        thisEditor.conversion.for('upcast').attributeToAttribute({
            view: {
                name: 'em'
            },
            model: 'italic',
            converterPriority: 'high'
        });
    };

}