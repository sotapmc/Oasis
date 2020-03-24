import axios, { AxiosResponse, AxiosProxyConfig } from 'axios';
import qs from 'qs';

class Server {
    public static axios = axios;

    public static post(action: string, data: object | string, callback: Function): void {
        axios.post("/backend/data-transfer.php", qs.stringify({
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
            action: "get-config",
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