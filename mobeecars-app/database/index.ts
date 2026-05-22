import * as SQLite from 'expo-sqlite';

let db: SQLite.SQLiteDatabase;
let initialized: boolean = false;

export const getDB = async () => {
  if (!db) {
    db = await SQLite.openDatabaseAsync('app.db');
  }
  return db;
};

export const initDB = async () => {
  if (initialized) return;

  const db = await getDB();

  await db.execAsync(`
    CREATE TABLE IF NOT EXISTS cars (
      id INTEGER PRIMARY KEY NOT NULL,
      brand TEXT,
      model TEXT,
      type TEXT,
      image TEXT,
      local_image TEXT
    );
  `);

  await db.execAsync(`
    CREATE TABLE IF NOT EXISTS preferences (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      car_id INTEGER UNIQUE,
      liked INTEGER,
      synced INTEGER DEFAULT 0,
      synced_at INTEGER,
      created_at INTEGER
    );
  `);
};
