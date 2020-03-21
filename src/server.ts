import axios from 'axios';
import qs from 'qs';

class Server {
    public static axios = axios;

    public static post(data: Object, callback: Function): void {
        axios.post("/backend/data-transfer.php", qs.stringify(data), {
            method: "POST"
        }).then(result => {
            callback(result);
        }).catch(reason => {
            console.error(reason);
        })
    }
}

export default Server;