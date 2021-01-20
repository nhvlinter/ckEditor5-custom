import { observable, action, toJS, computed } from "mobx";
import { aFetch } from "../services/api/fetch";

export class CKEditor {
    @observable id: string = "";
    @observable content: string = "";

    constructor(data?: any) {
        if (data != null) {
            Object.assign(this, data);
        }
    }

    @action set_id = (v: string) => { this.id = v }
    @action set_content = (v: string) => { this.content = v }

    toJS() {
        return ({
            id: this.id,
            content: this.content,
        })
    }

    static async get() {
        const [err, data] = await aFetch<{}>("GET", `/api/get.php`);
        return [err, err ? null : data];
    }

    static async save(e: CKEditor) {
        const body = e.toJS();
        const [err, x] = await aFetch<{}>("POST", `/api/index.php`, body);
        return [err, (err ? undefined : new CKEditor(x))!] as const;
    }

}