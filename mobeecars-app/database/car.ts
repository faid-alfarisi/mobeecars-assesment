import { getDB } from './index';
import type { Car } from './types';

export const getCars = async () => {
  const db = await getDB();
  return db.getAllAsync<Car>(`SELECT * FROM cars`);
};

export const refreshCars = async (cars: Car[]) => {
  if (!cars || cars.length === 0) return;

  const db = await getDB();

  let carQuery = `
    INSERT INTO cars (
        id,
        brand,
        model,
        type,
        image,
        local_image
    )
    VALUES
  `;
  let carValues: string[] = [];
  let carParams: (string | number | null)[] = [];

  await db.execAsync('BEGIN');

  try {
    await db.execAsync(`DELETE FROM cars`);

    for (const car of cars) {
      carValues.push(`(?, ?, ?, ?, ?, ?)`);
      carParams.push(
        car.id ?? null,
        car.brand ?? null,
        car.model ?? null,
        car.type ?? null,
        car.image ?? null,
        car.local_image ?? null,
      );

      if (carValues.length >= 100) {
        await db.runAsync(carQuery + carValues.join(','), ...carParams);
        carValues = [];
        carParams = [];
      }
    }

    if (carValues.length > 0) {
      await db.runAsync(carQuery + carValues.join(','), ...carParams);
    }

    await db.execAsync('COMMIT');
  } catch (e) {
    await db.execAsync('ROLLBACK');
    throw e;
  }
};

export const clearCars = async () => {
  const db = await getDB();
  await db.execAsync(`DELETE FROM cars`);
};
