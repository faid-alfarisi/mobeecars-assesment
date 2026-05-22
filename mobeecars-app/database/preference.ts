import { getDB } from './index';
import type { UserPreference, MostLikedCar } from './types';

export const getPreferences = async (synced: number | null = null) => {
  const db = await getDB();
  const params: number[] = [];
  const wheres: string[] = [];

  if (synced !== null) {
    wheres.push(`synced = ?`);
    params.push(synced);
  }
  return db.getAllAsync<UserPreference>(
    `SELECT * FROM preferences ${wheres.length ? `WHERE ` + wheres.join(` AND `) : ``}`,
    ...params,
  );
};

export const savePreferences = async (prefs: UserPreference[]) => {
  if (!prefs || prefs.length === 0) return;

  const db = await getDB();

  const prefQueryStart = `
    INSERT INTO preferences (
      car_id,
      liked,
      synced,
      synced_at,
      created_at
    )
    VALUES
  `;
  const prefQueryEnd = `
    ON CONFLICT(car_id)
    DO UPDATE SET
      liked = excluded.liked,
      synced = 0,
      synced_at = NULL
  `;
  let prefValues: string[] = [];
  let prefParams: (string | number | null)[] = [];

  await db.execAsync('BEGIN');

  try {
    for (const pref of prefs) {
      prefValues.push(`(?, ?, 0, NULL, ?)`);
      prefParams.push(
        pref.car_id,
        pref?.liked ? 1 : 0,
        Math.floor(Date.now() / 1000),
      );

      if (prefValues.length >= 100) {
        await db.runAsync(
          prefQueryStart + prefValues.join(',') + prefQueryEnd,
          ...prefParams,
        );
        prefValues = [];
        prefParams = [];
      }
    }

    if (prefValues.length > 0) {
      await db.runAsync(
        prefQueryStart + prefValues.join(',') + prefQueryEnd,
        ...prefParams,
      );
    }

    await db.execAsync('COMMIT');
  } catch (e) {
    await db.execAsync('ROLLBACK');
    throw e;
  }
};

export const getMostLikedCar = async () => {
  const db = await getDB();

  return db.getFirstAsync<MostLikedCar>(`
    SELECT
    (
        SELECT c.brand
        FROM preferences p
        JOIN cars c ON c.id = p.car_id
        WHERE p.liked = 1
        GROUP BY c.brand
        ORDER BY COUNT(*) DESC
        LIMIT 1
    ) AS favorite_brand,

    (
        SELECT c.model
        FROM preferences p
        JOIN cars c ON c.id = p.car_id
        WHERE p.liked = 1
        GROUP BY c.model
        ORDER BY COUNT(*) DESC
        LIMIT 1
    ) AS favorite_model,

    (
        SELECT c.type
        FROM preferences p
        JOIN cars c ON c.id = p.car_id
        WHERE p.liked = 1
        GROUP BY c.type
        ORDER BY COUNT(*) DESC
        LIMIT 1
    ) AS favorite_type  
  `);
};

export const clearPreferences = async () => {
  const db = await getDB();
  await db.execAsync(`
    DELETE FROM preferences;
    UPDATE sqlite_sequence SET seq = 0 WHERE name = 'preferences';
  `);
};
