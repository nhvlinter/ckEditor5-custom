import { observable, action, toJS, computed } from "mobx";
import { aFetch, fetchFormData } from "../services/api/fetch";

export class CKEditor {
    @observable id: string = "";
    @observable content: string = "";

    constructor(data?: any) {
        if (data != null) {
            Object.assign(this, data);
        }
    }

    @computed get getId() {
        return this.id;
    }

    @computed get getContent() {
        return this.content;
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
        let formData = new FormData();
        formData.append("id", e.getId);
        formData.append("content", e.getContent);
        console.log(e.getContent);
        await fetchFormData("POST",`/api/index.php`, formData);
    }

}