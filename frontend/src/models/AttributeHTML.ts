import { observable, action } from "mobx";

export class AttributeHTML {
    @observable key: string = "";
    @observable value: string = "";

    constructor(data?: any) {
        if (data != null) {
            const {child, parent, ...pData } = data;
            Object.assign(this, data);
        }
    }

    @action set_key = (v: string) => {this.key = v;}
    @action set_value = (v: string) => {this.value = v;}
}