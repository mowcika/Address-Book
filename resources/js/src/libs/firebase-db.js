import { initializeApp } from 'firebase/app'
import { getDatabase } from 'firebase/database'

let firebaseConfig = {
    databaseURL: 'https://api-project-22073589686.firebaseio.com',
}

const app = initializeApp(firebaseConfig)

const database = getDatabase(app)

export {
    database
}
