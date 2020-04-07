import axios, { AxiosResponse, AxiosProxyConfig } from 'axios';
import qs from 'qs';

type Data = object | string;
type PostType = "GET" | "ALTER" | "TOGGLE" | "SUBMIT";

class Server {
    public static axios = axios;

    /**
     * 
     * @param {string} type 指定要进行的行为类型。例如修改为 `change`、提交为 `commit`、获取为 `get`。
     * @param {string} action 
     * @param {object | string} data 
     * @param {function} callback 
     */
    public static post(type: PostType, action: string, data: Data, callback: Function): void {
        axios.post("/backend/data-transfer.php", qs.stringify({
            type,
            action,
            data
        }), {
            method: "POST"
        }).then(result => {
            callback(result);
        }).catch(reason => {
            console.error(reason);
        })
    }

    public static getcfg(cfg: string | Array<string>, callback: Function) {
        axios.post("/backend/data-transfer.php", qs.stringify({
            type: "GET",
            action: "config",
            data: cfg
        }), {
            method: "POST"
        }).then(result => {
            callback(result.data);
        }).catch(reason => {
            console.error(reason);
        })
    }
}

export default Server;