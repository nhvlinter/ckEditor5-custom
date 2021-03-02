import { observable, action, toJS, computed } from "mobx";
import { aFetch, fetchFormData } from "../services/api/fetch";

export class TagData {
    @observable id: string = "";
    @observable content: string = "";
    @observable name: string = "";
    @observable children: TagData[] = [];
    @observable props: Object = {};

    constructor(data?: any) {
        if (data != null) {
            const {children, ...pData } = data;
            Object.assign(this, pData);
            if(children) {
                this.children = children.map((x:any) => new TagData(x));
            }
            if(this.children == null ) {
                this.children = [];
            }
        }
    }

    @action set_id = (v: string) => { this.id = v }
    @action set_content = (v: string) => { this.content = v }
    @action set_name = (v: string) => { this.name = v }
    @action set_children = (v: TagData[] | null) => {
        if(!v) {
            this.children = [];
        } else {
            this.children = v.map(x => x);
        }
    }
    @action set_props = (v: Object) => { this.props = v }
}