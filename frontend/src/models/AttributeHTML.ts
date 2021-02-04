import { observable, action } from "mobx";

export class AttributeHTML {
    @observable key: string = "";
    @observable value: string = "";

    constructor(data?: any) {
        if (data != null) {
            Object.assign(this, data);
        }
    }

    @action get_key = () => {return this.key;}
    @action get_value = () => {return this.value;}
    @action set_key = (v: string) => {this.key = v;}
    @action set_value = (v: string) => {this.value = v;}

}