import { observable, action, computed } from "mobx";
import { BaseStore } from "./BaseStore";
import { AttributeHTML } from "../models/AttributeHTML";
import { CKEditor } from "../models/CKEditor";

export class OverviewStore {

    @observable classes: string[] = [];
    @observable dataClass: string = "";
    @observable attributes: AttributeHTML[] = [];
    @observable attribute: AttributeHTML = new AttributeHTML();
    @observable keyAttr: string = "";
    @observable valueAttr: string = "";

    constructor(private store: BaseStore) {
        this.classes = [];
        this.dataClass = "";
        this.attributes = [];
        this.attribute = new AttributeHTML();
        this.keyAttr = "";
        this.valueAttr = "";
    }

    @action set_dataClass = (v: string) => { this.dataClass = v; }
    @action set_keyAttr = (v: string) => { this.keyAttr = v; }
    @action set_valueAttr = (v: string) => { this.valueAttr = v; }

    @action async init() {
        this.classes = [];
        this.dataClass = "";
        this.attributes = [];
        this.attribute = new AttributeHTML();
        this.keyAttr = "";
        this.valueAttr = "";
        let attr: AttributeHTML = new AttributeHTML();
        attr.set_key("id");
        attr.set_value("");
        let index = this.attributes.findIndex(x => x.get_key() == attr.key);
        if (index >= 0) {
            this.attributes[index].set_value(attr.value);
        } else {
            this.attributes.push(attr);
        }
    }

    @action async getClassesFromNode(node) {
        if (node != null && node.props != null && node.props.className != null) {            
            let arrayTemp = node.props.className.trim().split(" ");
            this.classes = arrayTemp;
        }
    }

    @action async deleteClassInNode(data) {
        if (this.classes.length > 0) {
            this.classes = this.classes.filter(x => x != data);
        }
    }

    @action async addClassInNode() {
        if (this.dataClass != "") {
            this.classes.push(this.dataClass);
        }
    }

    @action async addAttribute() {
        let index = this.attributes.findIndex(x => x.get_key() == this.attribute.key);
        if (index >= 0) {
            this.attributes[index].set_value(this.attribute.value);
        } else {
            this.attributes.push(this.attribute);
        }
    }

    @action removeAttribute(keyValue) {
        let index = this.attributes.findIndex(x => x.get_key() == keyValue);
        if (index >= 0) {
            this.attributes.splice(index, 1);
        }
    }

    @action updateAttribute(key) {
        let index = this.attributes.findIndex(x => x.get_key() == key);
        if (index >= 0) {
            this.attributes[index].set_value(this.attribute.value);
        }
    }

    @action updateAttrFromData(node) {
        let attrs = node.props;
        let attrTemp = Object.entries(attrs);
        for (let i = 0; i < attrTemp.length; i++) {
            let key = attrTemp[i][0];
            if (key != 'className' && key != 'reactid' && key != 'data-reactroot' && key != 'children') {
                let index = this.attributes.findIndex(x => x.get_key() == key);
                if (index >= 0) {
                    this.attributes[index].set_value(attrTemp[i][1]);
                } else {
                    let attr: AttributeHTML = new AttributeHTML();
                    attr.set_key(key);
                    attr.set_value(attrTemp[i][1]);
                    this.attributes.push(attr);
                }
            }
        }
    }

    @action async updatedAttr2Server(node) {
        const [err, dataGet] = await CKEditor.get();
        if (!err && dataGet) {

        }
    }
}