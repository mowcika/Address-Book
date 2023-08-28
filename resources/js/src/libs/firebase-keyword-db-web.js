import { ref, child, get, push, update } from 'firebase/database'
import { database } from './firebase-db'

const key_word_db_web_databse = []
const dbref = ref(database)
const key_word_db_web = get(child(dbref, 'key_word_db_web'))
    .then((snapshot) => {
        if (snapshot.exists()) {
            // console.log(snapshot.val());
            snapshot.forEach(function (userSnapshot) {
                if (key_word_db_web_databse.includes(userSnapshot.val().key_word)) {

                } else {
                    key_word_db_web_databse.push(userSnapshot.val().key_word)
                }
            })
            return key_word_db_web_databse.sort()
        } else {
            console.log('No data available')
        }
    })
    .catch((error) => {
        console.error(error)
    })

function writeNewPost(key_word) {

    // A post entry.
    const postData = {
        key_word: key_word,
    }

    // Get a key for a new Post.
    const newPostKey = push(child(dbref, 'key_word_db_web')).key
    console.log('000' + newPostKey)
    // Write the new post's data simultaneously in the posts list and the user's post list.
    const updates = {}
    updates['/key_word_db_web/' + newPostKey] = postData

    return update(dbref, updates)
}

// console.log(sports);

class FirebaseDataService {
    getAll() {
        return key_word_db_web
    }

    create(key_word) {
        return writeNewPost(key_word)
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

export default new FirebaseDataService()
