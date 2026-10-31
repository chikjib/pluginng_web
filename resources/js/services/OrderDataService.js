import { ref, set, get, child, update, remove } from "firebase/database";
import database from "../firebase";
const db = database;

class OrderDataService {
    getOrder(refss) {
        //return db;
        return get(child(ref(db), `orders/${refss}`));
    }
    create(order) {
        return set(ref(db, "orders/" + order.ref), order);

        //return db.push(order);
    }
    update(key, value) {
        return update(ref(db, key), value);
    }
    delete(key) {
        return remove(ref(db, key));
    }
    deleteAll() {
        return remove(ref(db));
    }
}
export default new OrderDataService();
