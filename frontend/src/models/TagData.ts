import { observable, action, toJS, computed } from "mobx";
import { aFetch, fetchFormData } from "../services/api/fetch";

export class TagData {
    @observable id: string = "";
    @observable text: string = "";
    @observable name: string = "";
    @observable children: TagData[] = [];
    @observable props: Object = {};
    @observable level: string = "";
    @observable attributes: string = "";
    @observable parent: TagData | null = null;

    constructor(data?: any) {
        if (data != null) {
            const {children,parent, ...pData } = data;
            Object.assign(this, pData);
            if(children) {
                this.children = children.map((x:any) => new TagData(x));
            }
            if(this.children == null ) {
                this.children = [];
            }
            if(parent) {
                this.parent = new TagData(parent);
            }
            if(this.parent == null) {
                this.parent = new TagData();
            }
        }
    }

    @action set_id = (v: string) => { this.id = v }
    @action set_level = (v: string) => { this.level = v }
    @action set_attributes = (v: string) => { this.attributes = v }
    @action set_text = (v: string) => { this.text = v }
    @action set_name = (v: string) => { this.name = v }
    @action set_children = (v: TagData[] | null) => {
        if(!v) {
            this.children = [];
        } else {
            this.children = v.map(x => x);
        }
    }
    @action set_parent = (v: TagData | null) => { 
        if(!v) {
            this.parent = new TagData();
        } else {
            this.parent = new TagData(v);
        }
    }
    @action set_props = (v: Object) => { Object.assign(this.props, v) }
}