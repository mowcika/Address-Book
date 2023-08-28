import firebase from './firebase-db'

const db = firebase.ref('/key_word_db_web')

class KeywordDbWebDataService {
    getAll() {
        return db
    }

    create(keyword) {
        return db.push(keyword)
    }

    update(key, value) {
        return db.child(key)
            .update(value)
    }

    delete(key) {
        return db.child(key)
            .remove()
    }

    deleteAll() {
        return db.remove()
    }
}

export default new KeywordDbWebDataService()
